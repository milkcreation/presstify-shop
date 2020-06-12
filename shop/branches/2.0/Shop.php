<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop;

use Exception;
use tiFy\Contracts\Container\Container;
use tiFy\Contracts\View\Engine as ViewEngine;
use tiFy\Plugins\Shop\Contracts\{
    Cart,
    Checkout,
    Form,
    Functions,
    Gateways,
    Notices,
    Order,
    Orders,
    Product,
    Products,
    Route,
    Session,
    Settings,
    Shop as ShopContract,
    ShopEntity as ShopEntityContract,
    User,
    Users
};
use tiFy\Support\ParamsBag;

/**
 * @desc Extension PresstiFy de gestion de boutique en ligne.
 * @author Jordy Manner <jordy@milkcreation.fr>
 * @package tiFy\Plugins\Shop
 * @version 2.0.53
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
     * Indicateur d'initialisation.
     * @var bool
     */
    protected $booted = false;

    /**
     * Instance du gestionnaire de configuration.
     * @var ParamsBag
     */
    protected $config;

    /**
     * Conteneur d'injection de dépendances.
     * @var Container
     */
    protected $container;

    /**
     * CONSTRUCTEUR.
     *
     * @param array $config
     * @param Container $container
     *
     * @return void
     */
    public function __construct(array $config = [], Container $container = null)
    {
        $this->setConfig($config);

        if (!is_null($container)) {
            $this->setContainer($container);
        }

        if (!self::$instance instanceof static) {
            self::$instance = $this;
        }
    }

    /**
     * Récupération de l'instance courante.
     *
     * @return static
     *
     * @throws Exception
     */
    public static function instance(): ShopContract
    {
        if (self::$instance instanceof static) {
            return self::$instance;
        }

        throw new Exception(__('Impossible de récupérer l\'instance du gestionnaire de boutique.', 'tify'));
    }

    /**
     * @inheritDoc
     */
    public function boot(): ShopContract
    {
        if (!$this->booted) {
            $booting = [
                //'admin',
                //'api',
                'cart',
                //'checkout',
                'entity',
                //'functions',
                'gateways',
                'notices',
                //'orders',
                'products',
                'route',
                'session',
                //'settings',
                'users',
            ];

            foreach($booting as $alias) {
                $this->resolve($alias);
            }

            $this->booted = true;

            events()->trigger('shop.booted', [$this]);
        }

        return $this;
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
     * Récupération de paramètre|Définition de paramètres|Instance du gestionnaire de paramètre.
     *
     * @param string|array|null $key Clé d'indice du paramètre à récupérer|Liste des paramètre à définir.
     * @param mixed $default Valeur de retour par défaut lorsque la clé d'indice est une chaine de caractère.
     *
     * @return mixed|ParamsBag
     */
    public function config($key = null, $default = null)
    {
        if (!$this->config instanceof ParamsBag) {
            $this->config = new ParamsBag();
        }

        if (is_string($key)) {
            return $this->config->get($key, $default);
        } elseif (is_array($key)) {
            return $this->config->set($key);
        } else {
            return $this->config;
        }
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
    public function form(): Form
    {
        return $this->resolve('form');
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
     * Récupération du chemin absolu vers le répertoire des ressources.
     *
     * @param string|null $path Chemin relatif d'une resource (répertoire|fichier).
     *
     * @return string
     */
    public function resources(string $path = null): string
    {
        $path = $path ? '/' . ltrim($path, '/') : '';

        return (file_exists(__DIR__ . "/Resources{$path}")) ? __DIR__ . "/Resources{$path}" : '';
    }

    /**
     * @inheritDoc
     */
    public function route(): Route
    {
        return $this->resolve('route');
    }

    /**
     * @inheritDoc
     */
    public function session(): Session
    {
        return $this->resolve('session');
    }

    /**
     * Définition des paramètres de configuration.
     *
     * @param array $attrs Liste des attributs de configuration.
     *
     * @return static
     */
    public function setConfig(array $attrs): ShopContract
    {
        $this->config($attrs);

        return $this;
    }

    /**
     * Définition du conteneur d'injection de dépendances.
     *
     * @param Container $container
     *
     * @return static
     */
    public function setContainer(Container $container): ShopContract
    {
        $this->container = $container;

        return $this;
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
     * @inheritDoc
     *
     * @return string
     */
    public function view(string $name, array $data = [])
    {
        /** @var ViewEngine $view */
        $view = $this->resolve('view');

        return $view->render("_override::{$name}", $data);
    }
}
