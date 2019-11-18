<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop;

use tiFy\Container\ServiceProvider;
use tiFy\Plugins\Shop\Contracts\{
    Actions as ActionsContract,
    AddressBilling as AddressBillingContract,
    AddressFormHandler as AddressFormHandlerContract,
    AddressShipping as AddressShippingContract,
    Addresses as AddressesContract,
    Admin as AdminContract,
    Api as ApiContract,
    Cart as CartContract,
    CartLine as CartLineContact,
    CartLinesCollection as CartLinesCollectionContact,
    CartSessionItems as CartSessionItemsContact,
    CartTotal as CartTotalContract,
    Checkout as CheckoutContract,
    CustomTypes as CustomTypesContract,
    Functions as FunctionsContract,
    FunctionsDate as FunctionsDateContract,
    FunctionsPage as FunctionsPageContract,
    FunctionsPrice as FunctionsPriceContract,
    FunctionsUrl as FunctionsUrlContract,
    Gateway as GatewayContract,
    Gateways as GatewaysContract,
    Notices as NoticeContract,
    Order as OrderContract,
    OrderItem as OrderItemContract,
    OrderItemTypeCoupon as OrderItemTypeCouponContract,
    OrderItemTypeFee as OrderItemTypeFeeContract,
    OrderItemTypeProduct as OrderItemTypeProductContract,
    OrderItemTypeShipping as OrderItemTypeShippingContract,
    OrderItemTypeTax as OrderItemTypeTaxContract,
    OrderItems as OrderItemsContract,
    OrderItemsCollection as OrderItemsCollectionContract,
    Orders as OrdersContract,
    OrdersCollection as OrdersCollectionContract,
    Product as ProductContract,
    ProductPurchasingOption as ProductPurchasingOptionContract,
    ProductObjectTypeUncategorized,
    Products as ProductsContract,
    ProductsCollection as ProductsCollectionContract,
    Routes as RoutesContract,
    Session as SessionContract,
    Settings as SettingsContract,
    User as UserContract,
    UserCustomer as UserCustomerContract,
    UserShopManager as UserShopManagerContract,
    Users as UsersContract,
    Shop as ShopContract
};
use tiFy\Plugins\Shop\{
    Actions\Actions,
    Addresses\Addresses,
    Addresses\Billing as AddressesBilling,
    Addresses\FormHandler as AddressesFormHandler,
    Addresses\Shipping as AddressesShipping,
    Admin\Admin,
    Api\Api,
    Api\Endpoint\Orders as ApiEndpointOrders,
    Cart\Cart,
    Cart\Line as CartLine,
    Cart\LinesCollection as CartLinesCollection,
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
    Orders\OrderItems,
    Orders\OrderItem,
    Orders\OrderItemsCollection,
    Orders\OrderItemTypeCoupon,
    Orders\OrderItemTypeFee,
    Orders\OrderItemTypeProduct,
    Orders\OrderItemTypeShipping,
    Orders\OrderItemTypeTax,
    Orders\OrdersCollection,
    Products\ObjectType\Uncategorized as ProductsObjectTypeUncategorized,
    Products\Products,
    Products\Product,
    Products\ProductsCollection,
    Products\ProductPurchasingOption,
    Routing\Routes,
    Session\Session,
    Settings\Settings,
    Users\Customer as UserCustomer,
    Users\ShopManager as UserShopManager,
    Users\User,
    Users\Users
};

