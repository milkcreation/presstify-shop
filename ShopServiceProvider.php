<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop;

use tiFy\Container\ServiceProvider;
use tiFy\Plugins\Shop\Contracts\{
    Actions as ActionsContract,
    AddressBillingInterface as AddressBillingContract,
    AddressFormHandlerInterface as AddressFormHandlerContract,
    AddressShippingInterface as AddressShippingContract,
    AddressesInterface as AddressesContract,
    AdminInterface as AdminContract,
    Api as ApiContract,
    CartInterface as CartContract,
    CartLineInterface as CartLineContact,
    CartLineListInterface as CartLineListContact,
    CartSessionItemsInterface as CartSessionItemsContact,
    CartTotalInterface as CartTotalContract,
    GatewayInterface as GatewayContract,
    GatewaysInterface as GatewaysContract,
    ProductItemInterface as ProductContract,
    Routes as RoutesContract,
    ShopInterface as ShopContract
};
use tiFy\Plugins\Shop\{
    Actions\Actions,
    Addresses\Addresses,
    Addresses\Billing as AddressesBilling,
    Addresses\FormHandler as AddressesFormHandler,
    Addresses\Shipping as AddressesShipping,
    Admin\Admin,
    Api\Api,
    Api\Orders\Orders as ApiOrders,
    Cart\Cart,
    Cart\Line as CartLine,
    Cart\LineList as CartLineList,
    Cart\SessionItems as CartSessionItems,
    Cart\Total as CartTotal,
    Checkout\Checkout,
    CustomTypes\CustomTypes,
    Functions\Functions,
    Functions\Date as FunctionsDate,
    Functions\Page as FunctionsPage,
    Functions\Price as FunctionsPrice,
    Functions\Url as FunctionsUrl,
    Gateways\CashOnDeliveryGateway,
    Gateways\ChequeGateway,
    Gateways\Gateways,
    Notices\Notices,
    Orders\Orders,
    Orders\Order,
    Orders\OrderItems\OrderItems,
    Orders\OrderItems\OrderItem,
    Orders\OrderItems\OrderItemList,
    Orders\OrderItems\OrderItemTypeCoupon,
    Orders\OrderItems\OrderItemTypeFee,
    Orders\OrderItems\OrderItemTypeProduct,
    Orders\OrderItems\OrderItemTypeShipping,
    Orders\OrderItems\OrderItemTypeTax,
    Orders\OrderList,
    Products\ObjectType\Categorized as ProductsObjectTypeCategorized,
    Products\ObjectType\Uncategorized as ProductsObjectTypeUncategorized,
    Products\Products,
    Products\ProductItem as ProductsItem,
    Products\ProductList as ProductsList,
    Products\ProductPurchasingOption,
    Routing\Routes,
    Session\Session,
    Settings\Settings,
    Users\Users,
    Users\Customer as UsersCustomer,
    Users\LoggedOut as UsersLoggedOut,
    Users\ShopManager as UsersShopManager};
use WP_Post;
use WP_User;

