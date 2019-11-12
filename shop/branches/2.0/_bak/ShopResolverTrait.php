<?php

namespace tiFy\Plugins\Shop__Bak;

use tiFy\Contracts\View\ViewController;
use tiFy\Contracts\View\ViewEngine;
use tiFy\Plugins\Shop\{
    Contracts\Actions,
    Contracts\AddressesInterface,
    Contracts\CartInterface,
    Contracts\CheckoutInterface,
    Contracts\FunctionsInterface,
    Contracts\GatewaysInterface,
    Contracts\OrdersInterface,
    Contracts\ProductsInterface,
    Contracts\NoticesInterface,
    Contracts\SessionInterface,
    Contracts\SettingsInterface,
    Contracts\UserCustomerInterface,
    Contracts\UserItemInterface,
    Contracts\UserLoggedOutInterface,
    Contracts\UserShopManagerInterface,
    Contracts\UsersInterface
};

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
        return app('shop.addresses');
    }

    /**
     * {@inheritdoc}
     *
     * @return CartInterface
     */
    public function cart()
    {
        return app('shop.cart');
    }

    /**
     * {@inheritdoc}
     *
     * @return CheckoutInterface
     */
    public function checkout()
    {
        return app('shop.checkout');
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
        return app('shop.functions');
    }

    /**
     * {@inheritdoc}
     *
     * @return GatewaysInterface
     */
    public function gateways()
    {
        return app('shop.gateways');
    }

    /**
     * {@inheritdoc}
     *
     * @return OrdersInterface
     */
    public function orders()
    {
        return app('shop.orders');
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
        return app('shop.products');
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
        return app('shop.session');
    }

    /**
     * {@inheritdoc}
     *
     * @return SettingsInterface
     */
    public function settings()
    {
        return app('shop.settings');
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
     * @inheritDoc
     */
    public function user(int $id = null)
    {
        return $this->users()->getItem($id);
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