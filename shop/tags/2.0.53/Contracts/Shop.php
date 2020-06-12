<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop\Contracts;

use tiFy\Contracts\Container\Container;
use tiFy\Support\ParamsBag;
use WP_Post;

interface Shop
{
    /**
     * Récupération de l'instance de la boutique.
     *
     * @return static|null
     */
    public static function instance(): ?Shop;

    /**
     * Initialisation.
     *
     * @return static
     */
    public function boot(): Shop;

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
     * Récupération de paramètre|Définition de paramètres|Instance du gestionnaire de paramètre.
     *
     * @param string|array|null $key Clé d'indice du paramètre à récupérer|Liste des paramètre à définir.
     * @param mixed $default Valeur de retour par défaut lorsque la clé d'indice est une chaine de caractère.
     *
     * @return mixed|ParamsBag
     */
    public function config($key = null, $default = null);

    /**
     * Récupération de l'instance de la gestion des entités de la boutique.
     *
     * @return ShopEntity
     */
    public function entity(): ShopEntity;

    /**
     * Récupération du gestionnaire de formulaires.
     *
     * @return Form
     */
    public function form(): Form;

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
     * Récupération d'une instance de commande.
     *
     * @param int|string|WP_Post|null.
     *
     * @return Order|null
     */
    public function order($id = null): ?Order;

    /**
     * Récupération de la classe de rappel de gestion des commandes.
     *
     * @param array|null $args Liste des arguments de requête de récupération des commandes.
     *
     * @return Orders|Order[]|null
     */
    public function orders(?array $args = null);

    /**
     * Récupération d'une instance de produit.
     *
     * @param int|string|WP_Post|null.
     *
     * @return Product|null
     */
    public function product($id = null): ?Product;

    /**
     * Récupération de l'instance du gestionnaire de produits|Récupération d'une liste d'instance de produits.
     *
     * @param array|null $args Liste des arguments de requête de récupération des produits.
     *
     * @return Products|Product[]|null
     */
    public function products(?array $args = null);

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
     * Résolution de service fournis.
     *
     * @param string $alias
     * @param array ...$args Liste d'arguments dynamiques complémentaires.
     *
     * @return mixed
     */
    public function resolve(string $alias, ...$args);

    /**
     * Vérifie si un service est fourni.
     *
     * @param string $alias Nom de qualification du service.
     *
     * @return boolean
     */
    public function resolvable(string $alias): bool;

    /**
     * Récupération du chemin absolu vers le répertoire des ressources.
     *
     * @param string|null $path Chemin relatif d'une resource (répertoire|fichier).
     *
     * @return string
     */
    public function resources(string $path = null): string;

    /**
     * Récupération du gestionnaire de routage.
     *
     * @return Route
     */
    public function route(): Route;

    /**
     * Récupération de la classe de rappel de récupération de données de session.
     *
     * @return Session
     */
    public function session(): Session;

    /**
     * Définition des paramètres de configuration.
     *
     * @param array $attrs Liste des attributs de configuration.
     *
     * @return static
     */
    public function setConfig(array $attrs): Shop;

    /**
     * Définition du conteneur d'injection de dépendances.
     *
     * @param Container $container
     *
     * @return static
     */
    public function setContainer(Container $container): Shop;

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
     * Récupération d'un gabarit d'affichage.
     *
     * @param string $name
     * @param array $data
     *
     * @return string
     */
    public function view(string $name, array $data = []);
}