class ShopServiceProvider extends ServiceProvider
{
    /**
     * Liste des alias de qualification des services fournis.
     * @var array
     */
    protected $aliases = [
        'shop'                                 => Shop::class,
        'shop.actions'                         => Actions::class,
        'shop.addresses'                       => Addresses::class,
        'shop.addresses.billing'               => AddressesBilling::class,
        'shop.addresses.form-handler'          => AddressesFormHandler::class,
        'shop.addresses.shipping'              => AddressesShipping::class,
        'shop.admin'                           => Admin::class,
        'shop.api'                             => Api::class,
        'shop.api.orders'                      => ApiOrders::class,
        'shop.cart'                            => Cart::class,
        'shop.cart.line'                       => CartLine::class,
        'shop.cart.line-list'                  => CartLineList::class,
        'shop.cart.session-items'              => CartSessionItems::class,
        'shop.cart.total'                      => CartTotal::class,
        'shop.checkout.controller'             => Checkout::class,
        'shop.functions.controller'            => Functions::class,
        'shop.functions.date'                  => FunctionsDate::class,
        'shop.functions.page'                  => FunctionsPage::class,
        'shop.functions.price'                 => FunctionsPrice::class,
        'shop.functions.url'                   => FunctionsUrl::class,
        'shop.gateway.cash_on_delivery'        => CashOnDeliveryGateway::class,
        'shop.gateway.cheque'                  => ChequeGateway::class,
        'shop.gateways'                        => Gateways::class,
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
        'shop.routing.routes'                  => Routes::class,
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
        'shop.addresses',
        'shop.addresses.billing',
        'shop.addresses.form-handler',
        'shop.addresses.shipping',
        'shop.admin',
        'shop.api',
        'shop.api.orders',
        'shop.cart',
        'shop.cart.line',
        'shop.cart.line-list',
        'shop.cart.session-items',
        'shop.cart.total',
        'shop.checkout.controller',
        'shop.functions.controller',
        'shop.functions.date',
        'shop.functions.page',
        'shop.functions.price',
        'shop.functions.url',
        'shop.gateway.cash_on_delivery',
        'shop.gateway.cheque',
        'shop.gateways',
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
        'shop.routing.routes',
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
        'addresses',
        'admin',
        'api',
        'cart',
        'checkout.controller',
        'custom_types.controller',
        'functions.controller',
        'gateways',
        'notices.controller',
        'orders.controller',
        'products.controller',
        'routing.routes',
        'session.controller',
        'settings.controller',
        'users.controller'
    ];

    /**
     * Instance de la boutique.
     * @var ShopContract
     */
    protected $shop;

