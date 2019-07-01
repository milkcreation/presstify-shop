<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop\Concerns;

use tiFy\Plugins\Shop\Contracts\ShopInterface as Shop;
use tiFy\Plugins\Shop\Contracts\ShopAwareTrait as ShopAwareTraitContract;

/**
 * @mixin ShopAwareTraitContract
 */
trait ShopAwareTrait
{
    /**
     * Instance du gestionnaire de boutique.
     * @var Shop
     */
    protected $shop;

    /**
     * @inheritDoc
     */
    public function setShop(Shop $shop): ShopAwareTraitContract
    {
        $this->shop = $shop;

        return $this;
    }
}