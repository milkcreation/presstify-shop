<?php

namespace tiFy\Plugins\Shop\Users;

class ShopManager extends AbstractUser implements ShopManagerInterface
{
    /**
     * Vérifie si un utilisateur est considéré en tant que client
     *
     * @return bool
     */
    public function isShopManager()
    {
        return true;
    }
}