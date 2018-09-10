<?php

namespace tiFy\Plugins\Shop;

use App\Json\ProductItem;
use \LogicException;
use tiFy\App\Container\AppServiceProvider;
use tiFy\Plugins\Shop\Products\ProductList;
use tiFy\Plugins\Shop\Shop;

/**
 * CONTROLEURS.
 */
use tiFy\Plugins\Shop\Addresses\Addresses;
use tiFy\Plugins\Shop\Addresses\Billing as AddressesBilling;
use tiFy\Plugins\Shop\Addresses\FormHandler as AddressesFormHandler;
use tiFy\Plugins\Shop\Addresses\Shipping as AddressesShipping;
use tiFy\Plugins\Shop\Admin\Admin;
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

/**
 * CONTRACTS
 */
use tiFy\Plugins\Shop\Contracts\AddressesInterface;
use tiFy\Plugins\Shop\Contracts\AddressBillingInterface;
use tiFy\Plugins\Shop\Contracts\AddressFormHandlerInterface;
use tiFy\Plugins\Shop\Contracts\AddressShippingInterface;
use tiFy\Plugins\Shop\Contracts\AdminInterface;
use tiFy\Plugins\Shop\Contracts\BootableControllerInterface;
use tiFy\Plugins\Shop\Contracts\CartInterface;
use tiFy\Plugins\Shop\Contracts\CartLineInterface;
use tiFy\Plugins\Shop\Contracts\CartLineListInterface;
use tiFy\Plugins\Shop\Contracts\CartSessionItemsInterface;
use tiFy\Plugins\Shop\Contracts\CartTotalInterface;
use tiFy\Plugins\Shop\Contracts\CheckoutInterface;
use tiFy\Plugins\Shop\Contracts\CustomTypesInterface;
use tiFy\Plugins\Shop\Contracts\FunctionsDateInterface;
use tiFy\Plugins\Shop\Contracts\FunctionsInterface;
use tiFy\Plugins\Shop\Contracts\FunctionsPageInterface;
use tiFy\Plugins\Shop\Contracts\FunctionsPriceInterface;
use tiFy\Plugins\Shop\Contracts\FunctionsUrlInterface;
use tiFy\Plugins\Shop\Contracts\GatewayListInterface;
use tiFy\Plugins\Shop\Contracts\GatewaysInterface;
use tiFy\Plugins\Shop\Contracts\NoticesInterface;
use tiFy\Plugins\Shop\Contracts\OrderInterface;
use tiFy\Plugins\Shop\Contracts\OrderListInterface;
use tiFy\Plugins\Shop\Contracts\OrdersInterface;
use tiFy\Plugins\Shop\Contracts\ProductItemInterface;
use tiFy\Plugins\Shop\Contracts\ProductListInterface;
use tiFy\Plugins\Shop\Contracts\ProductPurchasingOptionInterface;
use tiFy\Plugins\Shop\Contracts\ProductsInterface;
use tiFy\Plugins\Shop\Contracts\SessionInterface;
use tiFy\Plugins\Shop\Contracts\SettingsInterface;
use tiFy\Plugins\Shop\Contracts\UsersInterface;

class ShopServiceProvider extends AppServiceProvider
{
    /**
     * {@inheritdoc}
     */
    protected $singletons = [
        Shop::class,
        AddressesBilling::class,
        AddressesFormHandler::class,
        AddressesShipping::class,
        CartSessionItems::class,
        FunctionsPage::class,
        FunctionsPrice::class,
        FunctionsUrl::class,
        GatewaysCachOnDelivery::class,
        GatewaysCheque::class,
        GatewaysList::class,
    ];

    /**
     * Liste des services à instance multiples auto-déclarés.
     * @var array|string[]
     */
    protected $bindings = [
        CartLine::class,
        CartLineList::class,
        CartTotal::class,
        FunctionsDate::class,
        Order::class,
        OrderItem::class,
        OrderItems::class,
        OrderItemList::class,
        OrderItemTypeCoupon::class,
        OrderItemTypeFee::class,
        OrderItemTypeProduct::class,
        OrderItemTypeShipping::class,
        OrderItemTypeTax::class,
        OrderList::class,
        ProductItem::class,
        ProductList::class,
        ProductPurchasingOption::class,
        ProductsObjectTypeCategorized::class,
        ProductsObjectTypeUncategorized::class,
        UsersCustomer::class,
        UsersLoggedOut::class,
        UsersShopManager::class
    ];

    /**
     * Liste des alias de qualification des services fournis.
     * @var array
     */
    protected $aliases = [
        'shop.addresses.controller'            => Addresses::class,
        'shop.addresses.billing'               => AddressesBilling::class,
        'shop.addresses.form_handler'          => AddressesFormHandler::class,
        'shop.addresses.shipping'              => AddressesShipping::class,
        'shop.admin.controller'                => Admin::class,
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
        'shop.users.shop_manager'              => UsersShopManager::class,
    ];

    /**
     * Listes des noms de qualification de services instanciés automatiquement au démarrage.
     * @var array
     */
    protected $bootables = [
        'shop.addresses.controller',
        'shop.admin.controller',
        'shop.cart.controller',
        'shop.checkout.controller',
        'shop.custom_types.controller',
        'shop.functions.controller',
        'shop.gateways.controller',
        'shop.notices.controller',
        'shop.orders.controller',
        'shop.products.controller',
        'shop.session.controller',
        'shop.settings.controller',
        'shop.users.controller'
    ];

