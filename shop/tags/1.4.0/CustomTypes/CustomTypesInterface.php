<?php

namespace tiFy\Plugins\Shop\CustomTypes;

use tiFy\Plugins\Shop\Shop;

interface CustomTypesInterface
{
    /**
     * Instanciation de la classe
     *
     * @param Shop $shop
     *
     * @return CustomTypes
     */
    public static function make(Shop $shop);
}