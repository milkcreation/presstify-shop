<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop\Products;

use tiFy\Plugins\Shop\Contracts\{Product, ProductsCollection as ProductsCollectionContract, Shop};
use tiFy\Plugins\Shop\ShopAwareTrait;
use tiFy\Support\Collection;

class ProductsCollection extends Collection implements ProductsCollectionContract
{
    use ShopAwareTrait;

    /**
     * Liste des éléments déclarés.
     * @var Product[]|array
     */
    protected $items = [];

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
    public function query(...$args): ProductsCollectionContract
    {
        return $this;
    }

    /**
     * @inheritDoc
     *
     * @return Product
     */
    public function wrap($item, $key = null)
    {
        return $this->items[$key] = $this->shop()->resolve('product', [$item, $this->shop]);
    }
}