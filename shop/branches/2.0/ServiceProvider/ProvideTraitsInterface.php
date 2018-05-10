<?php

namespace tiFy\Plugins\Shop\ServiceProvider;

use tiFy\User\Session\StoreInterface as tiFySession;
use tiFy\Plugins\Shop\Addresses\Addresses;
use tiFy\Plugins\Shop\Cart\Cart;
use tiFy\Plugins\Shop\Checkout\Checkout;
use tiFy\Plugins\Shop\Functions\Functions;
use tiFy\Plugins\Shop\Gateways\Gateways;
use tiFy\Plugins\Shop\Notices\Notices;
use tiFy\Plugins\Shop\Orders\Orders;
use tiFy\Plugins\Shop\Products\Products;
use tiFy\Plugins\Shop\Settings\Settings;
use tiFy\Plugins\Shop\Users\Users;

interface ProvideTraitsInterface
{
    /**
     * Récupération des données de configuration de la boutique.
     * @param null|string $key  Attribut de configuration. Syntaxe à point autorisée pour accéder
     *                          aux sous niveau d'un tableau.
     *                          Renvoie la liste complète des attributs de configuration si null.
     * @param mixed $default Valeur de retour par défaut.
     * @return mixed
     */
    public function config($key = null, $default = '');

    /**
     * Récupération d'un service fournit par la boutique.
     * @param string $name Identifiant de qualification du service
     * @param array $args Liste des variables passées en argument au service
     * @return object
     */
    public function provide($name, $args = []);

    /**
     * Récupération de la classe de rappel de gestion des adresses : livraison|facturation.
     * @return Addresses
     */
    public function addresses();

    /**
     * Récupération de la dépendance panier.
     * @return Cart
     */
    public function cart();

    /**
     * Récupération de la dépendance commande.
     * @return Checkout
     */
    public function checkout();

    /**
     * Récupération de la dépendance des fournisseurs de service.
     * @return Functions
     */
    public function functions();

    /**
     * Récupération de la dépendance commande.
     * @return Gateways
     */
    public function gateways();

    /**
     * Récupération de la classe de rappel de gestion des commandes.
     * @return Orders
     */
    public function orders();

    /**
     * Récupération de la classe de rappel de gestion des produits.
     * @return Products
     */
    public function products();

    /**
     * Récupération de la dépendance des notices.
     * @return Notices
     */
    public function notices();

    /**
     * Récupération de la classe de rappel de récupération de données de session.
     * @return tiFySession
     */
    public function session();

    /**
     * Récupération de la dépendance des réglages de la boutique.
     * @return Settings
     */
    public function settings();

    /**
     * Récupération de la dépendance des utilisateurs de la boutique.
     * @return Users
     */
    public function users();
}