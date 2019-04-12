<?php

namespace tiFy\Plugins\Shop;

use tiFy\Container\ServiceProvider;
use tiFy\Contracts\Form\FormFactory;
use tiFy\Plugins\Shop\Actions\Actions;
use tiFy\Plugins\Shop\Addresses\Addresses;
use tiFy\Plugins\Shop\Addresses\Billing as AddressesBilling;
use tiFy\Plugins\Shop\Addresses\FormHandler as AddressesFormHandler;
use tiFy\Plugins\Shop\Addresses\Shipping as AddressesShipping;
use tiFy\Plugins\Shop\Admin\Admin;
use tiFy\Plugins\Shop\Api\Api;
use tiFy\Plugins\Shop\Api\Orders\Orders as ApiOrders;
use tiFy\Plugins\Shop\Cart\Cart;
use tiFy\Plugins\Shop\Cart\Line as CartLine;
use tiFy\Plugins\Shop\Cart\LineList as CartLineList;
use tiFy\Plugins\Shop\Cart\SessionItems as CartSessionItems;
use tiFy\Plugins\Shop\Cart\Total as CartTotal;
use tiFy\Plugins\Shop\Checkout\Checkout;
use tiFy\Plugins\Shop\CustomTypes\CustomTypes;
use tiFy\Plugins\Shop\Functions\Functions;
use tiFy\Plugins\Shop\Functions\Date as FunctionsDate;
use tiFy\Plugins\Shop\Functions\Page as FunctionsPage;
use tiFy\Plugins\Shop\Functions\Price as FunctionsPrice;
use tiFy\Plugins\Shop\Functions\Url as FunctionsUrl;
use tiFy\Plugins\Shop\Gateways\CashOnDelivery\CashOnDelivery as GatewaysCachOnDelivery;
use tiFy\Plugins\Shop\Gateways\Cheque\Cheque as GatewaysCheque;
use tiFy\Plugins\Shop\Gateways\Gateways;
use tiFy\Plugins\Shop\Gateways\GatewayList as GatewaysList;
use tiFy\Plugins\Shop\Notices\Notices;
use tiFy\Plugins\Shop\Orders\Orders;
use tiFy\Plugins\Shop\Orders\Order;
use tiFy\Plugins\Shop\Orders\OrderItems\OrderItems;
use tiFy\Plugins\Shop\Orders\OrderItems\OrderItem;
use tiFy\Plugins\Shop\Orders\OrderItems\OrderItemList;
use tiFy\Plugins\Shop\Orders\OrderItems\OrderItemTypeCoupon;
use tiFy\Plugins\Shop\Orders\OrderItems\OrderItemTypeFee;
use tiFy\Plugins\Shop\Orders\OrderItems\OrderItemTypeProduct;
use tiFy\Plugins\Shop\Orders\OrderItems\OrderItemTypeShipping;
use tiFy\Plugins\Shop\Orders\OrderItems\OrderItemTypeTax;
use tiFy\Plugins\Shop\Orders\OrderList;
use tiFy\Plugins\Shop\Products\ObjectType\Categorized as ProductsObjectTypeCategorized;
use tiFy\Plugins\Shop\Products\ObjectType\Uncategorized as ProductsObjectTypeUncategorized;
use tiFy\Plugins\Shop\Products\Products;
use tiFy\Plugins\Shop\Products\ProductItem as ProductsItem;
use tiFy\Plugins\Shop\Products\ProductList as ProductsList;
use tiFy\Plugins\Shop\Products\ProductPurchasingOption;
use tiFy\Plugins\Shop\Session\Session;
use tiFy\Plugins\Shop\Settings\Settings;
use tiFy\Plugins\Shop\Users\Users;
use tiFy\Plugins\Shop\Users\Customer as UsersCustomer;
use tiFy\Plugins\Shop\Users\LoggedOut as UsersLoggedOut;
use tiFy\Plugins\Shop\Users\ShopManager as UsersShopManager;

