<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop;

use tiFy\Plugins\Shop\Contracts\{ShopAwareTrait as ShopAwareTraitContract, Shop};

/**
 * @mixin ShopAwareTraitContract
 */
trait ShopAwareTrait
{
    /**
     * Instance de la boutique.
     * @var Shop|null
     */
    protected $shop;

    /**
     * @inheritDoc
     */
    public function shop(): ?Shop
    {
        return $this->shop;
    }

    /**
     * @inheritDoc
     */
    public function setShop(Shop $shop): ShopAwareTraitContract
    {
        $this->shop = $shop;

        return $this;
    }
}