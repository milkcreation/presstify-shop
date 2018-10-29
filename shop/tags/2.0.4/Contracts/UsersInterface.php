<?php

namespace tiFy\Plugins\Shop\Contracts;

use tiFy\Contracts\User\UserQueryInterface;
use tiFy\Contracts\User\UserSignInItemInterface;
use tiFy\Plugins\Shop\Contracts\BootableControllerInterface;
use tiFy\Plugins\Shop\Contracts\ShopResolverInterface;
use tiFy\Plugins\Shop\Contracts\UserCustomerInterface;
use tiFy\Plugins\Shop\Contracts\UserLoggedOutInterface;
use tiFy\Plugins\Shop\Contracts\UserShopManagerInterface;
use tiFy\Plugins\Shop\Contracts\UserItemInterface;

interface UsersInterface extends BootableControllerInterface, ShopResolverInterface, UserQueryInterface
{
    /**
     * Récupération de l'object utilisateur courant de Wordpress.
     *
     * @return null|\WP_User
     */
    public function current();

    /**
     * Récupération des données d'un utilisateur
     *
     * @param string|int|\WP_User|null $user Identifiant pixvert|ID de l'utilisateur WP|Objet User WP|Utilisateur courant WP.
     *
     * @return object|UserCustomerInterface|UserLoggedOutInterface|UserShopManagerInterface|UserItemInterface.
     */
    public function getItem($user = null);

    /**
     * Instance du formulaire d'authentification.
     *
     * @return null|UserSignInItemInterface
     */
    public function signin();
}