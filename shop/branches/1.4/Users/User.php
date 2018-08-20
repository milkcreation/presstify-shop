<?php

namespace tiFy\Plugins\Shop\Users;

class User extends AbstractUser implements UserInterface
{
    /**
     * Vérifie si un utilisateur est considéré en tant que client
     *
     * @return bool
     */
    public function isCustomer()
    {
        return $this->hasRole('customer');
    }

    /**
     * Vérifie si un utilisateur est considéré en tant que client
     *
     * @return bool
     */
    public function isShopManager()
    {
        return $this->hasRole('shop_manager');
    }
}