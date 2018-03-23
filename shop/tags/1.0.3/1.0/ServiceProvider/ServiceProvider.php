<?php

namespace tiFy\Plugins\Shop\ServiceProvider;

use Illuminate\Support\Arr;
use League\Container\ServiceProvider\AbstractServiceProvider;
use League\Container\ServiceProvider\BootableServiceProviderInterface;
use League\Container\Exception\NotFoundException;
use \LogicException;
use ReflectionFunction;
use ReflectionException;
use tiFy\App\Traits\App as TraitsApp;
use tiFy\Plugins\Shop\Shop;
use tiFy\Plugins\Shop\Addresses\Addresses;
use tiFy\Plugins\Shop\Addresses\Billing as AddressesBilling;
use tiFy\Plugins\Shop\Addresses\FormHandler as AddressesFormHandler;
use tiFy\Plugins\Shop\Addresses\Shipping as AddressesShipping;
use tiFy\Plugins\Shop\Admin\Admin;
use tiFy\Plugins\Shop\Cart\Cart;
use tiFy\Plugins\Shop\Cart\CartInterface;
use tiFy\Plugins\Shop\Cart\Line as CartLine;
use tiFy\Plugins\Shop\Cart\LineList as CartLineList;
use tiFy\Plugins\Shop\Cart\SessionItems as CartSessionItems;
use tiFy\Plugins\Shop\Cart\Total as CartTotal;
use tiFy\Plugins\Shop\Checkout\Checkout;
use tiFy\Plugins\Shop\Functions\Functions;
use tiFy\Plugins\Shop\Functions\Date as FunctionsDate;
use tiFy\Plugins\Shop\Functions\Page as FunctionsPage;
use tiFy\Plugins\Shop\Functions\Price as FunctionsPrice;
use tiFy\Plugins\Shop\Functions\Url as FunctionsUrl;
use tiFy\Plugins\Shop\Gateways\Gateways;
use tiFy\Plugins\Shop\Gateways\GatewayList as GatewaysList;
use tiFy\Plugins\Shop\Notices\Notices;
use tiFy\Plugins\Shop\CustomTypes\CustomTypes;
use tiFy\Plugins\Shop\Orders\Orders;
use tiFy\Plugins\Shop\Orders\OrdersInterface;
use tiFy\Plugins\Shop\Orders\Order as OrdersItem;
use tiFy\Plugins\Shop\Orders\OrderList as OrdersList;
use tiFy\Plugins\Shop\Orders\OrderItem\OrderItemCoupon as OrdersItemCoupon;
use tiFy\Plugins\Shop\Orders\OrderItem\OrderItemFee as OrdersItemFee;
use tiFy\Plugins\Shop\Orders\OrderItem\OrderItemProduct as OrdersItemProduct;
use tiFy\Plugins\Shop\Orders\OrderItem\OrderItemShipping as OrdersItemShipping;
use tiFy\Plugins\Shop\Orders\OrderItem\OrderItemTax as OrdersItemTax;
use tiFy\Plugins\Shop\Products\Products;
use tiFy\Plugins\Shop\Products\ProductsInterface;
use tiFy\Plugins\Shop\Products\ProductItem as ProductsItem;
use tiFy\Plugins\Shop\Products\ProductItemInterface as ProductsItemInterface;
use tiFy\Plugins\Shop\Products\ProductList as ProductsList;
use tiFy\Plugins\Shop\Session\Session;
use tiFy\Plugins\Shop\Settings\Settings;
use tiFy\Plugins\Shop\Users\Users;

class ServiceProvider extends AbstractServiceProvider implements BootableServiceProviderInterface
{
    use TraitsApp;

    /**
     * Liste des services fournis.
     * @internal requis. Tous les alias de services à traiter doivent être renseignés.
     * @var string[]
     */
    protected $provides = [
        Addresses::class,
        AddressesBilling::class,
        AddressesFormHandler::class,
        AddressesShipping::class,
        Admin::class,
        Cart::class,
        CartLine::class,
        CartLineList::class,
        CartSessionItems::class,
        CartTotal::class,
        Checkout::class,
        Functions::class,
        FunctionsDate::class,
        FunctionsPage::class,
        FunctionsPrice::class,
        FunctionsUrl::class,
        Gateways::class,
        GatewaysList::class,
        Notices::class,
        CustomTypes::class,
        Orders::class,
        OrdersItem::class,
        OrdersItemCoupon::class,
        OrdersItemFee::class,
        OrdersItemProduct::class,
        OrdersItemShipping::class,
        OrdersItemTax::class,
        OrdersList::class,
        Products::class,
        ProductsItem::class,
        ProductsList::class,
        Session::class,
        Settings::class,
        Users::class
    ];