    /**
     * @inheritDoc
     */
    public function boot(): void
    {
        $providers = config('shop.providers', []);

        array_walk($providers, function ($value, $key) {
            if ($key === 'shop') {
                $this->customs['shop'] = $value;
            } else {
                $this->customs["shop.{$key}"] = $value;
            }
        });

        add_action('after_setup_theme', function () {
            $this->shop = $this->getContainer()->get('shop');

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
     * @inheritDoc
     */
    public function register(): void
    {
        $this->getContainer()->share('shop', function () {
            /** @var ShopContract $concrete  */
            $concrete = $this->getConcrete('shop');

            return new $concrete($this->getContainer()->get('app'));
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
        $this->registerRouting();
        $this->registerSession();
        $this->registerSettings();
        $this->registerUsers();
        $this->registerViewer();
    }

    /**
     * @todo
     */
    public function registerActions(): void
    {
        $this->getContainer()->share('shop.actions', function () : ActionsContract {
            $concrete = $this->getConcrete('shop.actions');

            /** @var ActionsContract $instance */
            $instance = is_object($concrete) ? $concrete : new $concrete($this->getContainer()->get('shop'));

            return $instance;
        });
    }

    /**
     * Déclaration des controleurs d'adresse de facturation et de livraison.
     *
     * @return void
     */
    public function registerAddresses(): void
    {
        $this->getContainer()->share('shop.addresses', function (): AddressesContract {
            $concrete = $this->getConcrete('shop.addresses');

            /** @var AddressesContract $instance */
            $instance = is_object($concrete) ? $concrete : new $concrete($this->getContainer()->get('shop'));

            return $instance;
        });

        $this->getContainer()->share('shop.addresses.billing', function () : AddressBillingContract{
            $concrete = $this->getConcrete('shop.addresses.billing');

            /** @var AddressBillingContract $instance */
            $instance = is_object($concrete) ? $concrete : new $concrete($this->getContainer()->get('shop'));

            return $instance;
        });

        $this->getContainer()->add('shop.addresses.form-handler', function (): AddressFormHandlerContract {
            $concrete = $this->getConcrete('shop.addresses.form-handler');

            /** @var AddressFormHandlerContract $instance */
            $instance = is_object($concrete) ? $concrete : new $concrete($this->getContainer()->get('shop'));

            return $instance;
        });

        $this->getContainer()->share('shop.addresses.shipping', function (): AddressShippingContract {
            $concrete = $this->getConcrete('shop.addresses.shipping');

            /** @var AddressShippingContract $instance */
            $instance = is_object($concrete) ? $concrete : new $concrete($this->getContainer()->get('shop'));

            return $instance;
        });
    }

    /**
     * Déclaration des controleurs de l'interface d'administration.
     *
     * @return void
     */
    public function registerAdmin(): void
    {
        $this->getContainer()->share('shop.admin', function (): AdminContract {
            $concrete = $this->getConcrete('shop.admin');

            /** @var AdminContract $instance */
            $instance = is_object($concrete) ? $concrete : new $concrete($this->getContainer()->get('shop'));

            return $instance;
        });
    }

    /**
     * Déclaration des controleurs de l'api.
     *
     * @return void
     */
    public function registerApi(): void
    {
        $this->getContainer()->share('shop.api', function (): ApiContract {
            $concrete = $this->getConcrete('shop.api');

            /** @var ApiContract $instance */
            $instance = is_object($concrete) ? $concrete : new $concrete($this->getContainer()->get('shop'));

            return $instance;
        });

        $this->getContainer()->add('shop.api.orders', function () {
            $concrete = $this->getConcrete('shop.api.orders');

            $instance = is_object($concrete) ? $concrete : new $concrete($this->getContainer()->get('shop'));

            return $instance;
        });
    }

    /**
     * Déclaration des controleurs du panier de commande.
     *
     * @return void
     */
    public function registerCart(): void
    {
        $this->getContainer()->share('shop.cart', function (): CartContract {
            $concrete = $this->getConcrete('shop.cart');

            /** @var CartContract $instance */
            $instance = is_object($concrete) ? $concrete : new $concrete($this->getContainer()->get('shop'));

            return $instance;
        });

        $this->getContainer()->add('shop.cart.line', function (): CartLineContact {
            $concrete = $this->getConcrete('shop.cart.line');

            /** @var CartLineContact $instance */
            $instance = is_object($concrete) ? $concrete : new $concrete($this->getContainer()->get('shop'));

            return $instance;
        });

        $this->getContainer()->add('shop.cart.line-list' , function (): CartLineListContact {
            $concrete = $this->getConcrete('shop.cart.line-list');

            /** @var CartLineListContact $instance */
            $instance = is_object($concrete) ? $concrete : new $concrete();

            return $instance->setShop($this->getContainer()->get('shop'));
        });

        $this->getContainer()->share('shop.cart.session-items', function (): CartSessionItemsContact {
            $concrete = $this->getConcrete('shop.cart.session-items');

            /** @var CartSessionItemsContact $instance */
            $instance = is_object($concrete) ? $concrete : new $concrete($this->getContainer()->get('shop'));

            return $instance->parse();
        });

        $this->getContainer()->add('shop.cart.total', function (): CartTotalContract {
            $concrete = $this->getConcrete('shop.cart.total');

            /** @var CartTotalContract $instance */
            $instance = is_object($concrete) ? $concrete : new $concrete($this->getContainer()->get('shop'));

            return $instance;
        });
    }

    /**
     * Déclaration du controleur de traitement du paiement.
     *
     * @return void
     */
    public function registerCheckout(): void
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
    public function registerCustomTypes(): void
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
    public function registerFunctions(): void
    {
        $this->getContainer()->share('shop.functions.controller', function () {
            /** @var AbstractShopSingleton $concrete */
            $concrete = $this->getConcrete('shop.functions.controller');

            return $concrete::make('shop.functions.controller', $this->getContainer()->get('shop'));
        });

        $this->getContainer()->add('shop.functions.date', function ($time = 'now', $timezone = true) {
            $concrete = $this->getConcrete('shop.functions.date');

            return new $concrete($time, $timezone, $this->getContainer()->get('shop'));
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
    public function registerGateways(): void
    {
        $this->getContainer()->share('shop.gateways', function (): GatewaysContract {
            $concrete = $this->getConcrete('shop.gateways');

            /** @var GatewaysContract $instance */
            $instance = is_object($concrete) ? $concrete : new $concrete();

            return $instance->setShop($this->shop);
        });

        $this->getContainer()->add('shop.gateway.cash_on_delivery', function (): GatewayContract {
            $concrete = $this->getConcrete('shop.gateway.cash_on_delivery');

            /** @var GatewayContract $instance */
            $instance = is_object($concrete) ? $concrete : new $concrete();

            return $instance;
        });

        $this->getContainer()->add('shop.gateway.cheque', function (): GatewayContract {
            $concrete = $this->getConcrete('shop.gateway.cheque');

            /** @var GatewayContract $instance */
            $instance = is_object($concrete) ? $concrete : new $concrete();

            return $instance;
        });
    }

    /**
     * Déclaration du controleur de message de notification.
     *
     * @return void
     */
    public function registerNotices(): void
    {
        $this->getContainer()->share('shop.notices.controller', function () {
            /** @var AbstractShopSingleton $concrete */
            $concrete = $this->getConcrete('shop.notices.controller');

            return $concrete::make('shop.notices.controller', $this->shop);
        });
    }

    /**
     * Déclaration des controleurs de message de commande.
     *
     * @return void
     */
    public function registerOrders(): void
    {
        $this->getContainer()->share('shop.orders.controller', function () {
            /** @var AbstractShopSingleton $concrete */
            $concrete = $this->getConcrete('shop.orders.controller');

            return $concrete::make('shop.orders.controller', $this->shop);
        });

        $this->getContainer()->add('shop.orders.order', function (WP_Post $post) {
            $concrete = $this->getConcrete('shop.orders.order');

            return new $concrete($post, $this->shop);
        });

        $this->getContainer()->add('shop.orders.order_item', function ($attrs = []) {
            $concrete = $this->getConcrete('shop.orders.order_item');

            return new $concrete($attrs, $this->shop);
        });

        $this->getContainer()->add('shop.orders.order_item_list', function ($items = []) {
            $concrete = $this->getConcrete('shop.orders.order_item_list');

            return new $concrete($items, $this->shop);
        });

        $this->getContainer()->add('shop.orders.order_item_type_coupon', function ($item = 0, $order) {
            $concrete = $this->getConcrete('shop.orders.order_item_type_coupon');

            return new $concrete($item, $order, $this->shop);
        });

        $this->getContainer()->add('shop.orders.order_item_type_fee', function ($item = 0, $order) {
            $concrete = $this->getConcrete('shop.orders.order_item_type_fee');

            return new $concrete($item, $order, $this->shop);
        });

        $this->getContainer()->add('shop.orders.order_item_type_product', function ($item = 0, $order) {
            $concrete = $this->getConcrete('shop.orders.order_item_type_product');

            return new $concrete($item, $order, $this->shop);
        });

        $this->getContainer()->add('shop.orders.order_item_type_shipping', function ($item = 0, $order) {
            $concrete = $this->getConcrete('shop.orders.order_item_type_shipping');

            return new $concrete($item, $order, $this->shop);
        });

        $this->getContainer()->add('shop.orders.order_item_type_tax', function ($item = 0, $order) {
            $concrete = $this->getConcrete('shop.orders.order_item_type_tax');

            return new $concrete($item, $order, $this->shop);
        });

        $this->getContainer()->add('shop.orders.order_items', function ($order) {
            $concrete = $this->getConcrete('shop.orders.order_items');

            return new $concrete($order, $this->shop);
        });

        $this->getContainer()->add('shop.orders.list', function ($items) {
            $concrete = $this->getConcrete('shop.orders.list');

            return new $concrete($items, $this->shop);
        });
    }

    /**
     * Déclaration des controleurs de produits.
     *
     * @return void
     */
    public function registerProducts(): void
    {
        $this->getContainer()->share('shop.products.controller', function () {
            /** @var AbstractShopSingleton $concrete */
            $concrete = $this->getConcrete('shop.products.controller');

            return $concrete::make('shop.products.controller', $this->shop);
        });

        $this->getContainer()->add('shop.products.item', function (WP_Post $wp_post): ProductContract {
            $concrete = $this->getConcrete('shop.products.item');

            /** @var ProductContract $instance */
            $instance = new $concrete($wp_post);

            return $instance->setShop($this->shop);
        });

        $this->getContainer()->add('shop.products.list', function ($items) {
            $concrete = $this->getConcrete('shop.products.list');

            return new $concrete($items, $this->shop);
        });

        $this->getContainer()->add('shop.products.purchasing_option', function ($name, $attrs, $product) {
            $concrete = $this->getConcrete('shop.products.purchasing_option');

            return new $concrete($name, $attrs, $product, $this->shop);
        });

        /*
        $this->getContainer()->add('shop.products.type.categorized', function ($items) {
            $concrete = $this->getConcrete('shop.products.type.categorized');

            return new $concrete($items, $this->shop);
        });
        */

        $this->getContainer()->add('shop.products.type.uncategorized', function ($name, $attrs) {
            $concrete = $this->getConcrete('shop.products.type.uncategorized');

            return new $concrete($name, $attrs, $this->shop);
        });
    }

    /**
     * Déclaration des controleurs de routage.
     *
     * @return void
     */
    public function registerRouting(): void
    {
        $this->getContainer()->share('shop.routing.routes', function () : RoutesContract {
            $concrete = $this->getConcrete('shop.routing.routes');

            /** @var RoutesContract $instance */
            $instance = is_object($concrete) ? $concrete : new $concrete();

            return $instance->setShop($this->getContainer()->get('shop'));
        });
    }

    /**
     * Déclaration du controleur de session.
     *
     * @return void
     */
    public function registerSession(): void
    {
        $this->getContainer()->share('shop.session.controller', function () {
            /** @var AbstractShopSingleton $concrete */
            $concrete = $this->getConcrete('shop.session.controller');

            return $concrete::make('shop.session.controller', $this->shop);
        });
    }

    /**
     * Déclaration du controleur des réglages de la boutique.
     *
     * @return void
     */
    public function registerSettings(): void
    {
        $this->getContainer()->share('shop.settings.controller', function () {
            /** @var AbstractShopSingleton $concrete */
            $concrete = $this->getConcrete('shop.settings.controller');

            return $concrete::make('shop.settings.controller', $this->shop);
        });
    }

    /**
     * Déclaration des controleurs utilisateurs.
     *
     * @return void
     */
    public function registerUsers(): void
    {
        $this->getContainer()->share('shop.users.controller', function () {
            /** @var AbstractShopSingleton $concrete */
            $concrete = $this->getConcrete('shop.users.controller');

            return new $concrete($this->shop);
        });

        $this->getContainer()->add('shop.users.customer', function (WP_User $user) {
            $concrete = $this->getConcrete('shop.users.customer');

            return new $concrete($user, $this->shop);
        });

        $this->getContainer()->add('shop.users.logged_out', function (WP_User $user) {
            $concrete = $this->getConcrete('shop.users.logged_out');

            return new $concrete($user, $this->shop);
        });

        $this->getContainer()->add('shop.users.shop_manager', function (WP_User $user) {
            $concrete = $this->getConcrete('shop.users.shop_manager');

            return new $concrete($user, $this->shop);
        });
    }

    /**
     * Déclaration du controleur de gabarit d'affichage.
     *
     * @return void
     */
    public function registerViewer(): void
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
                ->set('shop', $this->shop);
        });
    }
}