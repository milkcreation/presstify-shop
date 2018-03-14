<?php
namespace tiFy\Plugins\Shop\Factory;

class Users extends \tiFy\Plugins\Shop\Factory
{
    /**
     * Utilisateur courant
     * @var \WP_User
     */
    private static $Current = null;

    /**
     * Récupération de l'object utilisateur courant de Wordpress
     *
     * @return null|\WP_User
     */
    final public function current()
    {
        if (!is_user_logged_in()) :
            return '';
        endif;

        if (!is_null(self::$Current)) :
            return self::$Current;
        endif;

        if (!$user = wp_get_current_user()) :
            return '';
        endif;

        if (!$user instanceof \WP_User) :
            return '';
        endif;

        return self::$Current = $user;
    }

    /**
     * Vérifie si l'utilisateur courant est un client
     *
     * @return false
     */
    final public function isCustomer()
    {
        if(!$user = self::current()) :
            return false;
        endif;

        return reset($user->roles) === 'customer';
    }

    /**
     * Vérifie si l'utilisateur courant est un gestionnaire de boutique
     *
     * @return false
     */
    final public function isShopManager()
    {
        if(!$user = self::current()) :
            return false;
        endif;

        return reset($user->roles) === 'shop_manager';
    }
}