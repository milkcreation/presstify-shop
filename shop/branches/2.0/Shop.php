<?php

/**
 * @name Shop
 * @desc Extension PresstiFy de gestion de boutique en ligne.
 * @author Jordy Manner <jordy@milkcreation.fr>
 * @package presstiFy
 * @namespace \tiFy\Plugins\Shop
 * @version 2.0.0
 */

namespace tiFy\Plugins\Shop;

use League\Container\Exception\NotFoundException;
use tiFy\Apps\AppController;
use tiFy\User\Session\StoreInterface as tiFySession;
use tiFy\Plugins\Shop\Addresses\Addresses;
use tiFy\Plugins\Shop\Cart\Cart;
use tiFy\Plugins\Shop\Checkout\Checkout;
use tiFy\Plugins\Shop\Functions\Functions;
use tiFy\Plugins\Shop\Gateways\Gateways;
use tiFy\Plugins\Shop\Notices\Notices;
use tiFy\Plugins\Shop\Orders\Orders;
use tiFy\Plugins\Shop\Products\Products;
use tiFy\Plugins\Shop\ServiceProvider\ServiceProvider;
use tiFy\Plugins\Shop\Settings\Settings;
use tiFy\Plugins\Shop\Users\Users;

final class Shop extends AppController
{
    /**
     * Fournisseur de service
     * @var ServiceProvider
     */
    protected $provider;

    /**
     * CONSTRUCTEUR
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        // Déclaration du fournisseur de services.
        $this->provider = new ServiceProvider($this->appConfig('service_provider', []), $this);
        $this->appServiceProvider($this->provider);
    }

    /**
     * Récupération de l'instance de la classe.
     *
     * @return object|self
     */
    public static function get()
    {
        try {
            return self::tFyAppGetContainer(__CLASS__);
        } catch(NotFoundException $e) {
            wp_die($e->getMessage(), '', $e->getCode());
            exit;
        }
    }

    /**
     * Récupération du fournisseur de service.
     *
     * @return ServiceProvider
     */
    public function provider()
    {
        return $this->provider;
    }

    /**
     * Récupération d'un service fournit par la boutique.
     *
     * @param string $name Identifiant de qualification du service
     * @param array $args Liste des variables passées en argument au service
     *
     * @return object
     */
    public function provide($name, $args = [])
    {
        return $this->provider()->get($name, $args);
    }

    /**
     * Récupération de la classe de rappel de gestion des adresses : livraison|facturation
     *
     * @return object|Addresses
     */
    public function addresses()
    {
        return $this->provide('addresses.controller');
    }

    /**
     * Récupération de la dépendance panier
     *
     * @return object|Cart
     */
    public function cart()
    {
        return $this->provide('cart.controller');
    }

    /**
     * Récupération de la dépendance commande
     *
     * @return object|Checkout
     */
    public function checkout()
    {
        return $this->provide('checkout.controller');
    }

    /**
     * Récupération de la dépendance des fournisseurs de service
     *
     * @return object|Functions
     */
    public function functions()
    {
        return $this->provide('functions.controller');
    }

    /**
     * Récupération de la dépendance commande
     *
     * @return object|Gateways
     */
    public function gateways()
    {
        return $this->provide('gateways.controller');
    }

    /**
     * Récupération de la classe de rappel de gestion des commandes
     *
     * @return object|Orders
     */
    public function orders()
    {
        return $this->provide('orders.controller');
    }

    /**
     * Récupération de la classe de rappel de gestion des produits
     *
     * @return object|Products
     */
    public function products()
    {
        return $this->provide('products.controller');
    }

    /**
     * Récupération de la dépendance des notices
     *
     * @return object|Notices
     */
    public function notices()
    {
        return $this->provide('notices.controller');
    }

    /**
     * Récupération de la classe de rappel de récupération de données de session
     *
     * @return object|tiFySession
     */
    public function session()
    {
        /** @var tiFySession $session */
        $session = $this->provide('session.controller');

        return $session;
    }

    /**
     * Récupération de la dépendance des réglages de la boutique
     *
     * @return object|Settings
     */
    public function settings()
    {
        return $this->provide('settings.controller');
    }

    /**
     * Récupération de la dépendance des utilisateurs de la boutique
     *
     * @return object|Users
     */
    public function users()
    {
        return $this->provide('users.controller');
    }
}
