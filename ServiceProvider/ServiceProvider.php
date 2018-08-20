<?php

namespace tiFy\Plugins\Shop\ServiceProvider;

use tiFy\Core\ServiceProvider\AbstractServiceProvider;
use tiFy\Plugins\Shop\Shop;
use tiFy\Plugins\Shop\Addresses\Addresses;
use tiFy\Plugins\Shop\Addresses\AddressesInterface;
use tiFy\Plugins\Shop\Addresses\Billing as AddressesBilling;
use tiFy\Plugins\Shop\Addresses\FormHandler as AddressesFormHandler;
use tiFy\Plugins\Shop\Addresses\Shipping as AddressesShipping;
use tiFy\Plugins\Shop\Admin\Admin;
use tiFy\Plugins\Shop\Admin\AdminInterface;
use tiFy\Plugins\Shop\Cart\Cart;
use tiFy\Plugins\Shop\Cart\CartInterface;
use tiFy\Plugins\Shop\Cart\Line as CartLine;
use tiFy\Plugins\Shop\Cart\LineList as CartLineList;
use tiFy\Plugins\Shop\Cart\SessionItems as CartSessionItems;
use tiFy\Plugins\Shop\Cart\Total as CartTotal;
use tiFy\Plugins\Shop\Checkout\Checkout;
use tiFy\Plugins\Shop\Checkout\CheckoutInterface;
use tiFy\Plugins\Shop\CustomTypes\CustomTypes;
use tiFy\Plugins\Shop\CustomTypes\CustomTypesInterface;
use tiFy\Plugins\Shop\Functions\Functions;
use tiFy\Plugins\Shop\Functions\FunctionsInterface;
use tiFy\Plugins\Shop\Functions\Date as FunctionsDate;
use tiFy\Plugins\Shop\Functions\Page as FunctionsPage;
use tiFy\Plugins\Shop\Functions\Price as FunctionsPrice;
use tiFy\Plugins\Shop\Functions\Url as FunctionsUrl;
use tiFy\Plugins\Shop\Gateways\Gateways;
use tiFy\Plugins\Shop\Gateways\GatewaysInterface;
use tiFy\Plugins\Shop\Gateways\GatewayList as GatewaysList;
use tiFy\Plugins\Shop\Notices\Notices;
use tiFy\Plugins\Shop\Notices\NoticesInterface;
use tiFy\Plugins\Shop\Orders\Orders;
use tiFy\Plugins\Shop\Orders\OrdersInterface;
use tiFy\Plugins\Shop\Orders\Order;
use tiFy\Plugins\Shop\Orders\OrderInterface;
use tiFy\Plugins\Shop\Orders\OrderItems\OrderItems;
use tiFy\Plugins\Shop\Orders\OrderItems\OrderItem;
use tiFy\Plugins\Shop\Orders\OrderItems\OrderItemList;
use tiFy\Plugins\Shop\Orders\OrderItems\OrderItemTypeCoupon;
use tiFy\Plugins\Shop\Orders\OrderItems\OrderItemTypeFee;
use tiFy\Plugins\Shop\Orders\OrderItems\OrderItemTypeProduct;
use tiFy\Plugins\Shop\Orders\OrderItems\OrderItemTypeShipping;
use tiFy\Plugins\Shop\Orders\OrderItems\OrderItemTypeTax;
use tiFy\Plugins\Shop\Orders\OrderList;
use tiFy\Plugins\Shop\Products\Products;
use tiFy\Plugins\Shop\Products\ProductsInterface;
use tiFy\Plugins\Shop\Products\ProductItem as ProductsItem;
use tiFy\Plugins\Shop\Products\ProductList as ProductsList;
use tiFy\Plugins\Shop\Products\ProductPurchasingOption;
use tiFy\Plugins\Shop\Session\Session;
use tiFy\Plugins\Shop\Session\SessionInterface;
use tiFy\Plugins\Shop\Settings\Settings;
use tiFy\Plugins\Shop\Settings\SettingsInterface;
use tiFy\Plugins\Shop\Users\Users;
use tiFy\Plugins\Shop\Users\UsersInterface;
use tiFy\Plugins\Shop\Users\Customer as UsersCustomer;
use tiFy\Plugins\Shop\Users\LoggedOut as UsersLoggedOut;
use tiFy\Plugins\Shop\Users\ShopManager as UsersShopManager;

class ServiceProvider extends AbstractServiceProvider implements ProvideTraitsInterface
{
    use ProvideTraits;

    /**
     * Classe de rappel de la boutique.
     * @var Shop
     */
    protected $shop;

