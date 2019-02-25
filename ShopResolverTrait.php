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
        return ($actions = resolve('shop.actions'))
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
        return resolve('shop.addresses.controller');
    }

    /**
     * {@inheritdoc}
     *
     * @return CartInterface
     */
    public function cart()
    {
        return resolve('shop.cart.controller');
    }

    /**
     * {@inheritdoc}
     *
     * @return CheckoutInterface
     */
    public function checkout()
    {
        return resolve('shop.checkout.controller');
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
        return resolve('shop.functions.controller');
    }

    /**
     * {@inheritdoc}
     *
     * @return GatewaysInterface
     */
    public function gateways()
    {
        return resolve('shop.gateways.controller');
    }

    /**
     * {@inheritdoc}
     *
     * @return OrdersInterface
     */
    public function orders()
    {
        return resolve('shop.orders.controller');
    }

    /**
     * {@inheritdoc}
     *
     * @return ShopServiceProvider
     */
    public function provider()
    {
        return resolve(ShopServiceProvider::class);
    }

    /**
     * {@inheritdoc}
     *
     * @return ProductsInterface
     */
    public function products()
    {
        return resolve('shop.products.controller');
    }

    /**
     * {@inheritdoc}
     *
     * @return NoticesInterface
     */
    public function notices()
    {
        return resolve('shop.notices.controller');
    }

    /**
     * {@inheritdoc}
     *
     * @return SessionInterface
     */
    public function session()
    {
        return resolve('shop.session.controller');
    }

    /**
     * {@inheritdoc}
     *
     * @return SettingsInterface
     */
    public function settings()
    {
        return resolve('shop.settings.controller');
    }

    /**
     * {@inheritdoc}
     *
     * @return Shop
     */
    public function shop()
    {
        return resolve('shop');
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
        return resolve('shop.users.controller');
    }

    /**
     * {@inheritdoc}
     *
     * @return ViewController|ViewEngine
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