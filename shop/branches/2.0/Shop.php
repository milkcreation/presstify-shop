<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop;

use Psr\Container\ContainerInterface as Container;
use tiFy\Contracts\View\ViewController;
use tiFy\Contracts\View\ViewEngine;
use tiFy\Plugins\Shop\Contracts\{
    Actions,AddressesInterface as Addresses,CartInterface as Cart,CheckoutInterface as Checkout, FunctionsInterface as Functions, GatewaysInterface as Gateways, NoticesInterface as Notices, OrdersInterface as Orders, ProductsInterface as Products, SessionInterface as Session, SettingsInterface as Settings, ShopInterface as ShopContract, UsersInterface as Users};

/**
 * @desc Extension PresstiFy de gestion de boutique en ligne.
 * @author Jordy Manner <jordy@milkcreation.fr>
 * @package tiFy\Plugins\Shop
 * @version 2.0.45
 *
 * Activation :
 * ----------------------------------------------------------------------------------------------------
 * Dans config/app.php ajouter \tiFy\Plugins\Shop\ShopServiceProvider à la liste des fournisseurs de services
 *     chargés automatiquement par l'application.
 * ex.
 * <?php
 * ...
 * use tiFy\Plugins\Shop\ShopServiceProvider;
 * ...
 *
 * return [
 *      ...
 *      'providers' => [
 *          ...
 *          ShopServiceProvider::class
 *          ...
 *      ]
 * ];
 *
 * Configuration :
 * ----------------------------------------------------------------------------------------------------
 * Dans le dossier de config, créer le fichier shop.php
 * @see /vendor/presstify-plugins/shop/Resources/config/shop.php Exemple de configuration
 */
class Shop implements ShopContract
{
    /**
     * Conteneur d'injection de dépendances.
     * @var Container
     */
    protected $container;

    /**
     * CONSTRUCTEUR.
     *
     * @param Container $container Conteneur d'injection de dépendances.
     *
     * @return void
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * @inheritDoc
     */
    public function action($alias, $parameters = [], $absolute = false): string
    {
        /** @var Actions $actions */
        return ($actions = app('shop.actions'))
            ? $actions->url($alias, $parameters, $absolute)
            : '';
    }

    /**
     * @inheritDoc
     */
    public function addresses(): Addresses
    {
        return $this->resolve('addresses');
    }

    /**
     * @inheritDoc
     */
    public function cart(): Cart
    {
        return $this->resolve('cart');
    }

    /**
     * @inheritDoc
     */
    public function checkout(): Checkout
    {
        return $this->resolve('checkout.controller');
    }

    /**
     * @inheritDoc
     */
    public function config($key = null, $default = '')
    {
        return config($key ? "shop.{$key}" : 'shop', $default);
    }

    /**
     * @inheritDoc
     */
    public function functions(): Functions
    {
        return $this->resolve('functions.controller');
    }

    /**
     * @inheritDoc
     */
    public function gateways(): Gateways
    {
        return $this->resolve('gateways');
    }

    /**
     * @inheritDoc
     */
    public function getContainer(): Container
    {
        return $this->container;
    }

    /**
     * @inheritDoc
     */
    public function orders(): Orders
    {
        return $this->resolve('orders.controller');
    }

    /**
     * @inheritDoc
     */
    public function provider()
    {
        return app(ShopServiceProvider::class);
    }

    /**
     * @inheritDoc
     */
    public function products(): Products
    {
        return $this->resolve('products.controller');
    }

    /**
     * @inheritDoc
     */
    public function notices(): Notices
    {
        return $this->resolve('notices.controller');
    }

    /**
     * @inheritDoc
     */
    public function resolve($alias, ...$args)
    {
        return app("shop.{$alias}", $args);
    }

    /**
     * @inheritDoc
     */
    public function resourcesDir($path = ''): string
    {
        $path = '/Resources/' . ltrim($path, '/');

        return file_exists(__DIR__ . $path) ? __DIR__ . $path : '';
    }

    /**
     * @inheritDoc
     */
    public function resourcesUrl($path = ''): string
    {
        $cinfo = class_info($this);
        $path = '/Resources/' . ltrim($path, '/');

        return file_exists($cinfo->getDirname() . $path) ? class_info($this)->getUrl() . $path : '';
    }

    /**
     * @inheritDoc
     */
    public function session(): Session
    {
        return $this->resolve('session.controller');
    }

    /**
     * @inheritDoc
     */
    public function settings(): Settings
    {
        return $this->resolve('settings.controller');
    }

    /**
     * @inheritDoc
     */
    public function user(int $id = null)
    {
        return $this->users()->getItem($id);
    }

    /**
     * @inheritDoc
     */
    public function users(): Users
    {
        return $this->resolve('users.controller');
    }

    /**
     * {@inheritDoc}
     *
     * @return ViewController|ViewEngine
     */
    public function viewer($view = null, $data = [])
    {
        /** @var ViewEngine $viewer */
        $viewer = $this->resolve('viewer');

        if (func_num_args() === 0) {
            return $viewer;
        }

        return $viewer->make("_override::{$view}", $data);
    }
}
