<?php

namespace tiFy\Plugins\Shop\Orders;

use tiFy\Plugins\Shop\Shop;

interface OrdersInterface
{
    /**
     * Instanciation de la classe
     * @param Shop $shop
     * @return Orders
     */
    public static function boot(Shop $shop);
}