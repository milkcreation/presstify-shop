<?php

namespace tiFy\Plugins\Shop\Users;

use tiFy\User\Query\UserQuery;
use tiFy\Plugins\Shop\Contracts\UsersInterface;
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
     * @param string $alias
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
        foreach ($this->config('roles', []) as $name => $attrs) :
            user()->role()->register($name, $attrs);
        endforeach;

        user()->signin()->register('shop', $this->config('signin', []));
    }

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function getItem($user = null)
    {
        if (!$item = parent::getItem($user)) :
            return app('shop.users.logged_out', [new \WP_User(0), $this->shop]);
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

    /**
     * {@inheritdoc}
     */
    public function signin()
    {
        return user()->signin()->get('shop');
    }
}