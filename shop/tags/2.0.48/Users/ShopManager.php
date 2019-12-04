<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop\Users;

use tiFy\Plugins\Shop\Contracts\UserShopManager;

class ShopManager extends User implements UserShopManager
{
    /**
     * @inheritDoc
     */
    public function isShopManager(): bool
    {
        return true;
    }
}