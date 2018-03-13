<?php

/*
 Plugin Name: Shop
 Plugin URI: http://presstify.com/plugins/shop
 Description: boutique
 Version: 1.0.0
 Author: Milkcreation
 Author URI: http://milkcreation.fr
 Text Domain: tify
*/

namespace tiFy\Plugins\Shop;

use League\Container\Exception\NotFoundException;
use tiFy\App\Plugin;
use tiFy\Core\User\Session\StoreInterface as tiFySession;
use tiFy\Plugins\Shop\Addresses\Addresses;
use tiFy\Plugins\Shop\Admin\Admin;
use tiFy\Plugins\Shop\Cart\Cart;
use tiFy\Plugins\Shop\Checkout\Checkout;
use tiFy\Plugins\Shop\Gateways\Gateways;
use tiFy\Plugins\Shop\Notices\Notices;
use tiFy\Plugins\Shop\CustomTypes\CustomTypes;
use tiFy\Plugins\Shop\Products\Products;
use tiFy\Plugins\Shop\Providers\Providers;
use tiFy\Plugins\Shop\Session\Session;
use tiFy\Plugins\Shop\Settings\Settings;
use tiFy\Plugins\Shop\Users\Users;

class Shop extends Plugin
{
    /**
     * CONSTRUCTEUR
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        // Déclaration des dépendances
        $this->appShareContainer('tify.plugins.shop.addresses', Addresses::make($this));
        $this->appShareContainer('tify.plugins.shop.admin', Admin::make($this));
        $this->appShareContainer('tify.plugins.shop.cart', Cart::make($this));
        $this->appShareContainer('tify.plugins.shop.checkout', Checkout::make($this));
        $this->appShareContainer('tify.plugins.shop.custom-types', CustomTypes::make($this));
        $this->appShareContainer('tify.plugins.shop.gateways', Gateways::make($this));
        $this->appShareContainer('tify.plugins.shop.notices', Notices::make($this));
        $this->appShareContainer('tify.plugins.shop.products', Products::make($this));
        $this->appShareContainer('tify.plugins.shop.providers', Providers::make($this));
        $this->appShareContainer('tify.plugins.shop.session', Session::make($this));
        $this->appShareContainer('tify.plugins.shop.settings', Settings::make($this));
        $this->appShareContainer('tify.plugins.shop.users', Users::make($this));

        require_once($this->appDirname() . '/Helpers.php');
    }

    /**
     * Récupération de dépendance
     *
     * @param string $name Identifiant de qualification de la dépendance
     *
     * @return null|object|self|Addresses|Admin|Cart|Checkout|CustomTypes|Gateways|Notices|Products|Providers|Session|Settings|Users
     */
    public static function get($name = null)
    {
        try {
            /** @var Shop $Shop */
            $Shop = self::tFyAppGetContainer('tiFy\Plugins\Shop\Shop');
        } catch(NotFoundException $e) {
            wp_die($e->getMessage(), '', $e->getCode());
        }
        if (!$name) :
            return $Shop;
        else :
            $name = $Shop->appLowerName($name);
            if ($Shop->appHasContainer("tify.plugins.shop.{$name}")) :
                /** @var \League\Container\Container $Factory */
                $Factory = $Shop->appGetContainer("tify.plugins.shop.{$name}");

                return $Factory;
            endif;
        endif;

        return null;
    }

    /**
     * Récupération de la classe de rappel de gestion des adresses : livraison|facturation
     *
     * @return Addresses
     */
    public function addresses()
    {
        return self::get('addresses');
    }

    /**
     * Récupération de la dépendance panier
     *
     * @return Cart
     */
    public function cart()
    {
        return self::get('cart');
    }

    /**
     * Récupération de la dépendance commande
     *
     * @return Checkout
     */
    public function checkout()
    {
        return self::get('checkout');
    }

    /**
     * Récupération de la dépendance commande
     *
     * @return Gateways
     */
    public function gateways()
    {
        return self::get('gateways');
    }

    /**
     * Récupération de la classe de rappel de gestion des produits
     *
     * @return Products
     */
    public function products()
    {
        return self::get('products');
    }

    /**
     * Récupération de la dépendance des fournisseurs de service
     *
     * @return Providers
     */
    public function providers()
    {
        return self::get('providers');
    }

    /**
     * Récupération de la dépendance des notices
     *
     * @return Notices
     */
    public function notices()
    {
        return self::get('notices');
    }

    /**
     * Récupération de la classe de rappel de récupération de données de session
     *
     * @return tiFySession
     */
    public function session()
    {
        /** @var tiFySession $session */
        $session = self::get('session');

        return $session;
    }

    /**
     * Récupération de la dépendance des réglages de la boutique
     *
     * @return Settings
     */
    public function settings()
    {
        return self::get('settings');
    }

    /**
     * Récupération de la dépendance des utilisateurs de la boutique
     *
     * @return Users
     */
    public function users()
    {
        return self::get('users');
    }
}
