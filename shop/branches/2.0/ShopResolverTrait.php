<?php

namespace tiFy\Plugins\Shop;

use tiFy\Contracts\App\AppInterface;
use tiFy\Contracts\Container\ContainerInterface;
use tiFy\Plugins\Shop\Contracts\AddressesInterface;
use tiFy\Plugins\Shop\Contracts\AdminInterface;
use tiFy\Plugins\Shop\Contracts\CartInterface;
use tiFy\Plugins\Shop\Contracts\CheckoutInterface;
use tiFy\Plugins\Shop\Contracts\CustomTypesInterface;
use tiFy\Plugins\Shop\Contracts\FunctionsInterface;
use tiFy\Plugins\Shop\Contracts\GatewaysInterface;
use tiFy\Plugins\Shop\Contracts\NoticesInterface;
use tiFy\Plugins\Shop\Contracts\OrdersInterface;
use tiFy\Plugins\Shop\Contracts\ProductsInterface;
use tiFy\Plugins\Shop\Contracts\SessionResolvedInterface;
use tiFy\Plugins\Shop\Contracts\SettingsInterface;
use tiFy\Plugins\Shop\Contracts\UsersInterface;
use tiFy\Plugins\Shop\Shop;

trait ShopResolverTrait
{
    /**
     * Classe de rappel de la boutique.
     * @var Shop
     */
    protected $shop;

    /**
     * @return AddressesInterface
     */
    public function addresses()
    {
        return app('shop.addresses.controller');
    }

    /**
     * @return AppInterface|ContainerInterface
     */
    public function app($abstract = null, $args = [])
    {
        return $this->shop->app($abstract, $args);
    }

    /**
     * @return CartInterface
     */
    public function cart()
    {
        return app('shop.cart.controller');
    }

    /**
     * @return CheckoutInterface
     */
    public function checkout()
    {
        return app('shop.checkout.controller');
    }

    /**
     * @return mixed
     */
    public function config($key = null, $default = '')
    {
        return config($key ? "shop.{$key}" : 'shop', $default);
    }

    /**
     * @return FunctionsInterface
     */
    public function functions()
    {
        return app('shop.functions.controller');
    }

    /**
     * @return GatewaysInterface
     */
    public function gateways()
    {
        return app('shop.gateways.controller');
    }

    /**
     * @return OrdersInterface
     */
    public function orders()
    {
        return app('shop.orders.controller');
    }

    /**
     * @return ProductsInterface
     */
    public function products()
    {
        return app('shop.products.controller');
    }

    /**
     * @return NoticesInterface
     */
    public function notices()
    {
        return app('shop.notices.controller');
    }

    /**
     * @return SessionResolvedInterface
     */
    public function session()
    {
        return app('shop.session.controller');
    }

    /**
     * @return SettingsInterface
     */
    public function settings()
    {
        return app('shop.settings.controller');
    }

    /**
     * @return UsersInterface
     */
    public function users()
    {
        return app('shop.users.controller');
    }
}