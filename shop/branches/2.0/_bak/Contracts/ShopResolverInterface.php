<?php

namespace tiFy\Plugins\Shop__Bak\Contracts;

interface ShopResolverInterface
{
    /**
     * Récupération de l'url d'une action de traitement.
     *
     * @param string $alias Alias de qualification de l'action.
     * @param array $parameters Liste des variables passées en argument dans l'url.
     * @param boolean $absolute Format de sortie de l'url. Url relative par défaut.
     *
     * @return string
     */
    public function action($alias, $parameters = [], $absolute = false);

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
     *
     * @param null|string $key Attribut de configuration. Syntaxe à point autorisée pour accéder
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
     * Récupération du fournisseur de services.
     *
     * @return \tiFy\Plugins\Shop\ShopServiceProvider
     */
    public function provider();

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
     * @return SettingsInterface
     */
    public function settings();

    /**
     * Récupération de l'instance de l'utilisateur.
     *
     * @param int|null $id
     *
     * @return UserItemInterface|UserCustomerInterface|UserShopManagerInterface|UserLoggedOutInterface
     */
    public function user(?int $id = null);

    /**
     * Récupération de la dépendance des utilisateurs de la boutique.
     *
     * @return UsersInterface
     */
    public function users();
}