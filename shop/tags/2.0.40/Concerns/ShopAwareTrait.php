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
     * Instance de la boutique.
     * @var Shop|null
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