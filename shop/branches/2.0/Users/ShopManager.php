<?php

namespace tiFy\Plugins\Shop\Users;

class ShopManager extends UserItem
{
    /**
     * {@inheritdoc}
     */
    public function isShopManager()
    {
        return true;
    }
}