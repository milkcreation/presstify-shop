<?php

namespace tiFy\Plugins\Shop\Contracts;

use tiFy\Contracts\User\UserQuery;
use tiFy\Contracts\User\SigninFactory;
use WP_User;

interface UsersInterface extends BootableControllerInterface, ShopResolverInterface, UserQuery
{
    /**
     * Récupération de l'object utilisateur courant de Wordpress.
     *
     * @return WP_User|null
     */
    public function current();

    /**
     * Récupération des données d'un utilisateur
     *
     * @param string|int|WP_User|null $user Identifiant pixvert|ID de l'utilisateur WP|Objet User WP
     * |Utilisateur courant WP.
     *
     * @return object|UserCustomerInterface|UserLoggedOutInterface|UserShopManagerInterface|UserItemInterface.
     */
    public function getItem($user = null);

    /**
     * Instance du formulaire d'authentification.
     *
     * @return null|SigninFactory
     */
    public function signin();
}