    /**
     * Liste des alias des services fournis.
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
        Order::class,
        OrderItems::class,
        OrderItem::class,
        OrderItemList::class,
        OrderItemTypeCoupon::class,
        OrderItemTypeFee::class,
        OrderItemTypeProduct::class,
        OrderItemTypeShipping::class,
        OrderItemTypeTax::class,
        OrderList::class,
        Products::class,
        ProductsItem::class,
        ProductsList::class,
        ProductPurchasingOption::class,
        Session::class,
        Settings::class,
        Users::class,
        UsersCustomer::class,
        UsersLoggedOut::class,
        UsersShopManager::class,
    ];

    /**
     * Cartographie des alias de service fournis.
     * @internal requis. Etabli la correspondances entre l'identifiant de qualification d'un service et son alias de service fourni (cf $this->provides).
     * Toutes les correspondances de services doivent être renseignées.
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
            'controller' => Admin::class,
        ],
        'cart'         => [
            'controller'    => Cart::class,
            'line'          => CartLine::class,
            'line_list'     => CartLineList::class,
            'session_items' => CartSessionItems::class,
            'total'         => CartTotal::class,
        ],
        'checkout'     => [
            'controller' => Checkout::class,
        ],
        'functions'    => [
            'controller' => Functions::class,
            'date'       => FunctionsDate::class,
            'page'       => FunctionsPage::class,
            'price'      => FunctionsPrice::class,
            'url'        => FunctionsUrl::class,
        ],
        'gateways'     => [
            'controller' => Gateways::class,
            'list'       => GatewaysList::class,
        ],
        'notices'      => [
            'controller' => Notices::class,
        ],
        'custom_types' => [
            'controller' => CustomTypes::class,
        ],
        'orders'       => [
            'controller'               => Orders::class,
            'order'                    => Order::class,
            'order_items'              => OrderItems::class,
            'order_item'               => OrderItem::class,
            'order_item_list'          => OrderItemList::class,
            'order_item_type_coupon'   => OrderItemTypeCoupon::class,
            'order_item_type_fee'      => OrderItemTypeFee::class,
            'order_item_type_product'  => OrderItemTypeProduct::class,
            'order_item_type_shipping' => OrderItemTypeShipping::class,
            'order_item_type_tax'      => OrderItemTypeTax::class,
            'list'                     => OrderList::class,
        ],
        'products'     => [
            'controller'        => Products::class,
            'item'              => ProductsItem::class,
            'list'              => ProductsList::class,
            'purchasing_option' => ProductPurchasingOption::class
        ],
        'session'      => [
            'controller' => Session::class,
        ],
        'settings'     => [
            'controller' => Settings::class,
        ],
        'users'        => [
            'controller'   => Users::class,
            'customer'     => UsersCustomer::class,
            'logged_out'   => UsersLoggedOut::class,
            'shop_manager' => UsersShopManager::class,
        ],
    ];

    /**
     * Listes des services qui seront instanciés au démarrage.
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
     * Liste des services qui seront instanciés de manière différée.
     * @var array
     */
    protected $deferred = [
        'addresses' => ['billing', 'form_handler', 'shipping'],
        'cart'      => ['line', 'session_items'],
        'functions' => ['date', 'page', 'price', 'url'],
        'orders'    => [
            'order',
            'list',
            'order_items',
            'order_item',
            'order_item_list',
            'order_item_type_coupon',
            'order_item_type_fee',
            'order_item_type_product',
            'order_item_type_shipping',
            'order_item_type_tax'
        ],
        'products'  => ['item', 'list', 'purchasing_option'],
        'users'     => ['customer', 'shop_manager']
    ];

