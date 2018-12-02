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
     * Liste des éléments déclarés.
     * @var ProductItemInterface[]
     */
    protected $items = [];

    /**
     * CONSTRUCTEUR.
     *
     * @param array|ProductItemInterface[] $items Liste des éléments déclarés.
     *
     * @return void
     */
    public function __construct($items = [])
    {
        $this->shop = app('shop');

        parent::__construct($items);
    }
}