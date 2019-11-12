<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop\Users;

use tiFy\Plugins\Shop\Contracts\{Users as UsersContract, Shop};
use tiFy\Plugins\Shop\ShopAwareTrait;
use WP_User;

class Users implements UsersContract
{
    use ShopAwareTrait;

    /**
     * Utilisateur courant.
     * @var WP_User
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
        $this->setShop($shop);

        foreach ($this->shop()->config('roles', []) as $name => $attrs) {
            user()->role()->register($name, $attrs);
        }

        user()->signin()->register('shop', $this->shop()->config('signin', []));
    }

    /**
     * @inheritDoc
     */
    public function boot(): void { }

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
    public function get($user = null)
    {
        /** @var User $user */
        $user = $this->shop()->resolve('user', [new WP_User($user ?? 0)]);

        if ($user->hasRole('shop_manager')) {
            return $this->shop()->resolve('user.manager', [$user->getWpUser()]);
        } elseif ($user->hasRole('customer')) {
            return $this->shop()->resolve('user.customer', [$user->getWpUser()]);
        } else {
            return $user;
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