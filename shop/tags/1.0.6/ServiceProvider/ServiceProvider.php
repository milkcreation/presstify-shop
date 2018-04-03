<?php

namespace tiFy\Plugins\Shop\ServiceProvider;

use tiFy\Core\ServiceProvider\AbstractServiceProvider;
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
use tiFy\Plugins\Shop\Gateways\GatewaysInterface;
use tiFy\Plugins\Shop\Gateways\GatewayList as GatewaysList;
use tiFy\Plugins\Shop\Notices\Notices;
use tiFy\Plugins\Shop\CustomTypes\CustomTypes;
use tiFy\Plugins\Shop\Orders\Orders;
use tiFy\Plugins\Shop\Orders\OrdersInterface;
use tiFy\Plugins\Shop\Orders\Order;
use tiFy\Plugins\Shop\Orders\OrderList;
use tiFy\Plugins\Shop\Orders\OrderItems\OrderItemCoupon;
use tiFy\Plugins\Shop\Orders\OrderItems\OrderItemFee;
use tiFy\Plugins\Shop\Orders\OrderItems\OrderItemProduct;
use tiFy\Plugins\Shop\Orders\OrderItems\OrderItemShipping;
use tiFy\Plugins\Shop\Orders\OrderItems\OrderItemTax;
use tiFy\Plugins\Shop\Products\Products;
use tiFy\Plugins\Shop\Products\ProductsInterface;
use tiFy\Plugins\Shop\Products\ProductItem as ProductsItem;
use tiFy\Plugins\Shop\Products\ProductItemInterface as ProductsItemInterface;
use tiFy\Plugins\Shop\Products\ProductList as ProductsList;
use tiFy\Plugins\Shop\Session\Session;
use tiFy\Plugins\Shop\Settings\Settings;
use tiFy\Plugins\Shop\Users\Users;
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
        OrderItemCoupon::class,
        OrderItemFee::class,
        OrderItemProduct::class,
        OrderItemShipping::class,
        OrderItemTax::class,
        OrderList::class,
        Products::class,
        ProductsItem::class,
        ProductsList::class,
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
            'controller'    => Orders::class,
            'order'         => Order::class,
            'item_coupon'   => OrderItemCoupon::class,
            'item_fee'      => OrderItemFee::class,
            'item_product'  => OrderItemProduct::class,
            'item_shipping' => OrderItemShipping::class,
            'item_tax'      => OrderItemTax::class,
            'list'          => OrderList::class,
        ],
        'products'     => [
            'controller' => Products::class,
            'item'       => ProductsItem::class,
            'list'       => ProductsList::class,
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
        'orders'    => ['item_product'],
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
            ->setMapController('addresses.controller', function ($shop) {
                return Addresses::boot($shop);
            })
            ->setMapController('admin.controller', function ($shop) {
                return Admin::make($shop);
            })
            ->setMapController('cart.controller', function ($shop, $controller) {
                /** @var CartInterface $controller */
                return $controller::boot($shop);
            })
            ->setMapController('checkout.controller', function ($shop) {
                return Checkout::make($shop);
            })
            ->setMapController('functions.controller', function ($shop) {
                return Functions::boot($shop);
            })
            ->setMapController('gateways.controller', function ($shop, $controller) {
                /** @var GatewaysInterface $controller */
                return $controller::boot($shop);
            })
            ->setMapController('notices.controller', function ($shop) {
                return Notices::make($shop);
            })
            ->setMapController('custom_types.controller', function ($shop) {
                return CustomTypes::make($shop);
            })
            ->setMapController('orders.controller', function ($shop, $controller) {
                /** @var OrdersInterface $controller */
                return $controller::boot($shop);
            })
            ->setMapController('products.controller', function ($shop, $controller) {
                /** @var ProductsInterface $controller */
                return $controller::boot($shop);
            })
            ->setMapController('session.controller', function ($shop) {
                return Session::make($shop);
            })
            ->setMapController('settings.controller', function ($shop) {
                return Settings::make($shop);
            })
            ->setMapController('users.controller', function ($shop) {
                return Users::make($shop);
            });

        // Déclaration des personnalisations.
        parent::__construct($customs);

        // Déclaration de la listes des variables passées en arguments dans les controleurs.
        $this
            ->setMapArgs('addresses.controller', [$this->shop])
            ->setMapArgs('addresses.billing', [$this->shop, Addresses::class])
            ->setMapArgs('addresses.form_handler', [$this->shop])
            ->setMapArgs('addresses.shipping', [$this->shop, Addresses::class])
            ->setMapArgs('admin.controller', [$this->shop])
            ->setMapArgs('cart.controller', [
                $this->shop,
                $this->getMapCustom('cart.controller', 'controller') ? : $this->getDefault('cart.controller'),
            ])
            ->setMapArgs('cart.line', [$this->shop, Cart::class, []])
            ->setMapArgs('cart.session_items', [$this->shop, Cart::class])
            ->setMapArgs('checkout.controller', [$this->shop])
            ->setMapArgs('functions.controller', [$this->shop])
            ->setMapArgs('functions.date', ['now', true, $this->shop])
            ->setMapArgs('functions.page', [$this->shop])
            ->setMapArgs('functions.price', [$this->shop])
            ->setMapArgs('functions.url', [$this->shop])
            ->setMapArgs('gateways.controller', [
                $this->shop,
                $this->getMapCustom('gateways.controller', 'controller') ? : $this->getDefault('gateways.controller')
            ])
            ->setMapArgs('notices.controller', [$this->shop])
            ->setMapArgs('custom_types.controller', [$this->shop])
            ->setMapArgs('orders.controller', [
                $this->shop,
                $this->getMapCustom('orders.controller', 'controller') ? : $this->getDefault('orders.controller')
            ])
            ->setMapArgs('orders.item_product', [ProductsItemInterface::class, $this->shop, Order::class])
            ->setMapArgs('products.controller', [
                $this->shop,
                $this->getMapCustom('products.controller', 'controller') ? : $this->getDefault('products.controller')
            ])
            ->setMapArgs('session.controller', [$this->shop])
            ->setMapArgs('settings.controller', [$this->shop])
            ->setMapArgs('users.controller', [$this->shop]);
    }
}