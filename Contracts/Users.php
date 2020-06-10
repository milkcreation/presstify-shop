<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop\Contracts;

use tiFy\Contracts\Auth\{Signin, Signup};
use WP_User;

interface Users extends ShopAwareTrait
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
     * @return UserCustomer|UserShopManager|User.
     */
    public function get($user = null);
}