    /**
     * Listes des interfaces requises par les classe de surchage.
     * @var array
     */
    protected $contracts = [
        'shop.addresses.controller'       => AddressesInterface::class,
        'shop.addresses.billing'          => AddressBillingInterface::class,
        'shop.addresses.form_handler'     => AddressFormHandlerInterface::class,
        'shop.addresses.shipping'         => AddressShippingInterface::class,
        'shop.admin.controller'           => AdminInterface::class,
        'shop.cart.controller'            => CartInterface::class,
        'shop.cart.line'                  => CartLineInterface::class,
        'shop.cart.line_list'             => CartLineListInterface::class,
        'shop.cart.session_items'         => CartSessionItemsInterface::class,
        'shop.cart.total'                 => CartTotalInterface::class,
        'shop.checkout.controller'        => CheckoutInterface::class,
        'shop.custom_types.controller'    => CustomTypesInterface::class,
        'shop.functions.controller'       => FunctionsInterface::class,
        'shop.functions.date'             => FunctionsDateInterface::class,
        'shop.functions.page'             => FunctionsPageInterface::class,
        'shop.functions.price'            => FunctionsPriceInterface::class,
        'shop.functions.url'              => FunctionsUrlInterface::class,
        'shop.gateways.controller'        => GatewaysInterface::class,
        'shop.gateways.list'              => GatewayListInterface::class,
        'shop.notices.controller'         => NoticesInterface::class,
        'shop.orders.controller'          => OrdersInterface::class,
        'shop.orders.list'                => OrderListInterface::class,
        'shop.orders.order'               => OrderInterface::class,
        'shop.products.controller'        => ProductsInterface::class,
        'shop.products.item'              => ProductItemInterface::class,
        'shop.products.list'              => ProductListInterface::class,
        'shop.products.purchasing_option' => ProductPurchasingOptionInterface::class,
        'shop.session.controller'         => SessionInterface::class,
        'shop.settings.controller'        => SettingsInterface::class,
        'shop.users.controller'           => UsersInterface::class,
    ];

    /**
     * Listes des noms de qualification de services instanciés de manière différée.
     * @var array
     */
    protected $deferred = [
        'shop.addresses.billing',
        'shop.addresses.form_handler',
        'shop.addresses.shipping',
        'shop.cart.line',
        'shop.cart.line_list',
        'shop.cart.session_items',
        'shop.cart.total',
        'shop.functions.date',
        'shop.functions.page',
        'shop.functions.price',
        'shop.functions.url',
        'shop.gateway.cash_on_delivery',
        'shop.gateway.cheque',
        'shop.gateways.list',
        'shop.orders.order',
        'shop.orders.order_item',
        'shop.orders.order_items',
        'shop.orders.order_item_list',
        'shop.orders.order_item_type_coupon',
        'shop.orders.order_item_type_fee',
        'shop.orders.order_item_type_product',
        'shop.orders.order_item_type_shipping',
        'shop.orders.order_item_type_tax',
        'shop.orders.list',
        'shop.products.item',
        'shop.products.list',
        'shop.products.purchasing_option',
        'shop.products.type.categorized',
        'shop.products.type.uncategorized',
        'shop.users.customer',
        'shop.users.logged_out',
        'shop.users.shop_manager'
    ];

    /**
     * Liste des services personnalisés.
     * @var array
     */
    protected $customs = [];

    /**
     * Instance de l'accesseur.
     * @var Shop
     */
    protected $shop;

    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        $this->shop = $this->app->resolve(Shop::class);

        $this->customs = config('shop.service_provider');

        foreach($this->bootables as $abstract) :
            $this->app
                ->singleton(
                    $abstract,
                    function ($app) use ($abstract) {
                        $concrete = $this->getConcrete($abstract);

                        try {
                            $resolved = $concrete::make($abstract, $this->shop);
                        } catch (\Exception $e) {
                            wp_die();
                        }

                        if (isset($this->contracts[$abstract])) :
                            try {
                                $resolved instanceof $this->contracts[$abstract];
                            } catch (\Exception $e) {
                                throw new LogicException(
                                    sprintf(
                                        __('Le controleur de surcharge devrait être une instance de %s', 'tify'),
                                        $this->contracts[$abstract]
                                    ),
                                    500
                                );
                            }
                        endif;

                        return $resolved;
                    }
                );

            $resolved = $this->app->resolve($abstract);

            if ($resolved instanceof BootableControllerInterface) :
                add_action('tify_app_boot', [$resolved, 'boot'], 11);
            endif;
        endforeach;
    }

    /**
     * Récupération de la classe de rappel d'un service.
     *
     * @param string $alias Alias de qualification.
     *
     * @return string
     */
    public function getConcrete($abstract)
    {
        return isset($this->customs["shop.{$abstract}"])
            ? $this->customs["shop.{$abstract}"]
            : (
                isset($this->aliases[$abstract])
                ? $this->aliases[$abstract]
                : $abstract
            );
    }
}