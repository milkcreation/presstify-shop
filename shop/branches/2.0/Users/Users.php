<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop\Users;

use tiFy\Contracts\{Auth\Signin, Auth\Signup};
use tiFy\Plugins\Shop\Contracts\{Users as UsersContract, Shop};
use tiFy\Plugins\Shop\ShopAwareTrait;
use tiFy\Support\Proxy\Auth;
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
     * Instance du formulaire d'authentification.
     * @var Signin|null
     */
    protected $signin;

    /**
     * Instance du formulaire d'inscription.
     * @var Signup|null
     */
    protected $signup;

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

        if ($signin = $this->shop()->config('signin', [])) {
            $this->signin = Auth::registerSignin('shop', $signin);
        }

        if ($signup = $this->shop()->config('signup', [])) {
            $this->signup = Auth::registerSignup('shop', $signup);
        }
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
        $user = $this->shop()->resolve('user', [new WP_User($user ?? get_current_user_id())]);

        if ($user->hasRole('shop_manager')) {
            return $this->shop()->resolve('user.shop-manager', [$user->getWpUser()]);
        } elseif ($user->hasRole('customer')) {
            return $this->shop()->resolve('user.customer', [$user->getWpUser()]);
        } else {
            return $user;
        }
    }

    /**
     * @inheritDoc
     */
    public function signin(): ?Signin
    {
        return $this->signin;
    }

    /**
     * @inheritDoc
     */
    public function signup(): ?Signup
    {
        return $this->signup;
    }
}