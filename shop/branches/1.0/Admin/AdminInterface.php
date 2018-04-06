<?php

namespace tiFy\Plugins\Shop\Admin;

use tiFy\Plugins\Shop\Shop;

interface AdminInterface
{
    /**
     * Instanciation de la classe
     * @param Shop $shop
     * @return Admin
     */
    public static function make(Shop $shop);
}