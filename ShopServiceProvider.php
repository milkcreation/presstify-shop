<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop;

use tiFy\Container\ServiceProvider;
use tiFy\Plugins\Shop\Contracts\{
    AddressesForm as AddressesFormContract,
    Admin as AdminContract,
    Api as ApiContract,
    ApiController as ApiControllerContract,
    ApiMiddleware as ApiMiddlewareContract,
    Cart as CartContract,
    CartController as CartControllerContract,
    CartDiscount as CartDiscountContact,
    CartLine as CartLineContact,
    CartSession as CartSessionContact,
    CartTotal as CartTotalContract,
    Checkout as CheckoutContract,
    CheckoutController as CheckoutControllerContract,
    Form as FormContract,
    Functions as FunctionsContract,
    DateFunctions as FunctionsDateContract,
    PageFunctions as FunctionsPageContract,
    PriceFunctions as FunctionsPriceContract,
    Gateway as GatewayContract,
    Gateways as GatewaysContract,
    Notices as NoticeContract,
    Order as OrderContract,
    OrderItem as OrderItemContract,
    OrderItemCoupon as OrderItemCouponContract,
    OrderItemDiscount as OrderItemDiscountContract,
    OrderItemFee as OrderItemFeeContract,
    OrderItemProduct as OrderItemProductContract,
    OrderItemShipping as OrderItemShippingContract,
    OrderItemTax as OrderItemTaxContract,
    Orders as OrdersContract,
    OrdersCollection as OrdersCollectionContract,
    Product as ProductContract,
    ProductPurchasingOption as ProductPurchasingOptionContract,
    Products as ProductsContract,
    ProductObjectType as ProductObjectTypeContract,
    ProductsCollection as ProductsCollectionContract,
    Route as RouteContract,
    Session as SessionContract,
    Settings as SettingsContract,
    Shop as ShopContract,
    ShopEntity as EntityContract,
    User as UserContract,
    UserCustomer as UserCustomerContract,
    Users as UsersContract,
    UserShopManager as UserShopManagerContract,
};
use tiFy\Support\Proxy\{Request, User, View};

class ShopServiceProvider extends ServiceProvider
{
    /**
     * Liste des alias de qualification des services fournis.
     * @var array
     */
    protected $aliases = [
        'shop'                            => Shop::class,
        'shop.admin'                      => Admin\Admin::class,
        'shop.api'                        => Api\Api::class,
        'shop.api.endpoint.orders'        => Api\Endpoint\Orders::class,
        'shop.cart'                       => Cart\Cart::class,
        'shop.cart.discount'              => Cart\Discount::class,
        'shop.cart.line'                  => Cart\Line::class,
        'shop.cart.session'               => Cart\Session::class,
        'shop.cart.total'                 => Cart\Total::class,
        'shop.checkout'                   => Checkout\Checkout::class,
        'shop.controller.api'             => Controller\ApiController::class,
        'shop.controller.cart'            => Controller\CartController::class,
        'shop.controller.checkout'        => Controller\CheckoutController::class,
        'shop.entity'                     => ShopEntity::class,
        'shop.form'                       => Form\Form::class,
        'shop.form.addresses'             => Form\AddressesForm::class,
        'shop.functions'                  => Functions\Functions::class,
        'shop.functions.date'             => Functions\DateFunctions::class,
        'shop.functions.page'             => Functions\PageFunctions::class,
        'shop.functions.price'            => Functions\PriceFunctions::class,
        'shop.gateway.cash_on_delivery'   => Gateways\CashOnDeliveryGateway::class,
        'shop.gateway.cheque'             => Gateways\ChequeGateway::class,
        'shop.gateways'                   => Gateways\Gateways::class,
        'shop.middleware.api'             => Middleware\ApiMiddleware::class,
        'shop.notices'                    => Notices\Notices::class,
        'shop.order'                      => Orders\Order::class,
        'shop.order.item.common'          => Orders\OrderItemCommon::class,
        'shop.order.item.coupon'          => Orders\OrderItemCoupon::class,
        'shop.order.item.discount'        => Orders\OrderItemDiscount::class,
        'shop.order.item.fee'             => Orders\OrderItemFee::class,
        'shop.order.item.product'         => Orders\OrderItemProduct::class,
        'shop.order.item.shipping'        => Orders\OrderItemShipping::class,
        'shop.order.item.tax'             => Orders\OrderItemTax::class,
        'shop.orders'                     => Orders\Orders::class,
        'shop.orders.collection'          => Orders\OrdersCollection::class,
        'shop.product'                    => Products\Product::class,
        'shop.products'                   => Products\Products::class,
        'shop.products.collection'        => Products\ProductsCollection::class,
        'shop.products.purchasing_option' => Products\ProductPurchasingOption::class,
        'shop.products.object-type'       => Products\ProductObjectType::class,
        'shop.route'                      => Route\Route::class,
        'shop.session'                    => Session\Session::class,
        'shop.settings'                   => Settings\Settings::class,
        'shop.user'                       => Users\User::class,
        'shop.user.customer'              => Users\Customer::class,
        'shop.user.shop-manager'          => Users\ShopManager::class,
        'shop.users'                      => Users\Users::class,
    ];