class ShopServiceProvider extends ServiceProvider
{
    /**
     * Liste des alias de qualification des services fournis.
     * @var array
     */
    protected $aliases = [
        'shop.actions'                         => Actions::class,
        'shop.addresses.controller'            => Addresses::class,
        'shop.addresses.billing'               => AddressesBilling::class,
        'shop.addresses.form_handler'          => AddressesFormHandler::class,
        'shop.addresses.shipping'              => AddressesShipping::class,
        'shop.admin.controller'                => Admin::class,
        'shop.api'                             => Api::class,
        'shop.api.orders'                      => ApiOrders::class,
        'shop.cart.controller'                 => Cart::class,
        'shop.cart.line'                       => CartLine::class,
        'shop.cart.line_list'                  => CartLineList::class,
        'shop.cart.session_items'              => CartSessionItems::class,
        'shop.cart.total'                      => CartTotal::class,
        'shop.checkout.controller'             => Checkout::class,
        'shop.functions.controller'            => Functions::class,
        'shop.functions.date'                  => FunctionsDate::class,
        'shop.functions.page'                  => FunctionsPage::class,
        'shop.functions.price'                 => FunctionsPrice::class,
        'shop.functions.url'                   => FunctionsUrl::class,
        'shop.gateway.cash_on_delivery'        => GatewaysCachOnDelivery::class,
        'shop.gateway.cheque'                  => GatewaysCheque::class,
        'shop.gateways.controller'             => Gateways::class,
        'shop.gateways.list'                   => GatewaysList::class,
        'shop.notices.controller'              => Notices::class,
        'shop.custom_types.controller'         => CustomTypes::class,
        'shop.orders.controller'               => Orders::class,
        'shop.orders.order'                    => Order::class,
        'shop.orders.order_items'              => OrderItems::class,
        'shop.orders.order_item'               => OrderItem::class,
        'shop.orders.order_item_list'          => OrderItemList::class,
        'shop.orders.order_item_type_coupon'   => OrderItemTypeCoupon::class,
        'shop.orders.order_item_type_fee'      => OrderItemTypeFee::class,
        'shop.orders.order_item_type_product'  => OrderItemTypeProduct::class,
        'shop.orders.order_item_type_shipping' => OrderItemTypeShipping::class,
        'shop.orders.order_item_type_tax'      => OrderItemTypeTax::class,
        'shop.orders.list'                     => OrderList::class,
        'shop.products.controller'             => Products::class,
        'shop.products.item'                   => ProductsItem::class,
        'shop.products.list'                   => ProductsList::class,
        'shop.products.purchasing_option'      => ProductPurchasingOption::class,
        'shop.products.type.categorized'       => ProductsObjectTypeCategorized::class,
        'shop.products.type.uncategorized'     => ProductsObjectTypeUncategorized::class,
        'shop.session.controller'              => Session::class,
        'shop.settings.controller'             => Settings::class,
        'shop.users.controller'                => Users::class,
        'shop.users.customer'                  => UsersCustomer::class,
        'shop.users.logged_out'                => UsersLoggedOut::class,
        'shop.users.shop_manager'              => UsersShopManager::class
    ];

    /**
     * Liste des services personnalisés.
     * @var array
     */
    protected $customs = [];

    /**
     * Liste des noms de qualification des services fournis.
     * {@internal Permet le chargement différé des services qualifié.}
     * @var string[]
     */
    protected $provides = [
        'shop',
        'shop.actions',
        'shop.addresses.controller',
        'shop.addresses.billing',
        'shop.addresses.form_handler',
        'shop.addresses.shipping',
        'shop.admin.controller',
        'shop.api',
        'shop.api.orders',
        'shop.cart.controller',
        'shop.cart.line',
        'shop.cart.line_list',
        'shop.cart.session_items',
        'shop.cart.total',
        'shop.checkout.controller',
        'shop.functions.controller',
        'shop.functions.date',
        'shop.functions.page',
        'shop.functions.price',
        'shop.functions.url',
        'shop.gateway.cash_on_delivery',
        'shop.gateway.cheque',
        'shop.gateways.controller',
        'shop.gateways.list',
        'shop.notices.controller',
        'shop.custom_types.controller',
        'shop.orders.controller',
        'shop.orders.order',
        'shop.orders.order_items',
        'shop.orders.order_item',
        'shop.orders.order_item_list',
        'shop.orders.order_item_type_coupon',
        'shop.orders.order_item_type_fee',
        'shop.orders.order_item_type_product',
        'shop.orders.order_item_type_shipping',
        'shop.orders.order_item_type_tax',
        'shop.orders.list',
        'shop.products.controller',
        'shop.products.item',
        'shop.products.list',
        'shop.products.purchasing_option',
        'shop.products.type.categorized',
        'shop.products.type.uncategorized',
        'shop.session.controller',
        'shop.settings.controller',
        'shop.users.controller',
        'shop.users.customer',
        'shop.users.logged_out',
        'shop.users.shop_manager',
        'shop.viewer'
    ];

