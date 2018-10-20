<?php

/**
 * @name ProductList
 * @desc Controleur de récupération des données d'un produit.
 *
 * @author Jordy Manner <jordy@tigreblanc.fr>
 * @copyright Milkcreation
 */

namespace tiFy\Plugins\Shop\Products;

use tiFy\PostType\Query\PostQueryCollection;
use tiFy\Plugins\Shop\Contracts\ProductItemInterface;
use tiFy\Plugins\Shop\Contracts\ProductListInterface;
use tiFy\Plugins\Shop\Shop;
use tiFy\Plugins\Shop\ShopResolverTrait;

class ProductList extends PostQueryCollection implements ProductListInterface
{
    use ShopResolverTrait;

    /**
     * CONSTRUCTEUR.
     *
     * @param null|OrderInterface[] $items
     */
    public function __construct($items = [])
    {
        $this->shop = app('shop');

        parent::__construct($items);
    }
}