class ShopServiceProvider extends ServiceProvider
{
    /**
     * Liste des alias de qualification des services fournis.
     * @var array
     */
    protected $aliases = [
        'shop'                                    => Shop::class,
        'shop.actions'                            => Actions::class,
        'shop.addresses'                          => Addresses::class,
        'shop.addresses.billing'                  => AddressesBilling::class,
        'shop.addresses.form-handler'             => AddressesFormHandler::class,
        'shop.addresses.shipping'                 => AddressesShipping::class,
        'shop.admin'                              => Admin::class,
        'shop.api'                                => Api::class,
        'shop.api.endpoint.orders'                => ApiEndpointOrders::class,
        'shop.cart'                               => Cart::class,
        'shop.cart.line'                          => CartLine::class,
        'shop.cart.lines.collection'              => CartLinesCollection::class,
        'shop.cart.session-items'                 => CartSessionItems::class,
        'shop.cart.total'                         => CartTotal::class,
        'shop.checkout'                           => Checkout::class,
        'shop.functions'                          => Functions::class,
        'shop.functions.date'                     => FunctionsDate::class,
        'shop.functions.page'                     => FunctionsPage::class,
        'shop.functions.price'                    => FunctionsPrice::class,
        'shop.functions.url'                      => FunctionsUrl::class,
        'shop.gateway.cash_on_delivery'           => CashOnDeliveryGateway::class,
        'shop.gateway.cheque'                     => ChequeGateway::class,
        'shop.gateways'                           => Gateways::class,
        'shop.notices'                            => Notices::class,
        'shop.custom-types'                       => CustomTypes::class,
        'shop.order'                              => Order::class,
        'shop.order.item'                         => OrderItem::class,
        'shop.order.item.type.coupon'             => OrderItemTypeCoupon::class,
        'shop.order.item.type.fee'                => OrderItemTypeFee::class,
        'shop.order.item.type.product'            => OrderItemTypeProduct::class,
        'shop.order.item.type.shipping'           => OrderItemTypeShipping::class,
        'shop.order.item.type.tax'                => OrderItemTypeTax::class,
        'shop.order.items'                        => OrderItems::class,
        'shop.order.items.collection'             => OrderItemsCollection::class,
        'shop.orders'                             => Orders::class,
        'shop.orders.collection'                  => OrdersCollection::class,
        'shop.product'                            => Product::class,
        'shop.products'                           => Products::class,
        'shop.products.collection'                => ProductsCollection::class,
        'shop.products.purchasing_option'         => ProductPurchasingOption::class,
        'shop.products.object-type.uncategorized' => ProductsObjectTypeUncategorized::class,
        'shop.routing.routes'                     => Routes::class,
        'shop.session'                            => Session::class,
        'shop.settings'                           => Settings::class,
        'shop.user'                               => User::class,
        'shop.user.customer'                      => UserCustomer::class,
        'shop.user.shop-manager'                  => UserShopManager::class,
        'shop.users'                              => Users::class,
    ];

    /**
     * Liste des services personnalisés.
     * @var array
     */
    protected $customs = [];

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
        'checkout',
        'custom-types',
        'functions',
        'gateways',
        'notices',
        'orders',
        'products',
        'routing.routes',
        'session',
        'settings',
        'users',
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

