<?php

namespace tiFy\Plugins\Shop;

use tiFy\Contracts\App\AppInterface;
use tiFy\Contracts\Container\ContainerInterface;
use tiFy\Contracts\View\ViewEngine;
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
        return resolve('shop.addresses.controller');
    }

    /**
     * @return CartInterface
     */
    public function cart()
    {
        return resolve('shop.cart.controller');
    }

    /**
     * @return CheckoutInterface
     */
    public function checkout()
    {
        return resolve('shop.checkout.controller');
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
        return resolve('shop.functions.controller');
    }

    /**
     * @return GatewaysInterface
     */
    public function gateways()
    {
        return resolve('shop.gateways.controller');
    }

    /**
     * @return OrdersInterface
     */
    public function orders()
    {
        return resolve('shop.orders.controller');
    }

    /**
     * @return \tiFy\Plugins\Shop\ShopServiceProvider
     */
    public function provider()
    {
        return resolve(\tiFy\Plugins\Shop\ShopServiceProvider::class);
    }

    /**
     * @return ProductsInterface
     */
    public function products()
    {
        return resolve('shop.products.controller');
    }

    /**
     * @return NoticesInterface
     */
    public function notices()
    {
        return resolve('shop.notices.controller');
    }

    /**
     * @return SessionResolvedInterface
     */
    public function session()
    {
        return resolve('shop.session.controller');
    }

    /**
     * @return SettingsInterface
     */
    public function settings()
    {
        return resolve('shop.settings.controller');
    }

    /**
     * @return Shop
     */
    public function shop()
    {
        return resolve('shop');
    }

    /**
     * @return string
     */
    public function resourcesDir($path = '')
    {
        return $this->shop()->resourcesDir($path);
    }

    /**
     * @return string
     */
    public function resourcesUrl($path = '')
    {
        return $this->shop()->resourcesUrl($path);
    }

    /**
     * @return UsersInterface
     */
    public function users()
    {
        return resolve('shop.users.controller');
    }

    /**
     * {@inheritdoc}
     */
    public function viewer($view = null, $data = [])
    {
        /** @var ViewEngine $viewer */
        $viewer = resolve('shop.viewer');

        if (func_num_args() === 0) :
            return $viewer;
        endif;

        return $viewer->make("_override::{$view}", $data);
    }
}