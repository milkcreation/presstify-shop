<?php

/**
 * @name \tiFy\Plugins\Shop\Orders\OrderItems\OrderItemList
 * @desc Controleur des éléments de commande en base de données.
 *
 * @author Jordy Manner <jordy@tigreblanc.fr>
 * @copyright Milkcreation
 */

namespace tiFy\Plugins\Shop\Orders\OrderItems;

use illuminate\Support\Collection;
use tiFy\Plugins\Shop\Contracts\OrderItemInterface;
use tiFy\Plugins\Shop\Contracts\OrderItemListInterface;
use tiFy\Plugins\Shop\Shop;
use tiFy\Plugins\Shop\ShopResolverTrait;

class OrderItemList extends Collection implements OrderItemListInterface
{
    use ShopResolverTrait;

    /**
     * CONSTRUCTEUR.
     *
     * @param OrderItemInterface[] $items Liste des éléments.
     * @param Shop $shop Instance de la boutique.
     *
     * @return void
     */
    public function __construct($items = [], Shop $shop)
    {
        $this->shop = $shop;

        parent::__construct($items);
    }
}