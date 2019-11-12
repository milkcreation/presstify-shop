<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop\Orders;

use tiFy\Plugins\Shop\Contracts\{OrderItem as OrderItemContract, Shop};
use tiFy\Plugins\Shop\ShopAwareTrait;
use tiFy\Support\ParamsBag;

class OrderItem extends ParamsBag implements OrderItemContract
{
    use ShopAwareTrait;

    /**
     * Liste des attributs
     * @var array $attributes {
     *      @var int $order_item_id
     *      @var string $order_item_name
     *      @var string $order_item_type
     *      @var string $order_id
     * }
     */
    protected $attributes = [];

    /**
     * CONSTRUCTEUR.
     *
     * @param Shop $shop.
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
    public function getId(): int
    {
        return (int)$this->get('order_item_id', 0);
    }

    /**
     * @inheritDoc
     */
    public function getMeta($meta_key, $single = true)
    {
        return $this->shop()->orders()->getDb()->meta()->get($this->getId(), $meta_key, $single);
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return (string)$this->get('order_item_name', '');
    }

    /**
     * @inheritDoc
     */
    public function getOrderId(): int
    {
        return (int)$this->get('order_id', 0);
    }

    /**
     * @inheritDoc
     */
    public function getType(): string
    {
        return (string)$this->get('order_item_type', '');
    }
}