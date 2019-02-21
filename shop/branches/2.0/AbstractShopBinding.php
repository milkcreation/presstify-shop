<?php

namespace tiFy\Plugins\Shop;

use tiFy\Plugins\Shop\Contracts\ShopResolverInterface;

abstract class AbstractShopBinding implements ShopResolverInterface
{
    use ShopResolverTrait;

    /**
     * CONSTRUCTEUR.
     *
     * @param Shop $shop Classe de rappel de la boutique.
     *
     * @return void
     */
    public function __construct(Shop $shop)
    {
        $this->shop = $shop;
    }
}