    /**
     * Listes des noms de qualification des services instanciés au démarrage.
     * @var array
     */
    protected $resolve = [
        'actions',
        'addresses.controller',
        'admin.controller',
        'api',
        'cart.controller',
        'checkout.controller',
        'custom_types.controller',
        'functions.controller',
        'gateways.controller',
        'notices.controller',
        'orders.controller',
        'products.controller',
        'session.controller',
        'settings.controller',
        'users.controller'
    ];

    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        $providers = config('shop.providers', []);
        array_walk($providers, function ($value, $key) {
            $this->customs["shop.{$key}"] = $value;
        });

        add_action('after_setup_theme', function () {
            $this->getContainer()->get('shop');

            foreach($this->resolve as $alias) {
                $this->getContainer()->get("shop.{$alias}")->boot();
            }
        });
    }

    /**
     * Récupération de la classe de rappel d'un service.
     *
     * @param string $alias Alias de qualification.
     *
     * @return string
     */
    public function getConcrete($alias)
    {
        return $this->customs[$alias] ?? ($this->aliases[$alias] ?? $alias);
    }

    /**
     * @inheritdoc
     */
    public function register()
    {
        $this->getContainer()->share('shop', function () {
            return new Shop($this->getContainer());
        });

        $this->registerActions();
        $this->registerAddresses();
        $this->registerAdmin();
        $this->registerApi();
        $this->registerCart();
        $this->registerCheckout();
        $this->registerCustomTypes();
        $this->registerFunctions();
        $this->registerGateways();
        $this->registerNotices();
        $this->registerOrders();
        $this->registerProducts();
        $this->registerSession();
        $this->registerSettings();
        $this->registerUsers();
        $this->registerViewer();
    }

    /**
     * @todo
     */
    public function registerActions()
    {
        $this->getContainer()->share('shop.actions', function () {
            /** @var AbstractShopSingleton $concrete */
            $concrete = $this->getConcrete('shop.actions');

            return $concrete::make('shop.actions', $this->getContainer()->get('shop'));
        });
    }

    /**
     * Déclaration des controleurs d'adresse de facturation et de livraison.
     *
     * @return void
     */
    public function registerAddresses()
    {
        $this->getContainer()->share('shop.addresses.controller', function () {
            $concrete = $this->getConcrete('shop.addresses.controller');

            return new $concrete($this->getContainer()->get('shop'));
        });

        $this->getContainer()->share('shop.addresses.billing', function () {
            $concrete = $this->getConcrete('shop.addresses.billing');

            return new $concrete(
                $this->getContainer()->get('shop.addresses.controller'),
                $this->getContainer()->get('shop')
            );
        });

        $this->getContainer()->add('shop.addresses.form_handler', function ($name, $attrs, FormFactory $form) {
            $concrete = $this->getConcrete('shop.addresses.form_handler');

            return new $concrete($name, $attrs, $form, $this->getContainer()->get('shop'));
        });

        $this->getContainer()->share('shop.addresses.shipping', function () {
            $concrete = $this->getConcrete('shop.addresses.shipping');

            return new $concrete(
                $this->getContainer()->get('shop.addresses.controller'),
                $this->getContainer()->get('shop')
            );
        });
    }

    /**
     * Déclaration des controleurs de l'interface d'administration.
     *
     * @return void
     */
    public function registerAdmin()
    {
        $this->getContainer()->share('shop.admin.controller', function () {
            /** @var AbstractShopSingleton $concrete */
            $concrete = $this->getConcrete('shop.admin.controller');

            return $concrete::make('shop.admin.controller', $this->getContainer()->get('shop'));
        });
    }

    /**
     * Déclaration des controleurs de l'api.
     *
     * @return void
     */
    public function registerApi()
    {
        $this->getContainer()->share('shop.api', function () {
            /** @var AbstractShopSingleton $concrete */
            $concrete = $this->getConcrete('shop.api');

            return $concrete::make('shop.api', $this->getContainer()->get('shop'));
        });

        $this->getContainer()->add('shop.api.orders', function () {
            $concrete = $this->getConcrete('shop.api.orders');

            return new $concrete();
        });
    }

    /**
     * Déclaration des controleurs du panier de commande.
     *
     * @return void
     */
    public function registerCart()
    {
        $this->getContainer()->share('shop.cart.controller', function () {
            /** @var AbstractShopSingleton $concrete */
            $concrete = $this->getConcrete('shop.cart.controller');

            return $concrete::make('shop.cart.controller', $this->getContainer()->get('shop'));
        });

        $this->getContainer()->add('shop.cart.line', function ($attrs) {
            $concrete = $this->getConcrete('shop.cart.line');

            return new $concrete(
                $attrs,
                $this->getContainer()->get('shop.cart.controller'),
                $this->getContainer()->get('shop')
            );
        });

        $this->getContainer()->add('shop.cart.line_list' , function () {
            $concrete = $this->getConcrete('shop.cart.line_list');

            return new $concrete();
        });

        $this->getContainer()->share('shop.cart.session_items', function () {
            $concrete = $this->getConcrete('shop.cart.session_items');

            return new $concrete(
                $this->getContainer()->get('shop.cart.controller'),
                $this->getContainer()->get('shop')
            );
        });

        $this->getContainer()->add('shop.cart.total', function () {
            $concrete = $this->getConcrete('shop.cart.total');

            return new $concrete(
                $this->getContainer()->get('shop.cart.controller'),
                $this->getContainer()->get('shop')
            );
        });
    }

    /**
     * Déclaration du controleur de traitement du paiement.
     *
     * @return void
     */
    public function registerCheckout()
    {
        $this->getContainer()->share('shop.checkout.controller', function () {
            /** @var AbstractShopSingleton $concrete */
            $concrete = $this->getConcrete('shop.checkout.controller');

            return $concrete::make('shop.checkout.controller', $this->getContainer()->get('shop'));
        });
    }

    /**
     * Déclaration du controleur de définition des types.
     *
     * @return void
     */
    public function registerCustomTypes()
    {
        $this->getContainer()->share('shop.custom_types.controller', function () {
            /** @var AbstractShopSingleton $concrete */
            $concrete = $this->getConcrete('shop.custom_types.controller');

            return $concrete::make('shop.custom_types.controller', $this->getContainer()->get('shop'));
        });
    }

    /**
     * Déclaration des controleurs de fonctions.
     *
     * @return void
     */
    public function registerFunctions()
    {
        $this->getContainer()->share('shop.functions.controller', function () {
            /** @var AbstractShopSingleton $concrete */
            $concrete = $this->getConcrete('shop.functions.controller');

            return $concrete::make('shop.functions.controller', $this->getContainer()->get('shop'));
        });

        $this->getContainer()->add('shop.functions.date', function ($time = 'now', $timezone = true, Shop $shop) {
            $concrete = $this->getConcrete('shop.functions.date');

            return new $concrete($time, $timezone, $shop);
        });

        $this->getContainer()->share('shop.functions.page', function () {
            $concrete = $this->getConcrete('shop.functions.page');

            return new $concrete($this->getContainer()->get('shop'));
        });

        $this->getContainer()->share('shop.functions.price', function () {
            $concrete = $this->getConcrete('shop.functions.price');

            return new $concrete($this->getContainer()->get('shop'));
        });

        $this->getContainer()->share('shop.functions.url', function () {
            $concrete = $this->getConcrete('shop.functions.url');

            return new $concrete($this->getContainer()->get('shop'));
        });
    }

    /**
     * Déclaration des controleurs de plateforme de paiement.
     *
     * @return void
     */
    public function registerGateways()
    {
        $this->getContainer()->share('shop.gateways.controller', function () {
            /** @var AbstractShopSingleton $concrete */
            $concrete = $this->getConcrete('shop.gateways.controller');

            return $concrete::make('shop.gateways.controller', $this->getContainer()->get('shop'));
        });

        $this->getContainer()->add('shop.gateway.cash_on_delivery', function ($id, $attrs) {
            $concrete = $this->getConcrete('shop.gateway.cash_on_delivery');

            return new $concrete($id, $attrs, $this->getContainer()->get('shop'));
        });

        $this->getContainer()->add('shop.gateway.cheque', function ($id, $attrs) {
            $concrete = $this->getConcrete('shop.gateway.cheque');

            return new $concrete($id, $attrs, $this->getContainer()->get('shop'));
        });

        $this->getContainer()->add('shop.gateways.list', function ($items) {
            $concrete = $this->getConcrete('shop.gateways.list');

            return new $concrete($items, $this->getContainer()->get('shop'));
        });
    }

    /**
     * Déclaration du controleur de message de notification.
     *
     * @return void
     */
    public function registerNotices()
    {
        $this->getContainer()->share('shop.notices.controller', function () {
            /** @var AbstractShopSingleton $concrete */
            $concrete = $this->getConcrete('shop.notices.controller');

            return $concrete::make('shop.notices.controller', $this->getContainer()->get('shop'));
        });
    }

    /**
     * Déclaration des controleurs de message de commande.
     *
     * @return void
     */
    public function registerOrders()
    {
        $this->getContainer()->share('shop.orders.controller', function () {
            /** @var AbstractShopSingleton $concrete */
            $concrete = $this->getConcrete('shop.orders.controller');

            return $concrete::make('shop.orders.controller', $this->getContainer()->get('shop'));
        });

        $this->getContainer()->add('shop.orders.order', function (\WP_Post $post) {
            $concrete = $this->getConcrete('shop.orders.order');

            return new $concrete($post, $this->getContainer()->get('shop'));
        });

        $this->getContainer()->add('shop.orders.order_item', function ($attrs = []) {
            $concrete = $this->getConcrete('shop.orders.order_item');

            return new $concrete($attrs, $this->getContainer()->get('shop'));
        });

        $this->getContainer()->add('shop.orders.order_item_list', function ($items = []) {
            $concrete = $this->getConcrete('shop.orders.order_item_list');

            return new $concrete($items, $this->getContainer()->get('shop'));
        });

        $this->getContainer()->add('shop.orders.order_item_type_coupon', function ($item = 0, $order) {
            $concrete = $this->getConcrete('shop.orders.order_item_type_coupon');

            return new $concrete($item, $order, $this->getContainer()->get('shop'));
        });

        $this->getContainer()->add('shop.orders.order_item_type_fee', function ($item = 0, $order) {
            $concrete = $this->getConcrete('shop.orders.order_item_type_fee');

            return new $concrete($item, $order, $this->getContainer()->get('shop'));
        });

        $this->getContainer()->add('shop.orders.order_item_type_product', function ($item = 0, $order) {
            $concrete = $this->getConcrete('shop.orders.order_item_type_product');

            return new $concrete($item, $order, $this->getContainer()->get('shop'));
        });

        $this->getContainer()->add('shop.orders.order_item_type_shipping', function ($item = 0, $order) {
            $concrete = $this->getConcrete('shop.orders.order_item_type_shipping');

            return new $concrete($item, $order, $this->getContainer()->get('shop'));
        });

        $this->getContainer()->add('shop.orders.order_item_type_tax', function ($item = 0, $order) {
            $concrete = $this->getConcrete('shop.orders.order_item_type_tax');

            return new $concrete($item, $order, $this->getContainer()->get('shop'));
        });

        $this->getContainer()->add('shop.orders.order_items', function ($order) {
            $concrete = $this->getConcrete('shop.orders.order_items');

            return new $concrete($order, $this->getContainer()->get('shop'));
        });

        $this->getContainer()->add('shop.orders.list', function ($items) {
            $concrete = $this->getConcrete('shop.orders.list');

            return new $concrete($items, $this->getContainer()->get('shop'));
        });
    }

    /**
     * Déclaration des controleurs de produits.
     *
     * @return void
     */
    public function registerProducts()
    {
        $this->getContainer()->share('shop.products.controller', function () {
            /** @var AbstractShopSingleton $concrete */
            $concrete = $this->getConcrete('shop.products.controller');

            return $concrete::make('shop.products.controller', $this->getContainer()->get('shop'));
        });

        $this->getContainer()->add('shop.products.item', function (\WP_Post $wp_post) {
            $concrete = $this->getConcrete('shop.products.item');

            return new $concrete($wp_post, $this->getContainer()->get('shop'));
        });

        $this->getContainer()->add('shop.products.list', function ($items) {
            $concrete = $this->getConcrete('shop.products.list');

            return new $concrete($items, $this->getContainer()->get('shop'));
        });

        $this->getContainer()->add('shop.products.purchasing_option', function ($name, $attrs, $product) {
            $concrete = $this->getConcrete('shop.products.purchasing_option');

            return new $concrete($name, $attrs, $product, $this->getContainer()->get('shop'));
        });

        /*
        $this->getContainer()->add('shop.products.type.categorized', function ($items) {
            $concrete = $this->getConcrete('shop.products.type.categorized');

            return new $concrete($items, $this->getContainer()->get('shop'));
        });
        */

        $this->getContainer()->add('shop.products.type.uncategorized', function ($name, $attrs) {
            $concrete = $this->getConcrete('shop.products.type.uncategorized');

            return new $concrete($name, $attrs, $this->getContainer()->get('shop'));
        });
    }

    /**
     * Déclaration du controleur de session.
     *
     * @return void
     */
    public function registerSession()
    {
        $this->getContainer()->share('shop.session.controller', function () {
            /** @var AbstractShopSingleton $concrete */
            $concrete = $this->getConcrete('shop.session.controller');

            return $concrete::make('shop.session.controller', $this->getContainer()->get('shop'));
        });
    }

    /**
     * Déclaration du controleur des réglages de la boutique.
     *
     * @return void
     */
    public function registerSettings()
    {
        $this->getContainer()->share('shop.settings.controller', function () {
            /** @var AbstractShopSingleton $concrete */
            $concrete = $this->getConcrete('shop.settings.controller');

            return $concrete::make('shop.settings.controller', $this->getContainer()->get('shop'));
        });
    }

    /**
     * Déclaration des controleurs utilisateurs.
     *
     * @return void
     */
    public function registerUsers()
    {
        $this->getContainer()->share('shop.users.controller', function () {
            /** @var AbstractShopSingleton $concrete */
            $concrete = $this->getConcrete('shop.users.controller');

            return $concrete::make('shop.users.controller', $this->getContainer()->get('shop'));
        });

        $this->getContainer()->add('shop.users.customer', function (\WP_User $user) {
            $concrete = $this->getConcrete('shop.users.customer');

            return new $concrete($user, $this->getContainer()->get('shop'));
        });

        $this->getContainer()->add('shop.users.logged_out', function (\WP_User $user) {
            $concrete = $this->getConcrete('shop.users.logged_out');

            return new $concrete($user, $this->getContainer()->get('shop'));
        });

        $this->getContainer()->add('shop.users.shop_manager', function (\WP_User $user) {
            $concrete = $this->getConcrete('shop.users.shop_manager');

            return new $concrete($user, $this->getContainer()->get('shop'));
        });
    }

    /**
     * Déclaration du controleur de gabarit d'affichage.
     *
     * @return void
     */
    public function registerViewer()
    {
        $this->getContainer()->share('shop.viewer', function () {
            $default_dir = __DIR__ . '/Resources/views';
            return view()
                ->setDirectory($default_dir)
                ->setController(config('shop.viewer.controller') ?: ShopViewController::class)
                ->setOverrideDir(($dir = config('shop.viewer.override_dir')) && is_dir($dir)
                    ? $dir
                    : $default_dir
                )
                ->set('shop', $this->getContainer()->get('shop'));
        });
    }
}