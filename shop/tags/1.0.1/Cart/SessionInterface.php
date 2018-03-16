<?php

namespace tiFy\Plugins\Shop\Cart;

interface SessionInterface
{
    /**
     * @return array
     */
    public function getCart();
}