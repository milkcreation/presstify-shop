<?php

/**
 * @name \tiFy\Plugins\Shop\Orders\OrderItems\OrderItem
 * @desc Controleur d'un élément de commande en base de données.
 *
 * @author Jordy Manner <jordy@tigreblanc.fr>
 * @copyright Milkcreation
 */

namespace tiFy\Plugins\Shop\Orders\OrderItems;

use tiFy\Kernel\Params\ParamsBag;
use tiFy\Plugins\Shop\Contracts\OrderItemInterface;
use tiFy\Plugins\Shop\Shop;
use tiFy\Plugins\Shop\ShopResolverTrait;

class OrderItem extends ParamsBag implements OrderItemInterface
{
    use ShopResolverTrait;

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
     * @param array $attrs Liste des attributs de l'élément.
     * @param Shop $shop Instance de la boutique.
     *
     * @return void
     */
    public function __construct($attrs = [], Shop $shop)
    {
        $this->shop = $shop;

        parent::__construct($attrs);
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return (int)$this->get('order_item_id', 0);
    }

    /**
     * {@inheritdoc}
     */
    public function getMeta($meta_key, $single = true)
    {
        return $this->orders()->getDb()->meta()->get($this->getId(), $meta_key, $single);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return (string)$this->get('order_item_name', '');
    }

    /**
     * {@inheritdoc}
     */
    public function getOrderId()
    {
        return (int)$this->get('order_id', 0);
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return (string)$this->get('order_item_type', '');
    }
}