            foreach ($this->resolve as $alias) {
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
    public function provides($alias = null)
    {
        $provides = array_keys($this->aliases);

        if (!is_null($alias)) {
            return (in_array($alias, $provides));
        }

        return $provides;
    }

    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->getContainer()->share('shop', function () {
            /** @var ShopContract $concrete */
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
        $this->getContainer()->share('shop.actions', function (): ActionsContract {
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

        $this->getContainer()->share('shop.addresses.billing', function (): AddressBillingContract {
            $concrete = $this->getConcrete('shop.addresses.billing');

            /** @var AddressBillingContract $instance */
            $instance = is_object($concrete) ? $concrete : new $concrete($this->getContainer()->get('shop'));

            return $instance;
        });

        $this->getContainer()->share('shop.addresses.shipping', function (): AddressShippingContract {
            $concrete = $this->getConcrete('shop.addresses.shipping');

            /** @var AddressShippingContract $instance */
            $instance = is_object($concrete) ? $concrete : new $concrete($this->getContainer()->get('shop'));

            return $instance;
        });

        $this->getContainer()->add('shop.addresses.form-handler', function (): AddressFormHandlerContract {
            $concrete = $this->getConcrete('shop.addresses.form-handler');

            /** @var AddressFormHandlerContract $instance */
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

        $this->getContainer()->add('shop.api.endpoint.orders', function () {
            $concrete = $this->getConcrete('shop.api.endpoint.orders');

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

        $this->getContainer()->add('shop.cart.lines.collection', function (): CartLinesCollectionContact {
            $concrete = $this->getConcrete('shop.cart.lines.collection');

            /** @var CartLinesCollectionContact $instance */
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
        $this->getContainer()->share('shop.checkout', function (): CheckoutContract {
            $concrete = $this->getConcrete('shop.checkout');

            /** @var CheckoutContract $instance */
            $instance = is_object($concrete) ? $concrete : new $concrete($this->getContainer()->get('shop'));

            return $instance;
        });
    }

    /**
     * Déclaration du controleur de définition des types.
     *
     * @return void
     */
    public function registerCustomTypes(): void
    {
        $this->getContainer()->share('shop.custom-types', function (): CustomTypesContract {
            $concrete = $this->getConcrete('shop.custom-types');

            /** @var CustomTypesContract $instance */
            $instance = is_object($concrete) ? $concrete : new $concrete($this->getContainer()->get('shop'));

            return $instance;
        });
    }

    /**
     * Déclaration des controleurs de fonctions.
     *
     * @return void
     */
    public function registerFunctions(): void
    {
        $this->getContainer()->share('shop.functions', function (): FunctionsContract {
            $concrete = $this->getConcrete('shop.functions');

            /** @var FunctionsContract $instance */
            $instance = is_object($concrete) ? $concrete : new $concrete($this->getContainer()->get('shop'));

            return $instance;
        });

        $this->getContainer()->add('shop.functions.date', function (): FunctionsDateContract {
            $concrete = $this->getConcrete('shop.functions.date');

            /** @var FunctionsDateContract $instance */
            $instance = is_object($concrete) ? $concrete : new $concrete($this->getContainer()->get('shop'));

            return $instance;
        });

        $this->getContainer()->share('shop.functions.page', function (): FunctionsPageContract {
            $concrete = $this->getConcrete('shop.functions.page');

            /** @var FunctionsPageContract $instance */
            $instance = is_object($concrete) ? $concrete : new $concrete($this->getContainer()->get('shop'));

            return $instance;
        });

        $this->getContainer()->share('shop.functions.price', function (): FunctionsPriceContract {
            $concrete = $this->getConcrete('shop.functions.price');

            /** @var FunctionsPriceContract $instance */
            $instance = is_object($concrete) ? $concrete : new $concrete($this->getContainer()->get('shop'));

            return $instance;
        });

        $this->getContainer()->share('shop.functions.url', function (): FunctionsUrlContract {
            $concrete = $this->getConcrete('shop.functions.url');

            /** @var FunctionsUrlContract $instance */
            $instance = is_object($concrete) ? $concrete : new $concrete($this->getContainer()->get('shop'));

            return $instance;
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
            $instance = is_object($concrete) ? $concrete : new $concrete($this->getContainer()->get('shop'));

            return $instance;
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
        $this->getContainer()->share('shop.notices', function (): NoticeContract {
            $concrete = $this->getConcrete('shop.notices');

            /** @var NoticeContract $instance */
            $instance = is_object($concrete) ? $concrete : new $concrete($this->getContainer()->get('shop'));

            return $instance;
        });
    }

    /**
     * Déclaration des controleurs de message de commande.
     *
     * @return void
     */
    public function registerOrders(): void
    {
        $this->getContainer()->add('shop.order', function ($id = null): OrderContract {
            $concrete = $this->getConcrete('shop.order');

            /** @var OrderContract $instance */
            $instance = is_object($concrete) ? $concrete : new $concrete();

            return is_null($id) ? $instance : $instance::create($id);
        });

        $this->getContainer()->add('shop.order.items', function (OrderContract $order): OrderItemsContract {
            $concrete = $this->getConcrete('shop.order.items');

            /** @var OrderItemsContract $instance */
            $instance = is_object($concrete) ? $concrete : new $concrete($order);

            return $instance;
        });

        $this->getContainer()->add(
            'shop.order.items.collection',
            function (OrderContract $order): OrderItemsCollectionContract {
                $concrete = $this->getConcrete('shop.order.items.collection');

                /** @var OrderItemsCollectionContract $instance */
                $instance = is_object($concrete) ? $concrete : new $concrete($order);

                return $instance;
            }
        );

        $this->getContainer()->add('shop.order.item', function (OrderContract $order): OrderItemContract {
            $concrete = $this->getConcrete('shop.order.item');

            /** @var OrderItemContract $instance */
            $instance = is_object($concrete) ? $concrete : new $concrete($order);

            return $instance;
        });

        $this->getContainer()->add(
            'shop.order.item.coupon',
            function (OrderItemContract $item): OrderItemTypeCouponContract {
                $concrete = $this->getConcrete('shop.order.item.coupon');

                /** @var OrderItemTypeCouponContract $instance */
                $instance = is_object($concrete) ? $concrete : new $concrete($item);

                return $instance;
            }
        );

        $this->getContainer()->add(
            'shop.order.item.fee',
            function (OrderItemContract $item): OrderItemTypeFeeContract {
                $concrete = $this->getConcrete('shop.order.item.fee');

                /** @var OrderItemTypeFeeContract $instance */
                $instance = is_object($concrete) ? $concrete : new $concrete($item);

                return $instance;
            });

        $this->getContainer()->add(
            'shop.order.item.product',
            function (OrderItemContract $item): OrderItemTypeProductContract {
                $concrete = $this->getConcrete('shop.order.item.product');

                /** @var OrderItemTypeProductContract $instance */
                $instance = is_object($concrete) ? $concrete : new $concrete($item);

                return $instance;
            }
        );

        $this->getContainer()->add(
            'shop.order.item.shipping',
            function (OrderItemContract $item): OrderItemTypeShippingContract {
                $concrete = $this->getConcrete('shop.order.item.shipping');

                /** @var OrderItemTypeShippingContract $instance */
                $instance = is_object($concrete) ? $concrete : new $concrete($item);

                return $instance;
            }
        );

        $this->getContainer()->add(
            'shop.order.item.tax',
            function (OrderItemContract $item): OrderItemTypeTaxContract {
                $concrete = $this->getConcrete('shop.order.item.tax');

                /** @var OrderItemTypeTaxContract $instance */
                $instance = is_object($concrete) ? $concrete : new $concrete($item);

                return $instance;
            }
        );

        $this->getContainer()->share('shop.orders', function (): OrdersContract {
            $concrete = $this->getConcrete('shop.orders');

            /** @var OrdersContract $instance */
            $instance = is_object($concrete) ? $concrete : new $concrete($this->getContainer()->get('shop'));

            return $instance;
        });

        $this->getContainer()->add('shop.orders.collection', function (): OrdersCollectionContract {
            $concrete = $this->getConcrete('shop.orders.collection');

            /** @var OrdersCollectionContract $instance */
            $instance = is_object($concrete) ? $concrete : new $concrete($this->getContainer()->get('shop'));

            return $instance;
        });
    }

    /**
     * Déclaration des controleurs de produits.
     *
     * @return void
     */
    public function registerProducts(): void
    {
        $this->getContainer()->add('shop.product', function ($id = null): ?ProductContract {
            $concrete = $this->getConcrete('shop.product');

            /** @var ProductContract $instance */
            $instance = is_object($concrete) ? $concrete : new $concrete();

            return is_null($id) ? $instance : $instance::create($id);
        });

        $this->getContainer()->add(
            'shop.product.purchasing-option',
            function (string $name, array $attrs, ProductContract $product): ProductPurchasingOptionContract {
                $concrete = $this->getConcrete('shop.product.purchasing-option');

                /** @var ProductPurchasingOptionContract $instance */
                $instance = is_object($concrete) ? $concrete : new $concrete(
                    $name, $attrs, $product
                );

                return $instance;
            }
        );

        $this->getContainer()->share('shop.products', function (): ProductsContract {
            $concrete = $this->getConcrete('shop.products');

            /** @var ProductsContract $instance */
            $instance = is_object($concrete) ? $concrete : new $concrete($this->getContainer()->get('shop'));

            return $instance;
        });

        $this->getContainer()->add('shop.products.collection', function (): ProductsCollectionContract {
            $concrete = $this->getConcrete('shop.products.collection');

            /** @var ProductsCollectionContract $instance */
            $instance = is_object($concrete) ? $concrete : new $concrete($this->getContainer()->get('shop'));

            return $instance;
        });

        $this->getContainer()->add(
            'shop.products.object-type.uncategorized',
            function (string $name, array $attrs): ProductObjectTypeUncategorized {
                $concrete = $this->getConcrete('shop.products.object-type.uncategorized');

                /** @var ProductObjectTypeUncategorized $instance */
                $instance = is_object($concrete) ? $concrete : new $concrete(
                    $name, $attrs, $this->getContainer()->get('shop')
                );

                return $instance;
            }
        );
    }

    /**
     * Déclaration des controleurs de routage.
     *
     * @return void
     */
    public function registerRouting(): void
    {
        $this->getContainer()->share('shop.routing.routes', function (): RoutesContract {
            $concrete = $this->getConcrete('shop.routing.routes');

            /** @var RoutesContract $instance */
            $instance = is_object($concrete) ? $concrete : new $concrete($this->getContainer()->get('shop'));

            return $instance;
        });
    }

    /**
     * Déclaration du controleur de session.
     *
     * @return void
     */
    public function registerSession(): void
    {
        $this->getContainer()->share('shop.session', function (): SessionContract {
            $concrete = $this->getConcrete('shop.session');

            /** @var SessionContract $instance */
            $instance = is_object($concrete) ? $concrete : new $concrete($this->getContainer()->get('shop'));

            return $instance;
        });
    }

    /**
     * Déclaration du controleur des réglages de la boutique.
     *
     * @return void
     */
    public function registerSettings(): void
    {
        $this->getContainer()->share('shop.settings', function (): SettingsContract {
            $concrete = $this->getConcrete('shop.settings');

            /** @var SettingsContract $instance */
            $instance = is_object($concrete) ? $concrete : new $concrete($this->getContainer()->get('shop'));

            return $instance;
        });
    }

    /**
     * Déclaration des controleurs utilisateurs.
     *
     * @return void
     */
    public function registerUsers(): void
    {
        $this->getContainer()->share('shop.users', function (): UsersContract {
            $concrete = $this->getConcrete('shop.users');

            /** @var UsersContract $instance */
            $instance = is_object($concrete) ? $concrete : new $concrete($this->getContainer()->get('shop'));

            return $instance;
        });

        $this->getContainer()->add('shop.user.customer', function ($id = null): ?UserCustomerContract {
            $concrete = $this->getConcrete('shop.user.customer');

            /** @var UserCustomerContract $instance */
            $instance = is_object($concrete) ? $concrete : new $concrete();

            return is_null($id) ? $instance : $instance::create($id);
        });

        $this->getContainer()->add('shop.user', function ($id = null): UserContract {
            $concrete = $this->getConcrete('shop.user');

            /** @var UserContract $instance */
            $instance = is_object($concrete) ? $concrete : new $concrete();

            return is_null($id) ? $instance : $instance::create($id);
        });

        $this->getContainer()->add('shop.user.shop-manager', function ($id = null): ?UserShopManagerContract {
            $concrete = $this->getConcrete('shop.user.shop-manager');

            /** @var UserShopManagerContract $instance */
            $instance = is_object($concrete) ? $concrete : new $concrete();

            return is_null($id) ? $instance : $instance::create($id);
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