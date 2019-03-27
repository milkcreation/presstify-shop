<?php

namespace tiFy\Plugins\Shop;

use tiFy\Contracts\View\ViewController;
use tiFy\Contracts\View\ViewEngine;
use tiFy\Plugins\Shop\Contracts\Actions;
use tiFy\Plugins\Shop\Contracts\AddressesInterface;
use tiFy\Plugins\Shop\Contracts\CartInterface;
use tiFy\Plugins\Shop\Contracts\CheckoutInterface;
use tiFy\Plugins\Shop\Contracts\FunctionsInterface;
use tiFy\Plugins\Shop\Contracts\GatewaysInterface;
use tiFy\Plugins\Shop\Contracts\OrdersInterface;
use tiFy\Plugins\Shop\Contracts\ProductsInterface;
use tiFy\Plugins\Shop\Contracts\NoticesInterface;
use tiFy\Plugins\Shop\Contracts\SessionInterface;
use tiFy\Plugins\Shop\Contracts\SettingsInterface;
use tiFy\Plugins\Shop\Contracts\UsersInterface;

trait ShopResolverTrait
{
    /**
     * Classe de rappel de la boutique.
     * @var Shop
     */
    protected $shop;

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function action($alias, $parameters = [], $absolute = false)
    {
        /** @var Actions $actions */
        return ($actions = app('shop.actions'))
            ? $actions->url($alias, $parameters, $absolute)
            : '';
    }

    /**
     * {@inheritdoc}
     *
     * @return AddressesInterface
     */
    public function addresses()
    {
        return app('shop.addresses.controller');
    }

    /**
     * {@inheritdoc}
     *
     * @return CartInterface
     */
    public function cart()
    {
        return app('shop.cart.controller');
    }

    /**
     * {@inheritdoc}
     *
     * @return CheckoutInterface
     */
    public function checkout()
    {
        return app('shop.checkout.controller');
    }

    /**
     * {@inheritdoc}
     */
    public function config($key = null, $default = '')
    {
        return config($key ? "shop.{$key}" : 'shop', $default);
    }

    /**
     * {@inheritdoc}
     *
     * @return FunctionsInterface
     */
    public function functions()
    {
        return app('shop.functions.controller');
    }

    /**
     * {@inheritdoc}
     *
     * @return GatewaysInterface
     */
    public function gateways()
    {
        return app('shop.gateways.controller');
    }

    /**
     * {@inheritdoc}
     *
     * @return OrdersInterface
     */
    public function orders()
    {
        return app('shop.orders.controller');
    }

    /**
     * {@inheritdoc}
     *
     * @return ShopServiceProvider
     */
    public function provider()
    {
        return app(ShopServiceProvider::class);
    }

    /**
     * {@inheritdoc}
     *
     * @return ProductsInterface
     */
    public function products()
    {
        return app('shop.products.controller');
    }

    /**
     * {@inheritdoc}
     *
     * @return NoticesInterface
     */
    public function notices()
    {
        return app('shop.notices.controller');
    }

    /**
     * {@inheritdoc}
     *
     * @return SessionInterface
     */
    public function session()
    {
        return app('shop.session.controller');
    }

    /**
     * {@inheritdoc}
     *
     * @return SettingsInterface
     */
    public function settings()
    {
        return app('shop.settings.controller');
    }

    /**
     * {@inheritdoc}
     *
     * @return Shop
     */
    public function shop()
    {
        return app('shop');
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function resourcesDir($path = '')
    {
        return $this->shop()->resourcesDir($path);
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function resourcesUrl($path = '')
    {
        return $this->shop()->resourcesUrl($path);
    }

    /**
     * {@inheritdoc}
     *
     * @return UsersInterface
     */
    public function users()
    {
        return app('shop.users.controller');
    }

    /**
     * {@inheritdoc}
     *
     * @return ViewController|ViewEngine
     */
    public function viewer($view = null, $data = [])
    {
        /** @var ViewEngine $viewer */
        $viewer = app('shop.viewer');

        if (func_num_args() === 0) :
            return $viewer;
        endif;

        return $viewer->make("_override::{$view}", $data);
    }
}