    /**
     * CONSTRUCTEUR
     *
     * @param array $customs Liste des attributs de personnalisation.
     * @param Shop $shop Classe de rappel de la boutique.
     *
     * @return void
     */
    public function __construct($customs = [], Shop $shop)
    {
        // Définition de la classe de rappel de la boutique
        $this->shop = $shop;

        // Pré-déclaration des controleurs principaux.
        $this
            ->setMapController('addresses.controller', function ($shop, $controller) {
                /** @var AddressesInterface $controller */
                return $controller::make($shop);
            })
            ->setMapController('admin.controller', function ($shop, $controller) {
                /** @var AdminInterface $controller */
                return $controller::make($shop);
            })
            ->setMapController('cart.controller', function ($shop, $controller) {
                /** @var CartInterface $controller */
                return $controller::make($shop);
            })
            ->setMapController('checkout.controller', function ($shop, $controller) {
                /** @var CheckoutInterface $controller */
                return $controller::make($shop);
            })
            ->setMapController('custom_types.controller', function ($shop, $controller) {
                /** @var CustomTypesInterface $controller */
                return $controller::make($shop);
            })
            ->setMapController('functions.controller', function ($shop, $controller) {
                /** @var FunctionsInterface $controller */
                return $controller::make($shop);
            })
            ->setMapController('gateways.controller', function ($shop, $controller) {
                /** @var GatewaysInterface $controller */
                return $controller::make($shop);
            })
            ->setMapController('notices.controller', function ($shop, $controller) {
                /** @var NoticesInterface $controller */
                return $controller::make($shop);
            })
            ->setMapController('orders.controller', function ($shop, $controller) {
                /** @var OrdersInterface $controller */
                return $controller::make($shop);
            })
            ->setMapController('products.controller', function ($shop, $controller) {
                /** @var ProductsInterface $controller */
                return $controller::make($shop);
            })
            ->setMapController('session.controller', function ($shop, $controller) {
                /** @var SessionInterface $controller */
                return $controller::make($shop);
            })
            ->setMapController('settings.controller', function ($shop, $controller) {
                /** @var SettingsInterface $controller */
                return $controller::make($shop);
            })
            ->setMapController('users.controller', function ($shop, $controller) {
                /** @var UsersInterface $controller */
                return $controller::make($shop);
            });

        // Déclaration des personnalisations.
        parent::__construct($customs);

        // Déclaration de la listes des variables passées en arguments dans les controleurs.
        $this
            // Addresses
            ->setMapArgs('addresses.controller', [
                $this->shop,
                $this->getMapCustom('addresses.controller', 'controller') ?: $this->getDefault('addresses.controller')
            ])
            ->setMapArgs('addresses.billing', [$this->shop, Addresses::class])
            ->setMapArgs('addresses.form_handler', [$this->shop])
            ->setMapArgs('addresses.shipping', [$this->shop, Addresses::class])
            // Admin
            ->setMapArgs('admin.controller', [
                $this->shop,
                $this->getMapCustom('admin.controller', 'controller') ?: $this->getDefault('admin.controller')
            ])
            // Cart
            ->setMapArgs('cart.controller', [
                $this->shop,
                $this->getMapCustom('cart.controller', 'controller') ?: $this->getDefault('cart.controller')
            ])
            ->setMapArgs('cart.line', [$this->shop, Cart::class, []])
            ->setMapArgs('cart.session_items', [$this->shop, Cart::class])
            // Checkout
            ->setMapArgs('checkout.controller', [
                $this->shop,
                $this->getMapCustom('checkout.controller', 'controller') ?: $this->getDefault('checkout.controller')
            ])
            // CustomTypes
            ->setMapArgs('custom_types.controller', [
                $this->shop,
                $this->getMapCustom('custom_types.controller',
                    'controller') ?: $this->getDefault('custom_types.controller')
            ])
            // Functions
            ->setMapArgs('functions.controller', [
                $this->shop,
                $this->getMapCustom('functions.controller', 'controller') ?: $this->getDefault('functions.controller')
            ])
            ->setMapArgs('functions.date', ['now', true, $this->shop])
            ->setMapArgs('functions.page', [$this->shop])
            ->setMapArgs('functions.price', [$this->shop])
            ->setMapArgs('functions.url', [$this->shop])
            // Gateways
            ->setMapArgs('gateways.controller', [
                $this->shop,
                $this->getMapCustom('gateways.controller', 'controller') ?: $this->getDefault('gateways.controller')
            ])
            // Notices
            ->setMapArgs('notices.controller', [
                $this->shop,
                $this->getMapCustom('notices.controller', 'controller') ?: $this->getDefault('notices.controller')
            ])
            // Orders
            ->setMapArgs('orders.controller', [
                $this->shop,
                $this->getMapCustom('orders.controller', 'controller') ?: $this->getDefault('orders.controller')
            ])
            ->setMapArgs('orders.order_item', [[], $this->shop])
            ->setMapArgs('orders.order_item_list', [[], $this->shop])
            ->setMapArgs('orders.order_items', [OrderInterface::class, $this->shop])
            ->setMapArgs('orders.order_item_type_coupon', [0, OrderInterface::class, $this->shop])
            ->setMapArgs('orders.order_item_type_fee', [0, OrderInterface::class, $this->shop])
            ->setMapArgs('orders.order_item_type_product', [0, OrderInterface::class, $this->shop])
            ->setMapArgs('orders.order_item_type_shipping', [0, OrderInterface::class, $this->shop])
            ->setMapArgs('orders.order_item_type_tax', [0, OrderInterface::class, $this->shop])
            // Products
            ->setMapArgs('products.controller', [
                $this->shop,
                $this->getMapCustom('products.controller', 'controller') ?: $this->getDefault('products.controller')
            ])
            ->setMapArgs('products.purchasing_option', [
                '',
                null,
                $this->shop
            ])
            // Session
            ->setMapArgs('session.controller', [
                $this->shop,
                $this->getMapCustom('session.controller', 'controller') ?: $this->getDefault('session.controller')
            ])
            // Settings
            ->setMapArgs('settings.controller', [
                $this->shop,
                $this->getMapCustom('settings.controller', 'controller') ?: $this->getDefault('settings.controller')
            ])
            // Users
            ->setMapArgs('users.controller', [
                $this->shop,
                $this->getMapCustom('users.controller', 'controller') ?: $this->getDefault('users.controller')
            ]);
    }
}