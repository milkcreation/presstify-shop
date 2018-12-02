<?php

/**
 * @name \tiFy\Plugins\Shop\Orders\OrderList
 * @desc Controleur de liste des commandes.
 *
 * @author Jordy Manner <jordy@tigreblanc.fr>
 * @copyright Milkcreation
 */

namespace tiFy\Plugins\Shop\Orders;

use tiFy\PostType\Query\PostQueryCollection;
use tiFy\Plugins\Shop\Contracts\OrderInterface;
use tiFy\Plugins\Shop\Contracts\OrderListInterface;
use tiFy\Plugins\Shop\Shop;
use tiFy\Plugins\Shop\ShopResolverTrait;

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
}