<?php

namespace tiFy\Plugins\Shop\Users;

use tiFy\Core\Query\Controller\AbstractUserQuery;
use tiFy\Core\User\Login\Login;
use \tiFy\Core\User\Role\Role;
use tiFy\Core\User\TakeOver\TakeOver;
use tiFy\Plugins\Shop\Shop;

class Users extends AbstractUserQuery
{
    /**
     * Instance de la classe
     * @var Users
     */
    private static $instance;

    /**
     * Classe de rappel de la boutique
     * @var Shop
     */
    protected $shop;

    /**
     * Liste des classes de rappel des roles utilisateurs
     * @var Role[]
     */
    private static $Role = [];

    /**
     * Classe de rappel des l'interface d'authentification
     * @var Login
     */
    private static $Login = null;

    /**
     * Liste des classes de rappel des permissions de prise de contrôle de comptes utilisateurs
     * @var TakeOver[]
     */
    private static $TakeOver = [];

    /**
     * Utilisateur courant
     * @var \WP_User
     */
    private static $Current = null;

    /**
     * CONSTRUCTEUR
     *
     * @param Shop $shop Classe de rappel de la boutique
     *
     * @return void
     */
    protected function __construct(Shop $shop)
    {
        // Définition de la classe de rappel de la boutique
        $this->shop = $shop;

        // Déclaration des événements
        $this->appAddAction('tify_user_role_register');
        $this->appAddAction('tify_user_login_register');
        $this->appAddAction('tify_user_take_over_register');
    }

    /**
     * Court-circuitage de l'implémentation
     *
     * @return void
     */
    private function __clone()
    {

    }

    /**
     * Court-circuitage de l'implémentation
     *
     * @return void
     */
    private function __wakeup()
    {

    }

    /**
     * Instanciation de la classe
     *
     * @param Shop $shop
     *
     * @return Users
     */
    public static function make(Shop $shop)
    {
        if (self::$instance) :
            return self::$instance;
        endif;

        return self::$instance = new self($shop);
    }

    /**
     * Récupération des données d'un ulisateur
     *
     * @param string|int|\WP_User|null $user Identifiant pixvert|ID de l'utilisateur WP|Objet User WP|Utilisateur courant WP
     *
     * @return object|User|Customer|ShopManager|LoggedOut
     */
    public function get($user = null)
    {
        if (!$item = parent::get($user)) :
            return new LoggedOut;
        endif;

        if ($item->isCustomer()) :
            $controller = $this->shop->appConfig('roles.customer.controller', '') ? : 'tiFy\Plugins\Shop\Users\Customer';
            return new $controller($item->getUser());
        elseif ($item->isShopManager()) :
            $controller = $this->shop->appConfig('roles.shop_manager.controller', '') ? : 'tiFy\Plugins\Shop\Users\Customer';
            return new $controller($item->getUser());
        else :
            return $item;
        endif;
    }

    /**
     * Déclaration des roles
     *
     * @return void
     */
    public function tify_user_role_register()
    {
        if ($roles = $this->shop->appConfig('roles', [])) :
            foreach ($roles as $role => $attrs) :
                self::$Role[$role] = Role::register($role, $attrs);
            endforeach;
        endif;
    }

    /**
     * Déclaration de l'interface d'authentification
     *
     * @return void
     */
    public function tify_user_login_register()
    {
        self::$Login = Login::register(
            '_tiFyShop',
            $this->shop->appConfig('login', [])
        );
    }

    /**
     * Déclaration des permissions de prise de contrôle de comptes utilisateurs
     *
     * @return void
     */
    public function tify_user_take_over_register()
    {
        if ($take_over = self::tFyAppConfig('take_over', [], 'tiFy\Plugins\Shop\Shop')) :
            foreach ($take_over as $id => $attrs) :
                self::$TakeOver[$id] = TakeOver::register($id, $attrs);
            endforeach;
        endif;
    }

    /**
     * Récupération de l'object utilisateur courant de Wordpress
     *
     * @return null|\WP_User
     */
    final public function current()
    {
        if (!is_user_logged_in()) :
            return null;
        endif;

        if (!is_null(self::$Current)) :
            return self::$Current;
        endif;

        if (!$user = wp_get_current_user()) :
            return null;
        endif;

        if (!$user instanceof \WP_User) :
            return null;
        endif;

        return self::$Current = $user;
    }
}