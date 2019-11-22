<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop;

use Exception;
use tiFy\Contracts\Container\Container;
use tiFy\Contracts\View\ViewController;
use tiFy\Contracts\View\ViewEngine;
use tiFy\Plugins\Shop\Contracts\{
    Actions,
    Addresses,
    Cart,
    Checkout,
    Functions,
    Gateways,
    Notices,
    Order,
    Orders,
    Product,
    Products,
    Session,
    Settings,
    Shop as ShopContract,
    ShopEntity as ShopEntityContract,
    User,
    Users
};

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
     * Instance du gestionnaire de boutique.
     * @var ShopContract
     */
    protected static $instance;

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
     *
     * @throws Exception
     */
    public function __construct(Container $container)
    {
        if (!static::$instance) {
            static::$instance = $this;
        } else {
            throw new Exception(__('Une instance de la boutique existe déjà.', 'tify'));
        }

        $this->container = $container;
    }

    /**
     * Récupération de l'instance de la boutique.
     *
     * @return static|null
     */
    public static function instance(): ?ShopContract
    {
        return static::$instance;
    }

    /**
     * @inheritDoc
     */
    public function action(string $alias, array $parameters = [], bool $absolute = false): string
    {
        /** @var Actions $actions */
        return ($actions = $this->resolve('actions')) ? $actions->url($alias, $parameters, $absolute) : '';
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
        return $this->resolve('checkout');
    }

    /**
     * @inheritDoc
     */
    public function config($key = null, $default = null)
    {
        return config($key ? "shop.{$key}" : 'shop', $default);
    }

    /**
     * @inheritDoc
     */
    public function entity(): ShopEntityContract
    {
        return $this->resolve('entity');
    }

    /**
     * @inheritDoc
     */
    public function functions(): Functions
    {
        return $this->resolve('functions');
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
    public function order($id = null): ?Order
    {
        return $this->resolve('order', [$id]);
    }

    /**
     * @inheritDoc
     */
    public function orders(?array $args = null)
    {
        /** @var Orders $orders */
        $orders = $this->resolve('orders');

        return is_null($args) ? $orders : $orders->query($args);
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
    public function product($id = null): ?Product
    {
        return $this->resolve('product', [$id]);
    }

    /**
     * @inheritDoc
     */
    public function products(?array $args = null)
    {
        /** @var Products $products */
        $products = $this->resolve('products');

        return is_null($args) ? $products : $products->query($args);
    }

    /**
     * @inheritDoc
     */
    public function notices(): Notices
    {
        return $this->resolve('notices');
    }

    /**
     * @inheritDoc
     */
    public function resolve(string $alias, ...$args)
    {
        return $this->getContainer()->get("shop.{$alias}", ...$args);
    }

    /**
     * @inheritDoc
     */
    public function resolvable(string $alias): bool
    {
        return $this->getContainer()->has("shop.{$alias}");
    }

    /**
     * @inheritDoc
     */
    public function resourcesDir(string $path = ''): string
    {
        $path = '/Resources/' . ltrim($path, '/');

        return file_exists(__DIR__ . $path) ? __DIR__ . $path : '';
    }

    /**
     * @inheritDoc
     */
    public function resourcesUrl(string $path = ''): string
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
        return $this->resolve('session');
    }

    /**
     * @inheritDoc
     */
    public function settings(): Settings
    {
        return $this->resolve('settings');
    }

    /**
     * @inheritDoc
     */
    public function user(int $id = null): ?User
    {
        return $this->users()->get($id);
    }

    /**
     * @inheritDoc
     */
    public function users(): Users
    {
        return $this->resolve('users');
    }

    /**
     * {@inheritDoc}
     *
     * @return ViewController|ViewEngine
     */
    public function viewer(?string $view = null, $data = [])
    {
        /** @var ViewEngine $viewer */
        $viewer = $this->resolve('viewer');

        if (func_num_args() === 0) {
            return $viewer;
        }

        return $viewer->make("_override::{$view}", $data);
    }
}
