<?php

namespace tiFy\Plugins\Shop\Users;

use LogicException;
use tiFy\Query\Controller\AbstractUserQuery;
use tiFy\User\Role\Role;
use tiFy\User\SignIn\SignIn;
use tiFy\User\TakeOver\TakeOver;
use tiFy\Plugins\Shop\ServiceProvider\ProvideTraits;
use tiFy\Plugins\Shop\ServiceProvider\ProvideTraitsInterface;
use tiFy\Plugins\Shop\Shop;

class Users extends AbstractUserQuery implements UsersInterface, ProvideTraitsInterface
{
    use ProvideTraits;

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
     * @var SignIn
     */
    private static $SignIn = null;

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
        $this->appAddAction('tify_user_signin_register');
        $this->appAddAction('tify_user_take_over_register');
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

        $roles = $item->getRoles();

        if (in_array('shop_manager', $roles)) :
            $user = $this->provider()->get('users.shop_manager', [$item->getUser(), $this->shop]);

            if(! $user instanceof ShopManagerInterface) :
                throw new LogicException(
                    sprintf(
                        __('Le controleur de surcharge devrait être une instance de %s', 'tify'),
                        ShopManagerInterface::class
                    ),
                    500
                );
            endif;

            return $user;
        elseif (in_array('customer', $roles)) :
            $user = $this->provider()->get('users.customer', [$item->getUser(), $this->shop]);

            if(! $user instanceof CustomerInterface) :
                throw new LogicException(
                    sprintf(
                        __('Le controleur de surcharge devrait être une instance de %s', 'tify'),
                        CustomerInterface::class
                    ),
                    500
                );
            endif;

            return $user;
        else :
            return $item;
        endif;
    }

    /**
     * Déclaration des roles.
     *
     * @param Role $roleController Classe de rappel du controleur de gestion des rôles.
     *
     * @return void
     */
    public function tify_user_role_register($roleController)
    {
        if ($roles = $this->config('roles', [])) :
            foreach ($roles as $name => $attrs) :
                self::$Role[$name] = $roleController->register($name, $attrs);
            endforeach;
        endif;
    }

    /**
     * Déclaration de l'interface d'authentification
     *
     * @param SignIn $signInController Classe de rappel du controleur d'interfaces de connexion.
     *
     * @return void
     */
    public function tify_user_signin_register($signinController)
    {
        self::$SignIn = $signinController->register(
            '_tiFyShop',
            $this->config('signin', [])
        );
    }

    /**
     * Déclaration des permissions de prise de contrôle de comptes utilisateurs
     *
     * @param TakeOver $takeOverController Classe de rappel du controleur de prise de controle.
     *
     * @return void
     */
    public function tify_user_take_over_register($takeOverController)
    {
        if ($take_over = $this->config('take_over', [])) :
            foreach ($take_over as $name => $attrs) :
                self::$TakeOver[$name] = $takeOverController->register($name, $attrs);
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