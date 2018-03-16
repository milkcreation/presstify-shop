<?php

namespace tiFy\Plugins\Shop\ServiceProvider;

use League\Container\ServiceProvider\AbstractServiceProvider;
use League\Container\ServiceProvider\BootableServiceProviderInterface;
use tiFy\App\Traits\App as TraitsApp;
use tiFy\Plugins\Shop\Addresses\Addresses;
use tiFy\Plugins\Shop\Admin\Admin;
use tiFy\Plugins\Shop\Cart\Cart;
use tiFy\Plugins\Shop\Checkout\Checkout;
use tiFy\Plugins\Shop\Gateways\Gateways;
use tiFy\Plugins\Shop\Notices\Notices;
use tiFy\Plugins\Shop\CustomTypes\CustomTypes;
use tiFy\Plugins\Shop\Orders\Orders;
use tiFy\Plugins\Shop\Products\Products;
use tiFy\Plugins\Shop\Providers\Providers;
use tiFy\Plugins\Shop\Session\Session;
use tiFy\Plugins\Shop\Settings\Settings;
use tiFy\Plugins\Shop\Shop;
use tiFy\Plugins\Shop\Users\Users;

class ServiceProvider extends AbstractServiceProvider implements BootableServiceProviderInterface
{
    use TraitsApp;

    /**
     * Classe de rappel de la boutique
     * @var Shop
     */
    protected $shop;

    /**
     * @var string[]
     */
    protected $provides = [
        'tify.plugins.shop.addresses',
        'tify.plugins.shop.admin',
        'tify.plugins.shop.cart',
        'tify.plugins.shop.checkout',
        'tify.plugins.shop.gateways',
        'tify.plugins.shop.notices',
        'tify.plugins.shop.custom-types',
        'tify.plugins.shop.orders',
        'tify.plugins.shop.products',
        'tify.plugins.shop.providers',
        'tify.plugins.shop.session',
        'tify.plugins.shop.settings',
        'tify.plugins.shop.users'
    ];

    /**
     * @var array
     */
    protected $maps = [
        'tify.plugins.shop.addresses'    => 'tiFy\Plugins\Shop\Addresses\Addresses',
        'tify.plugins.shop.admin'        => 'tiFy\Plugins\Shop\Admin\Admin',
        'tify.plugins.shop.cart'         => 'tiFy\Plugins\Shop\Cart\Cart',
        'tify.plugins.shop.checkout'     => 'tiFy\Plugins\Shop\Checkout\Checkout',
        'tify.plugins.shop.gateways'     => 'tiFy\Plugins\Shop\Gateways\Gateways',
        'tify.plugins.shop.notices'      => 'tiFy\Plugins\Shop\Notices\Notices',
        'tify.plugins.shop.custom-types' => 'tiFy\Plugins\Shop\CustomTypes\CustomTypes',
        'tify.plugins.shop.orders'       => 'tiFy\Plugins\Shop\Orders\Orders',
        'tify.plugins.shop.products'     => 'tiFy\Plugins\Shop\Products\Products',
        'tify.plugins.shop.providers'    => 'tiFy\Plugins\Shop\Providers\Providers',
        'tify.plugins.shop.session'      => 'tiFy\Plugins\Shop\Session\Session',
        'tify.plugins.shop.settings'     => 'tiFy\Plugins\Shop\Settings\Settings',
        'tify.plugins.shop.users'        => 'tiFy\Plugins\Shop\Users\Users'
    ];

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
    }

    /**
     *
     */
    public function boot()
    {
        foreach ($this->provides as $provide) :
            if (!isset($this->maps[$provide])) :
                continue;
            endif;
            $class = $this->maps[$provide];

            $this->getContainer()->share($provide, $class::make($this->shop));
        endforeach;
    }

    /**
     *
     */
    public function register()
    {

    }
}