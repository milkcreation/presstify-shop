<?php

namespace tiFy\Plugins\Shop\Interop;

use tiFy\Core\User\Session\StoreInterface as tiFySession;
use tiFy\Plugins\Shop\Addresses\Addresses;
use tiFy\Plugins\Shop\Cart\Cart;
use tiFy\Plugins\Shop\Checkout\Checkout;
use tiFy\Plugins\Shop\Functions\Functions;
use tiFy\Plugins\Shop\Gateways\Gateways;
use tiFy\Plugins\Shop\Notices\Notices;
use tiFy\Plugins\Shop\Orders\Orders;
use tiFy\Plugins\Shop\Products\Products;
use tiFy\Plugins\Shop\Settings\Settings;
use tiFy\Plugins\Shop\Shop;
use tiFy\Plugins\Shop\Users\Users;

trait ShopTraits
{
    /**
     * Classe de rappel de la boutique
     * @var Shop
     */
    protected $shop;

    /**
     * Récupération de la classe de rappel de gestion des adresses : livraison|facturation
     *
     * @return object|Addresses
     */
    public function addresses()
    {
        return $this->shop->provide('addresses.controller');
    }

    /**
     * Récupération de la dépendance panier
     *
     * @return object|Cart
     */
    public function cart()
    {
        return $this->shop->provide('cart.controller');
    }

    /**
     * Récupération de la dépendance commande
     *
     * @return object|Checkout
     */
    public function checkout()
    {
        return $this->shop->provide('checkout.controller');
    }

    /**
     * Récupération de la dépendance des fournisseurs de service
     *
     * @return object|Functions
     */
    public function functions()
    {
        return $this->shop->provide('functions.controller');
    }

    /**
     * Récupération de la dépendance commande
     *
     * @return object|Gateways
     */
    public function gateways()
    {
        return $this->shop->provide('gateways.controller');
    }

    /**
     * Récupération de la classe de rappel de gestion des commandes
     *
     * @return object|Orders
     */
    public function orders()
    {
        return $this->shop->provide('orders.controller');
    }

    /**
     * Récupération de la classe de rappel de gestion des produits
     *
     * @return object|Products
     */
    public function products()
    {
        return $this->shop->provide('products.controller');
    }

    /**
     * Récupération de la dépendance des notices
     *
     * @return object|Notices
     */
    public function notices()
    {
        return $this->shop->provide('notices.controller');
    }

    /**
     * Récupération de la classe de rappel de récupération de données de session
     *
     * @return object|tiFySession
     */
    public function session()
    {
        /** @var tiFySession $session */
        $session = $this->shop->provide('session.controller');

        return $session;
    }

    /**
     * Récupération de la dépendance des réglages de la boutique
     *
     * @return object|Settings
     */
    public function settings()
    {
        return $this->shop->provide('settings.controller');
    }

    /**
     * Récupération de la dépendance des utilisateurs de la boutique
     *
     * @return object|Users
     */
    public function users()
    {
        return $this->shop->provide('users.controller');
    }
}