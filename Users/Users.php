<?php

namespace tiFy\Plugins\Shop\Users;

use tiFy\User\Query\UserQuery;
use tiFy\Plugins\Shop\Contracts\UsersInterface;
use tiFy\Plugins\Shop\Shop;
use tiFy\Plugins\Shop\ShopResolverTrait;
use WP_User;

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
    public function __construct(Shop $shop)
    {
        $this->shop = $shop;
    }

    /**
     * @inheritDoc
     */
    public function boot()
    {
        foreach ($this->config('roles', []) as $name => $attrs) {
            user()->role()->register($name, $attrs);
        }

        user()->signin()->register('shop', $this->config('signin', []));
    }

    /**
     * @inheritDoc
     */
    public function current()
    {
        if (!is_user_logged_in()) {
            return null;
        } elseif (!is_null(self::$current)) {
            return self::$current;
        } elseif (!$user = wp_get_current_user()) {
            return null;
        } elseif (!$user instanceof WP_User) {
            return null;
        }

        return self::$current = $user;
    }

    /**
     * @inheritDoc
     */
    public function getItem($user = null)
    {
        if (!$item = parent::getItem($user)) {
            return app('shop.users.logged_out', [new WP_User(0), $this->shop]);
        }

        $roles = $item->getRoles();

        if (in_array('shop_manager', $roles)) {
            return app('shop.users.shop_manager', [$item->getUser(), $this->shop]);
        } elseif (in_array('customer', $roles)) {
            return app('shop.users.customer', [$item->getUser(), $this->shop]);
        } else {
            return $item;
        }
    }

    /**
     * @inheritDoc
     */
    public function signin()
    {
        return user()->signin()->get('shop');
    }
}