    /**
     * Liste des services personnalisés.
     * @var array
     */
    protected $customs = [];

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
        add_action('after_setup_theme', function () {
            $providers = config('shop.providers', []);

            array_walk($providers, function ($value, $key) {
                if ($key === 'shop') {
                    $this->customs['shop'] = $value;
                } else {
                    $this->customs["shop.{$key}"] = $value;
                }
            });

            events()->listen('shop.booted', function (Shop $shop) {
                if ($orderId = Request::instance()->query->getInt('order-received', 0)) {
                    $orderKey = Request::input('key', '');


                    if (($order = $shop->order($orderId)) && ($order->getOrderKey() === $orderKey)) {
                        $shop->cart()->destroy();
                    }
                }

                if (
                    ($orderId = (int)$shop->session()->get('order_awaiting_payment', 0)) &&
                    ($order = $shop->order($orderId)) && !$order->hasStatus($shop->orders()->getNotEmptyCartStatuses())
                ) {
                    $shop->cart()->destroy();
                }
            });

            $this->getContainer()->get('shop')->boot();
        });
    }

    /**
     * Récupération de la classe de rappel d'un service.
     *
     * @param string $alias Alias de qualification.
     *
     * @return object|string
     */
    public function getConcrete(string $alias)
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
            $concrete = $this->getConcrete('shop');

            /** @var ShopContract $concrete */
            $instance = is_object($concrete) ? $concrete : new $concrete();

            return $instance->setContainer($this->getContainer())->setConfig(config('shop', []));
        });

        $this->registerAdmin();
        $this->registerApi();
        $this->registerCart();
        $this->registerCheckout();
        $this->registerController();
        $this->registerEntity();
        $this->registerForm();
        $this->registerFunctions();
        $this->registerGateways();
        $this->registerMiddleware();
        $this->registerNotices();
        $this->registerOrders();
        $this->registerProducts();
        $this->registerRoute();
        $this->registerSession();
        $this->registerSettings();
        $this->registerUsers();
        $this->registerView();
    }

    /**
     * Déclaration du gestionnaire d'interface d'administration.
     *
     * @return void
     */
    public function registerAdmin(): void
    {
        $this->getContainer()->share('shop.admin', function (): AdminContract {
            return $this->resolveShared('admin');
        });
    }

    /**
     * Déclaration des gestionnaire d'API REST.
     *
     * @return void
     */
    public function registerApi(): void
    {
        $this->getContainer()->share('shop.api', function (): ApiContract {
            return $this->resolveShared('api');
        });

        $this->getContainer()->add('shop.api.endpoint.orders', function () {
            return $this->resolveAdded('api.endpoint.orders');
        });
    }

    /**
     * Déclaration des gestionnaire du panier de commande.
     *
     * @return void
     */
    public function registerCart(): void
    {
        $this->getContainer()->share('shop.cart', function (): CartContract {
            return $this->resolveShared('cart');
        });

        $this->getContainer()->add('shop.cart.discount', function (): CartDiscountContact {
            return $this->resolveAdded('cart.discount');
        });

        $this->getContainer()->add('shop.cart.line', function (): CartLineContact {
            return $this->resolveAdded('cart.line')->parse();
        });

        $this->getContainer()->share('shop.cart.session', function (): CartSessionContact {
            return $this->resolveShared('cart.session')->parse();
        });

        $this->getContainer()->add('shop.cart.total', function (): CartTotalContract {
            return $this->resolveAdded('cart.total')->parse();
        });
    }

    /**
     * Déclaration du gestionnaire de traitement des paiements.
     *
     * @return void
     */
    public function registerCheckout(): void
    {
        $this->getContainer()->share('shop.checkout', function (): CheckoutContract {
            return $this->resolveShared('checkout');
        });
    }

    /**
     * Déclaration des controleurs.
     *
     * @return void
     */
    public function registerController(): void
    {
        $this->getContainer()->share('shop.controller.api', function (): ApiControllerContract {
            return $this->resolveShared('controller.api');
        });

        $this->getContainer()->share('shop.controller.cart', function (): CartControllerContract {
            return $this->resolveShared('controller.cart');
        });

        $this->getContainer()->share('shop.controller.checkout', function (): CheckoutControllerContract {
            return $this->resolveShared('controller.checkout');
        });
    }

    /**
     * Déclaration du gestionnaire d'entités.
     *
     * @return void
     */
    public function registerEntity(): void
    {
        $this->getContainer()->share('shop.entity', function (): EntityContract {
            return $this->resolveShared('entity')->boot();
        });
    }

    /**
     * Déclaration des formulaires.
     *
     * @return void
     */
    public function registerForm(): void
    {
        $this->getContainer()->share('shop.form', function (): FormContract {
            return $this->resolveShared('form');
        });

        $this->getContainer()->share('shop.form.addresses', function (): AddressesFormContract {
            return $this->resolveShared('form.addresses');
        });
    }

    /**
     * Déclaration des fonctions.
     *
     * @return void
     */
    public function registerFunctions(): void
    {
        $this->getContainer()->share('shop.functions', function (): FunctionsContract {
            return $this->resolveShared('functions');
        });

        $this->getContainer()->add('shop.functions.date', function (...$args): FunctionsDateContract {
            return $this->resolveAdded('functions.date', ...$args);
        });

        $this->getContainer()->share('shop.functions.page', function (): FunctionsPageContract {
            return $this->resolveShared('functions.page');
        });

        $this->getContainer()->share('shop.functions.price', function (): FunctionsPriceContract {
            return $this->resolveShared('functions.price');
        });
    }

    /**
     * Déclaration des getionnaires de plateforme de paiement.
     *
     * @return void
     */
    public function registerGateways(): void
    {
        $this->getContainer()->share('shop.gateways', function (): GatewaysContract {
            return $this->resolveShared('gateways')->boot();
        });

        $this->getContainer()->add('shop.gateway.cash_on_delivery', function (): GatewayContract {
            return $this->resolveAdded('gateway.cash_on_delivery');
        });

        $this->getContainer()->add('shop.gateway.cheque', function (): GatewayContract {
            return $this->resolveAdded('gateway.cheque');
        });
    }

    /**
     * Déclaration des middlewares.
     *
     * @return void
     */
    public function registerMiddleware(): void
    {
        $this->getContainer()->share('shop.middleware.api', function (): ApiMiddlewareContract {
            return $this->resolveShared('middleware.api');
        });
    }

    /**
     * Déclaration du gestionnaire de message de notification.
     *
     * @return void
     */
    public function registerNotices(): void
    {
        $this->getContainer()->share('shop.notices', function (): NoticeContract {
            return $this->resolveShared('notices');
        });
    }

    /**
     * Déclaration des controleurs de message de commande.
     *
     * @return void
     */
    public function registerOrders(): void
    {
        $this->getContainer()->add('shop.order', function ($id = null): ?OrderContract {
            $concrete = $this->getConcrete('shop.order');

            /** @var OrderContract $instance */
            $instance = is_object($concrete) ? $concrete : new $concrete();

            return is_null($id) ? $instance : $instance::create($id);
        });

        $this->getContainer()->add('shop.order.item.common', function (): OrderItemContract {
            return $this->resolveAdded('order.item.common');
        });

        $this->getContainer()->add('shop.order.item.coupon', function (): OrderItemCouponContract {
            return $this->resolveAdded('order.item.coupon');
        });

        $this->getContainer()->add('shop.order.item.discount', function (): OrderItemDiscountContract {
            return $this->resolveAdded('order.item.discount');
        });

        $this->getContainer()->add('shop.order.item.fee', function (): OrderItemFeeContract {
            return $this->resolveAdded('order.item.fee');
        });

        $this->getContainer()->add('shop.order.item.product', function (): OrderItemProductContract {
            return $this->resolveAdded('order.item.product');
        });

        $this->getContainer()->add('shop.order.item.shipping', function (): OrderItemShippingContract {
            return $this->resolveAdded('order.item.shipping');
        });

        $this->getContainer()->add('shop.order.item.tax', function (): OrderItemTaxContract {
            return $this->resolveAdded('order.item.tax');
        });

        $this->getContainer()->share('shop.orders', function (): OrdersContract {
            return $this->resolveShared('orders');
        });

        $this->getContainer()->add('shop.orders.collection', function (): OrdersCollectionContract {
            return $this->resolveAdded('orders.collection');
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
            return $this->resolveShared('products')->boot();
        });

        $this->getContainer()->add('shop.products.collection', function (): ProductsCollectionContract {
            return $this->resolveAdded('products.collection');
        });

        $this->getContainer()->add('shop.products.object-type',
            function (string $name, array $attrs): ProductObjectTypeContract {
                $concrete = $this->getConcrete('shop.products.object-type');

                return (is_object($concrete) ? $concrete : new $concrete($name, $attrs))
                    ->setShop($this->getContainer()->get('shop'));
            }
        );
    }

    /**
     * Déclaration du gestionnaire de routage.
     *
     * @return void
     */
    public function registerRoute(): void
    {
        $this->getContainer()->share('shop.route', function (): RouteContract {
            return $this->resolveShared('route')->boot();
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
            return $this->resolveShared('session');
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
            return $this->resolveShared('settings')->parse();
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
            /** @var ShopContract $shop */
            $shop = $this->getContainer()->get('shop');

            $concrete = $this->getConcrete('shop.users');

            /** @var UsersContract $instance */
            $instance = (is_object($concrete) ? $concrete : new $concrete())
                ->setShop($this->getContainer()->get('shop'));

            foreach ($shop->config('roles', []) as $name => $attrs) {
                User::getInstance()->role()->register($name, $attrs);
            }

            return $instance;
        });

        $this->getContainer()->add('shop.user.customer', function ($id = null): ?UserCustomerContract {
            $concrete = $this->getConcrete('shop.user.customer');

            /** @var UserCustomerContract $instance */
            $instance = is_object($concrete) ? $concrete : new $concrete();

            return is_null($id) ? $instance : $instance::create($id);
        });

        $this->getContainer()->add('shop.user', function ($id = null): ?UserContract {
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
    public function registerView(): void
    {
        $this->getContainer()->share('shop.view', function () {
            $default_dir = __DIR__ . '/Resources/views';

            return View::getPlatesEngine(array_merge([
                'directory'    => $default_dir,
                'factory'      => ShopView::class,
                'override_dir' => ($dir = config('shop.view.override_dir')) && is_dir($dir) ? $dir : $default_dir,
            ]))->setParams(['shop' => $this->getContainer()->get('shop')]);
        });
    }

    /**
     * Récupération de la classe de rappel d'un service.
     *
     * @param string $alias
     * @param array ...$args
     *
     * @return object|mixed
     */
    public function resolveAdded(string $alias, array ...$args): object
    {
        $concrete = $this->getConcrete("shop.{$alias}");

        if(is_object($concrete)) {
            $instance = clone $concrete;
        } else {
            $instance = new $concrete(...$args);
        }

        return $instance->setShop($this->getContainer()->get('shop'));
    }

    /**
     * Récupération de la classe de rappel d'un service.
     *
     * @param string $alias
     *
     * @return object|mixed
     */
    public function resolveShared(string $alias): object
    {
        $concrete = $this->getConcrete("shop.{$alias}");

        return (is_object($concrete) ? $concrete : new $concrete())->setShop($this->getContainer()->get('shop'));
    }
}