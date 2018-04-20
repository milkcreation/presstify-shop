<?php

namespace tiFy\Plugins\Shop\Session;

use tiFy\Plugins\Shop\Shop;

interface SessionInterface
{
    /**
     * Instanciation de la classe
     * @param Shop $shop
     * @return Session
     */
    public static function make(Shop $shop);
}