    /**
     * Cartographie des alias de service fournis.
     * @internal requis. Toutes les correspondances de services doivent être renseignées.
     * @var array
     */
    protected $aliases_map = [
        'addresses'    => [
            'controller'   => Addresses::class,
            'billing'      => AddressesBilling::class,
            'form_handler' => AddressesFormHandler::class,
            'shipping'     => AddressesShipping::class,
        ],
        'admin'        => [
            'controller' => Admin::class
        ],
        'cart'         => [
            'controller'    => Cart::class,
            'line'          => CartLine::class,
            'line_list'     => CartLineList::class,
            'session_items' => CartSessionItems::class,
            'total'         => CartTotal::class,
        ],
        'checkout'     => [
            'controller' => Checkout::class
        ],
        'functions'    => [
            'controller' => Functions::class,
            'date'       => FunctionsDate::class,
            'page'       => FunctionsPage::class,
            'price'      => FunctionsPrice::class,
            'url'        => FunctionsUrl::class
        ],
        'gateways'     => [
            'controller' => Gateways::class,
            'list'       => GatewaysList::class
        ],
        'notices'      => [
            'controller' => Notices::class
        ],
        'custom_types' => [
            'controller' => CustomTypes::class
        ],
        'orders'       => [
            'controller'    => Orders::class,
            'item'          => OrdersItem::class,
            'item_coupon'   => OrdersItemCoupon::class,
            'item_fee'      => OrdersItemFee::class,
            'item_product'  => OrdersItemProduct::class,
            'item_shipping' => OrdersItemShipping::class,
            'item_tax'      => OrdersItemTax::class,
            'list'          => OrdersList::class
        ],
        'products'     => [
            'controller'   => Products::class,
            'product_item' => ProductsItem::class,
            'product_list' => ProductsList::class,
        ],
        'session'      => [
            'controller' => Session::class
        ],
        'settings'     => [
            'controller' => Settings::class
        ],
        'users'        => [
            'controller' => Users::class
        ],
    ];

    /**
     * Cartographie des controleurs des services à traiter.
     * @var array
     */
    protected $provides_map = [];

    /**
     * Cartographie des variables passé en arguments dans les services.
     * @var array
     */
    protected $arguments_map = [];

    /**
     * Listes des services initiaux.
     * @var array
     */
    protected $bootable = [
        'addresses'    => ['controller'],
        'admin'        => ['controller'],
        'cart'         => ['controller'],
        'checkout'     => ['controller'],
        'functions'    => ['controller'],
        'gateways'     => ['controller'],
        'notices'      => ['controller'],
        'custom_types' => ['controller'],
        'orders'       => ['controller'],
        'products'     => ['controller'],
        'session'      => ['controller'],
        'settings'     => ['controller'],
        'users'        => ['controller']
    ];

    /**
     * Liste des services différés.
     * @var array
     */
    protected $deferred = [
        'addresses' => ['billing', 'form_handler', 'shipping'],
        'cart'      => ['line', 'session_items'],
        'functions' => ['date', 'page', 'price', 'url'],
        'orders'    => ['item_product']
    ];

    /**
     * Liste des services personnalisables.
     * @var array
     */
    protected $customs = [
        'addresses' => [
            'billing'      => AddressesBilling::class,
            'form_handler' => AddressesFormHandler::class,
            'shipping'     => AddressesShipping::class
        ],
        'cart'      => [
            'line'          => CartLine::class,
            'session_items' => CartSessionItems::class
        ],
        'functions' => [
            'date'  => FunctionsDate::class,
            'page'  => FunctionsPage::class,
            'price' => FunctionsPrice::class,
            'url'   => FunctionsUrl::class
        ],
        'orders'    => [
            'item'         => OrdersItem::class,
            'list'         => OrdersList::class,
            'item_product' => OrdersItemProduct::class
        ],
        'products'  => [
            'list' => ProductsList::class
        ]
    ];

