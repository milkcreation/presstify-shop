<?php

namespace tiFy\Plugins\Shop\Orders;

use tiFy\PostType\Query\PostQueryCollection;
use tiFy\Plugins\Shop\Contracts\OrderInterface;
use tiFy\Plugins\Shop\Contracts\OrderListInterface;
use tiFy\Plugins\Shop\ShopResolverTrait;

/**
 * Class OrderList
 *
 * @desc Controleur de liste des commandes.
 */
class OrderList extends PostQueryCollection implements OrderListInterface
{
    use ShopResolverTrait;

    /**
     * Liste des éléments déclarés.
     * @var OrderInterface[]
     */
    protected $items = [];

    /**
     * CONSTRUCTEUR.
     *
     * @param array|OrderInterface[] $items Liste des éléments déclarés.
     *
     * @return void
     */
    public function __construct($items = [])
    {
        $this->shop = resolve('shop');

        parent::__construct($items);
    }

    /**
     * {@inheritdoc}
     *
     * @param \WP_Post $post
     * @param int $key Clé d'indice de l'élément.
     *
     * @return OrderInterface
     */
    public function wrap($post, $key = null)
    {
        return $this->items[$key] = app('shop.orders.order', [$post, $this->shop]);
    }
}