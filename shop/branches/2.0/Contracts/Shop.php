<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop\Contracts;

use Psr\Container\ContainerInterface as Container;

interface Shop
{
    /**
     * Récupération de l'instance de la boutique.
     *
     * @return static|null
     */
    public static function instance(): ?Shop;

    /**
     * Récupération de l'url d'une action de traitement.
     *
     * @param string $alias Alias de qualification de l'action.
     * @param array $parameters Liste des variables passées en argument dans l'url.
     * @param boolean $absolute Format de sortie de l'url. Url relative par défaut.
     *
     * @return string
     */
    public function action($alias, $parameters = [], $absolute = false): string;

    /**
     * Récupération de la classe de rappel de gestion des adresses : livraison|facturation.
     *
     * @return Addresses
     */
    public function addresses(): Addresses;

    /**
     * Récupération de la dépendance panier.
     *
     * @return Cart
     */
    public function cart(): Cart;

    /**
     * Récupération de la dépendance commande.
     *
     * @return Checkout
     */
    public function checkout(): Checkout;

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
     * @return Functions
     */
    public function functions(): Functions;

    /**
     * Récupération de la dépendance commande.
     *
     * @return Gateways
     */
    public function gateways(): Gateways;

    /**
     * Récupération du conteneur d'injection de dépendance.
     *
     * @return Container
     */
    public function getContainer(): Container;

    /**
     * Récupération de la classe de rappel de gestion des commandes.
     *
     * @return Orders
     */
    public function orders(): Orders;

    /**
     * Récupération de la classe de rappel de gestion des produits.
     *
     * @return Products
     */
    public function products(): Products;

    /**
     * Récupération du fournisseur de services.
     *
     * @return \tiFy\Plugins\Shop\ShopServiceProvider
     */
    public function provider();

    /**
     * Récupération de la dépendance des notices.
     *
     * @return Notices
     */
    public function notices(): Notices;

    /**
     * Récupération de la classe de rappel de récupération de données de session.
     *
     * @return Session
     */
    public function session(): Session;

    /**
     * Récupération de la dépendance des réglages de la boutique.
     *
     * @return Settings
     */
    public function settings(): Settings;

    /**
     * Récupération de l'instance de l'utilisateur.
     *
     * @param int|null $id
     *
     * @return UserCustomer|UserShopManager|User
     */
    public function user(?int $id = null): ?User;

    /**
     * Récupération de la dépendance des utilisateurs de la boutique.
     *
     * @return Users
     */
    public function users(): Users;

    /**
     * Résolution de service fournis.
     *
     * @param string $alias
     * @param array ...$args Liste d'arguments dynamiques complémentaires.
     *
     * @return mixed
     */
    public function resolve(string $alias, ...$args);

    /**
     * Récupération du chemin absolu vers une ressource.
     *
     * @param string $path Chemin relatif vers un sous élément.
     *
     * @return string
     */
    public function resourcesDir(string $path = ''): string;

    /**
     * Récupération de l'url absolue vers une ressource.
     *
     * @param string $path Chemin relatif vers un sous élément.
     *
     * @return string
     */
    public function resourcesUrl(string $path = ''): string;
}