    /**
     * Classe de rappel de la boutique.
     * @var Shop
     */
    protected $shop;

    /**
     * CONSTRUCTEUR
     *
     * @param Shop $shop Classe de rappel de la boutique
     *
     * @return void
     */
    public function __construct(Shop $shop)
    {
        // Définition de la classe de rappel de la boutique
        $this->shop = $shop;

        // Déclaration des services natifs (non personnalisables).
        $this
            ->addMapController('addresses.controller', function ($shop) {
                return Addresses::boot($shop);
            })
            ->addMapController('admin.controller', function ($shop) {
                return Admin::make($shop);
            })
            ->addMapController('cart.controller', function ($shop, $controller) {
                /** @var CartInterface $controller */
                return $controller::boot($shop);
            })
            ->addMapController('checkout.controller', function ($shop) {
                return Checkout::make($shop);
            })
            ->addMapController('functions.controller', function ($shop) {
                return Functions::boot($shop);
            })
            ->addMapController('gateways.controller', function ($shop) {
                return Gateways::make($shop);
            })
            ->addMapController('notices.controller', function ($shop) {
                return Notices::make($shop);
            })
            ->addMapController('custom_types.controller', function ($shop) {
                return CustomTypes::make($shop);
            })
            ->addMapController('orders.controller', function ($shop, $controller) {
                /** @var OrdersInterface $controller */
                return $controller::boot($shop);
            })
            ->addMapController('products.controller', function ($shop, $controller) {
                /** @var ProductsInterface $controller */
                return $controller::boot($shop);
            })
            ->addMapController('session.controller', function ($shop) {
                return Session::make($shop);
            })
            ->addMapController('settings.controller', function ($shop) {
                return Settings::make($shop);
            })
            ->addMapController('users.controller', function ($shop) {
                return Users::make($shop);
            });

        // Déclaration des services personnalisables.
        foreach ($this->customs as $category => $controllers) :
            foreach ($controllers as $name => $default_controller) :
                $key = "{$category}.{$name}";
                $controller = $this->shop->appConfig("service_provider.{$key}", $default_controller);

                switch ($key) :
                    default :
                        $this->addMapController($key, $controller);
                        break;
                    case 'cart.controller' :
                        $this->addMapController($key, function ($shop) use ($controller) {
                            $controller::boot($shop);
                        });
                        break;
                endswitch;
            endforeach;
        endforeach;

        /**
         * Déclaration de la listes des variables passés en arguments dans le service.
         * @var array
         */
        $this
            ->addMapArgs('addresses.controller', [$this->shop])
            ->addMapArgs('addresses.billing', [$this->shop, Addresses::class])
            ->addMapArgs('addresses.form_handler', [$this->shop])
            ->addMapArgs('addresses.shipping', [$this->shop, Addresses::class])
            ->addMapArgs('admin.controller', [$this->shop])
            ->addMapArgs('cart.controller', [
                $this->shop,
                $this->shop->appConfig('service_provider.cart.controller', Cart::class)
            ])
            ->addMapArgs('cart.line', [$this->shop, Cart::class, []])
            ->addMapArgs('cart.session_items', [$this->shop, Cart::class])
            ->addMapArgs('checkout.controller', [$this->shop])
            ->addMapArgs('functions.controller', [$this->shop])
            ->addMapArgs('functions.date', ['now', $this->shop])
            ->addMapArgs('functions.page', [$this->shop])
            ->addMapArgs('functions.price', [$this->shop])
            ->addMapArgs('functions.url', [$this->shop])
            ->addMapArgs('gateways.controller', [$this->shop])
            ->addMapArgs('notices.controller', [$this->shop])
            ->addMapArgs('custom_types.controller', [$this->shop])
            ->addMapArgs('orders.controller', [
                $this->shop,
                $this->shop->appConfig('service_provider.orders.controller', Orders::class)
            ])
            ->addMapArgs('orders.item_product', [
                ProductsItemInterface::class,
                $this->shop,
                OrdersItem::class
            ])
            ->addMapArgs('products.controller', [
                $this->shop,
                $this->shop->appConfig('service_provider.products.controller', Products::class)
            ])
            ->addMapArgs('session.controller', [$this->shop])
            ->addMapArgs('settings.controller', [$this->shop])
            ->addMapArgs('users.controller', [$this->shop]);
    }

