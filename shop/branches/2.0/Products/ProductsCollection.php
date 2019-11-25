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
    public function featured(): array
    {
        return $this->collect($this->items)->filter(function (Product $item) {
            return $item->isFeatured();
        })->values()->all();
    }

    /**
     * {@inheritDoc}
     *
     * @param Product $item
     *
     * @return Product
     */
    public function walk($item, $key = null): ?Product
    {
        if ($item instanceof Product) {
            return $this->items[$key] = $item;
        } else {
            return null;
        }
    }
}