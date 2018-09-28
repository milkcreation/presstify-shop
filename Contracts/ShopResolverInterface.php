<?php

namespace tiFy\Plugins\Shop\Contracts;

use tiFy\Contracts\App\AppInterface;
use tiFy\Plugins\Shop\Contracts\AddressesInterface;
use tiFy\Plugins\Shop\Contracts\AdminInterface;
use tiFy\Plugins\Shop\Contracts\CartInterface;
use tiFy\Plugins\Shop\Contracts\CheckoutInterface;
use tiFy\Plugins\Shop\Contracts\CustomTypesInterface;
use tiFy\Plugins\Shop\Contracts\FunctionsInterface;
use tiFy\Plugins\Shop\Contracts\GatewaysInterface;
use tiFy\Plugins\Shop\Contracts\NoticesInterface;
use tiFy\Plugins\Shop\Contracts\OrdersInterface;
use tiFy\Plugins\Shop\Contracts\ProductsInterface;
use tiFy\Plugins\Shop\Contracts\SessionInterface;
use tiFy\Plugins\Shop\Contracts\SettingsInterface;
use tiFy\Plugins\Shop\Contracts\UsersInterface;
use tiFy\Plugins\Shop\Shop;
use tiFy\User\Session\Session as tiFyUserSession;

interface ShopResolverInterface
{
    /**
     * Récupération de la classe de rappel de gestion des adresses : livraison|facturation.
     *
     * @return AddressesInterface
     */
    public function addresses();

    /**
     * Récupération de la dépendance panier.
     *
     * @return CartInterface
     */
    public function cart();

    /**
     * Récupération de la dépendance commande.
     *
     * @return CheckoutInterface
     */
    public function checkout();

    /**
     * Récupération des données de configuration de la boutique.
     * @param null|string $key  Attribut de configuration. Syntaxe à point autorisée pour accéder
     *                          aux sous niveau d'un tableau.
     *                          Renvoie la liste complète des attributs de configuration si null.
     * @param mixed $default Valeur de retour par défaut.
     *
     * @return mixed
     */
    public function config($key = null, $default = '');

    /**
     * Récupération de la dépendance des fournisseurs de service.
     *
     * @return FunctionsInterface
     */
    public function functions();

    /**
     * Récupération de la dépendance commande.
     *
     * @return GatewaysInterface
     */
    public function gateways();

    /**
     * Récupération de la classe de rappel de gestion des commandes.
     *
     * @return OrdersInterface
     */
    public function orders();

    /**
     * Récupération de la classe de rappel de gestion des produits.
     *
     * @return ProductsInterface
     */
    public function products();

    /**
     * Récupération de la dépendance des notices.
     *
     * @return NoticesInterface
     */
    public function notices();

    /**
     * Récupération de la classe de rappel de récupération de données de session.
     *
     * @return SessionInterface
     */
    public function session();

    /**
     * Récupération de la dépendance des réglages de la boutique.
     *
     * @return tiFyUserSession
     */
    public function settings();

    /**
     * Récupération de la dépendance des utilisateurs de la boutique.
     *
     * @return UsersInterface
     */
    public function users();
}