<?php

namespace tiFy\Plugins\Shop\Products;

use tiFy\Core\Query\Controller\QueryInterface;
use tiFy\Plugins\Shop\Shop;

interface ProductsInterface extends QueryInterface
{
    /**
     * Instanciation de la classe
     * @param Shop $shop
     * @return Products
     */
    public static function boot(Shop $shop);
}