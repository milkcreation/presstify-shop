<?php

namespace tiFy\Plugins\Shop\Users;

use tiFy\User\Query\UserQuery;
use tiFy\User\Role\Role;
use tiFy\User\SignIn\SignIn;
use tiFy\Plugins\Shop\Contracts\UsersInterface;
use tiFy\Plugins\Shop\Contracts\UserItemInterface;
use tiFy\Plugins\Shop\Shop;
use tiFy\Plugins\Shop\ShopResolverTrait;

class Users extends UserQuery implements UsersInterface
{
    use ShopResolverTrait;

    /**
     * Instance de la classe.
     * @var static
     */
    protected static $instance;

    /**
     * Utilisateur courant.
     * @var \WP_User
     */
    private static $current;

    /**
     * Liste des classes de rappel des roles utilisateurs.
     * @var Role[]
     */
    private static $role = [];

    /**
     * Classe de rappel des l'interface d'authentification.
     * @var SignIn
     */
    private static $signIn;

    /**
     * CONSTRUCTEUR.
     *
     * @param Shop $shop Classe de rappel de la boutique
     *
     * @return void
     */
    protected function __construct(Shop $shop)
    {
        $this->shop = $shop;
    }

    /**
     * Court-circuitage de l'implémentation.
     *
     * @return void
     */
    private function __clone()
    {

    }

    /**
     * Court-circuitage de l'implémentation.
     *
     * @return void
     */
    private function __wakeup()
    {

    }

    /**
     * Instanciation de la classe.
     *
     * @param Shop $shop
     *
     * @return Users
     */
    public static function make($alias, Shop $shop)
    {
        if (self::$instance) :
            return self::$instance;
        endif;

        return self::$instance = new self($shop);
    }

    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        add_action(
            'tify_user_role_register',
            function ($roleController) {
                /** @var Role $roleController */
                if ($roles = $this->config('roles', [])) :
                    foreach ($roles as $name => $attrs) :
                        self::$role[$name] = $roleController->register($name, $attrs);
                    endforeach;
                endif;
            }
        );

        add_action(
            'tify_user_signin_register',
            function ($signInController) {
                /** @var SignIn $signInController */
                self::$signIn = $signInController->register(
                    '_tiFyShop',
                    $this->config('signin', [])
                );
            }
        );
    }

    /**
     * Récupération de l'object utilisateur courant de Wordpress.
     *
     * @return null|\WP_User
     */
    public function current()
    {
        if (!is_user_logged_in()) :
            return null;
        endif;

        if (!is_null(self::$current)) :
            return self::$current;
        endif;

        if (!$user = wp_get_current_user()) :
            return null;
        endif;

        if (!$user instanceof \WP_User) :
            return null;
        endif;

        return self::$current = $user;
    }

    /**
     * Récupération des données d'un ulisateur
     *
     * @param string|int|\WP_User|null $user Identifiant pixvert|ID de l'utilisateur WP|Objet User WP|Utilisateur courant WP.
     *
     * @return object|UserItemInterface|Customer|ShopManager|LoggedOut.
     */
    public function getItem($user = null)
    {
        if (!$item = parent::getItem($user)) :
            return app('shop.users.logged_out');
        endif;

        $roles = $item->getRoles();

        if (in_array('shop_manager', $roles)) :
            return app('shop.users.shop_manager', [$item->getUser(), $this->shop]);
        elseif (in_array('customer', $roles)) :
            return app('shop.users.customer', [$item->getUser(), $this->shop]);
        else :
            return $item;
        endif;
    }
}