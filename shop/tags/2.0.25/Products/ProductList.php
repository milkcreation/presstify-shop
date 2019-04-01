<?php

namespace tiFy\Plugins\Shop\Products;

use tiFy\PostType\Query\PostQueryCollection;
use tiFy\Plugins\Shop\Contracts\ProductItemInterface;
use tiFy\Plugins\Shop\Contracts\ProductListInterface;
use tiFy\Plugins\Shop\Shop;
use tiFy\Plugins\Shop\ShopResolverTrait;

/**
 * Class ProductList
 *
 * @desc Controleur de récupération des données d'un produit.
 */
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
    public function __construct($items = [], Shop $shop)
    {
        $this->shop = $shop;

        parent::__construct($items);
    }

    /**
     * {@inheritdoc}
     *
     * @return ProductItemInterface
     */
    public function wrap($post, $key = null)
    {
        return $this->items[$key] = app('shop.products.item', [$post, $this->shop]);
    }
}