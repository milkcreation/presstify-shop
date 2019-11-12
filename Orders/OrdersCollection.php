<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop\Orders;

use tiFy\Plugins\Shop\Contracts\{Order, OrdersCollection as OrdersCollectionContract, Shop};
use tiFy\Plugins\Shop\ShopAwareTrait;
use tiFy\Support\Collection;

class OrdersCollection extends Collection implements OrdersCollectionContract
{
    use ShopAwareTrait;

    /**
     * Liste des éléments déclarés.
     * @var Order[]
     */
    protected $items = [];

    /**
     * Nombre d'élément total trouvés
     * @var int
     */
    protected $total = 0;

    /**
     * CONSTRUCTEUR.
     *
     * @param Shop $shop
     *
     * @return void
     */
    public function __construct(Shop $shop)
    {
        $this->setShop($shop);
    }

    /**
     * @inheritDoc
     */
    public function getTotal(): int
    {
        return $this->total;
    }

    /**
     * @inheritDoc
     */
    public function query(...$args): OrdersCollectionContract
    {
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setTotal($total): OrdersCollectionContract
    {
        $this->total = $total;

        return $this;
    }

    /**
     * {@inheritDoc}
     *
     * @return Order
     */
    public function walk($item, $key = null): Order
    {
        return $this->items[$key] = $this->shop()->resolve('order', [$item, $this->shop]);
    }
}