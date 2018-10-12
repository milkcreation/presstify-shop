<?php

namespace tiFy\Plugins\Shop;

use tiFy\Plugins\Shop\Contracts\ShopResolverInterface;
use tiFy\Plugins\Shop\Shop;
use tiFy\Plugins\Shop\ShopResolverTrait;

abstract class AbstractShopBinding implements ShopResolverInterface
{
    use ShopResolverTrait;

    /**
     * CONSTRUCTEUR.
     *
     * @param Shop $shop Classe de rappel de la boutique.
     *
     * @return null
     */
    public function __construct(Shop $shop)
    {
        $this->shop = $shop;
    }
}