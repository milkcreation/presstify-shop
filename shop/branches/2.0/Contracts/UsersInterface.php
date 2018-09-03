<?php

namespace tiFy\Plugins\Shop\Contracts;

use tiFy\Contracts\User\UserQueryInterface;
use tiFy\Plugins\Shop\Contracts\BootableControllerInterface;
use tiFy\Plugins\Shop\Contracts\ShopResolverInterface;

interface UsersInterface extends BootableControllerInterface, ShopResolverInterface, UserQueryInterface
{
    /**
     * Récupération de l'object utilisateur courant de Wordpress.
     *
     * @return null|\WP_User
     */
    public function current();
}