    /**
     * Instanciation des services initiaux.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->bootable) :
            foreach ($this->bootable as $category => $controllers) :
                foreach ($controllers as $name) :
                    $this->add("{$category}.{$name}");
                endforeach;
            endforeach;
        endif;
    }

    /**
     * Instanciation des services différés.
     *
     * @return void
     */
    public function register()
    {
        if ($this->deferred) :
            foreach ($this->deferred as $category => $controllers) :
                foreach ($controllers as $name) :
                    $this->add("{$category}.{$name}");
                endforeach;
            endforeach;
        endif;
    }

    /**
     * Déclaration d'un service
     *
     * @param string $key Identifiant de qualification du service.
     *
     * @return void
     */
    public function add($key)
    {
        if (!$controller = $this->getMapController($key)) :
            return;
        endif;

        try {
            $alias = $this->getAlias($key);
        } catch (LogicException $e) {
            wp_die($e->getMessage(), __('Alias de fournisseur de service introuvable', 'tify'), $e->getCode());
            exit;
        }

        $args = $this->getMapArgs($key);

        if ($this->isClosure($controller)) :
            $this->getContainer()->add(
                $alias,
                call_user_func_array($controller, $args)
            );
        else :
            $this->getContainer()->add(
                $alias,
                $controller
            )
                ->withArguments($args);
        endif;
    }

    /**
     * Récupération d'un service déclaré
     *
     * @param string $key
     *
     * @return object
     */
    public function get($key, $args = [])
    {
        try {
            $alias = $this->getAlias($key);
        } catch (LogicException $e) {
            \wp_die($e->getMessage(), __('Alias de fournisseur de service introuvable', 'tify'), $e->getCode());
            exit;
        }

        try {
            return $this->appGetContainer($alias, $args);
        } catch (NotFoundException $e) {
            \wp_die($e->getMessage(), __('Service requis introuvable', 'tify'), $e->getCode());
            exit;
        }
    }

    /**
     * Récupération de l'alias d'un service.
     *
     * @param string $key Identifiant de qualification du service.
     * @internal La Syntaxe à points est autorisée et permet une recherche en profondeur dans les clés d'un tableau dimensionné.
     *
     * @return string
     *
     * @throws LogicException
     */
    public function getAlias($key)
    {
        if ($alias = Arr::get($this->aliases_map, $key, '')) :
            return $alias;
        endif;

        if ($this->provides($alias)) :
            return $alias;
        endif;

        throw new LogicException(
            sprintf(
                __('Le service qualifié par l\'alias <b>%s</b> n\'est pas disponible.', 'tify'),
                $key
            )
        );
    }

    /**
     * Ajout d'une déclaration de controleur de service.
     *
     * @param string $key Identifiant de qualification du service.
     * @param mixed $controller Définition du controleur.
     *
     * @return self
     */
    public function addMapController($key, $controller)
    {
        $this->provides_map = Arr::add($this->provides_map, $key, $controller);

        return $this;
    }

    /**
     * Récupération d'un controleur de service déclaré.
     *
     * @param string $key Identifiant de qualification du service.
     *
     * @return false|mixed
     */
    public function getMapController($key)
    {
        return Arr::get($this->provides_map, $key, false);
    }

    /**
     * Ajout d'une déclaration d'une liste de variables à passer en argument dans le service.
     *
     * @param string $key Identifiant de qualification du service.
     * @param array $args Liste des variables passés en argument du service.
     *
     * @return self
     */
    public function addMapArgs($key, $args)
    {
        $this->arguments_map = Arr::add($this->arguments_map, $key, $args);

        return $this;
    }

    /**
     * Récupération d'une liste de variable à passer en argument dans le service.
     *
     * @param string $key Identifiant de qualification du service.
     *
     * @return array
     */
    public function getMapArgs($key)
    {
        return Arr::get($this->arguments_map, $key, []);
    }

    /**
     * Vérifie si un controleur est une function anonyme
     *
     * @return bool
     */
    public function isClosure($controller)
    {
        try {
            $reflection = new ReflectionFunction($controller);
            return $reflection->isClosure();
        } catch (ReflectionException $e) {
            return false;
        }
    }
}