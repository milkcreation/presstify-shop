<?php

namespace tiFy\Plugins\Shop\Users;

class Customer extends AbstractUser implements CustomerInterface
{
    /**
     * Vérifie si un utilisateur est considéré en tant que client
     *
     * @return bool
     */
    public function isCustomer()
    {
        return true;
    }
}