<?php

namespace tiFy\Plugins\Shop\Users;

use tiFy\Plugins\Shop\Contracts\UserShopManagerInterface;

class ShopManager extends UserItem implements UserShopManagerInterface
{
    /**
     * {@inheritdoc}
     */
    public function isShopManager()
    {
        return true;
    }
}