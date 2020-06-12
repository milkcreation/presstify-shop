<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop\Orders;

use tiFy\Contracts\PostType\PostTypeStatus;
use tiFy\Plugins\Shop\Contracts\{Order, Orders as OrdersContract, OrdersCollection};
use tiFy\Plugins\Shop\ShopAwareTrait;

class Orders implements OrdersContract
{
    use ShopAwareTrait;

    /**
     * Liste des statuts de commande.
     * @var array|null
     */
    protected $statuses;

    /**
     * Nombre d'élément total trouvés
     * @var int
     */
    protected $total = 0;

    /**
     * @inheritDoc
     */
    public function collect(array $orders = []): OrdersCollection
    {
        return $this->shop()->resolve('orders.collection')->set($orders);
    }

    /**
     * @inheritDoc
     */
    public function create(): ?Order
    {
        if (! $id = wp_insert_post(['post_type' => 'shop_order'])) {
            return null;
        }

        return $this->get($id);
    }

    /**
     * @inheritDoc
     */
    public function get($id = null): ?Order
    {
        if (!$id) {
            $id = $this->shop()->session()->get('order_awaiting_payment', 0);
        }

        return $this->shop()->resolve('order', [$id]);
    }

    /**
     * @inheritDoc
     */
    public function getDefaultStatus(): string
    {
        return 'order-pending';
    }

    /**
     * @inheritDoc
     */
    public function getNeedPaymentStatuses(): array
    {
        return ['order-failed', 'order-pending'];
    }

    /**
     * @inheritDoc
     */
    public function getNotEmptyCartStatuses(): array
    {
        return ['order-cancelled', 'order-failed', 'order-pending'];
    }

    /**
     * @inheritDoc
     */
    public function getPaymentCompleteStatuses(): array
    {
        return ['order-completed', 'order-processing'];
    }

    /**
     * @inheritDoc
     */
    public function getPaymentValidStatuses(): array
    {
        return ['order-failed', 'order-cancelled', 'order-on-hold', 'order-pending'];
    }

    /**
     * @inheritDoc
     */
    public function getRegisteredStatuses(): array
    {
        return array_keys($this->getStatuses());
    }

    /**
     * @inheritDoc
     */
    public function getStatuses(): array
    {
        if (is_null($this->statuses)) {
            $statuses = $this->shop()->entity()->getOrderStatuses();

            array_walk($statuses, function (PostTypeStatus $item) {
                $this->statuses[$item->getName()] = $item->getLabel();
            });
        }

        return $this->statuses ? : [];
    }

    /**
     * @inheritDoc
     */
    public function getStatusLabel($name, $default = ''): string
    {
        $statuses = $this->getStatuses();

        return $statuses[$name] ?? $default;
    }

    /**
     * Récupération du nombre d'enregistrement total.
     *
     * @return int
     */
    public function getTotal(): int
    {
        return $this->total;
    }

    /**
     * @inheritDoc
     */
    public function hasStatus(string $status): bool
    {
        return in_array($status, array_keys($this->getStatuses()));
    }

    /**
     * Définition du nombre d'enregistrement total.
     *
     * @param int $total
     *
     * @return $this
     */
    public function setTotal($total): self
    {
        $this->total = $total;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function is($order): bool
    {
        return $order instanceof Order;
    }

    /**
     * @inheritDoc
     */
    public function query(array $args = []): array
    {
        if (!isset($args['post_status'])) {
            $args['post_status'] = $this->getRegisteredStatuses();
        }

        $order = $this->shop()->order();

        return $order::fetchFromArgs($args);
    }
}