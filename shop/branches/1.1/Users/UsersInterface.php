<?php

namespace tiFy\Plugins\Shop\Users;

use tiFy\Plugins\Shop\Shop;

interface UsersInterface
{
    /**
     * Instanciation de la classe
     * @param Shop $shop
     * @return Users
     */
    public static function make(Shop $shop);

    /**
     * Récupération des données d'un ulisateur
     * @param string|int|\WP_User|null $user Identifiant pixvert|ID de l'utilisateur WP|Objet User WP|Utilisateur courant WP
     * @return object|User|Customer|ShopManager|LoggedOut
     */
    public function get($user = null);

    /**
     * Récupération de l'object utilisateur courant de Wordpress
     * @return null|\WP_User
     */
    public function current();
}