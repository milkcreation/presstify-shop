<?php

/**
 * @name OrderList
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
     * CONSTRUCTEUR.
     *
     * @param null|OrderInterface[] $items
     */
    public function __construct($items = [])
    {
        $this->shop = resolve('shop');

        parent::__construct($items);
    }
}