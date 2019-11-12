<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop\Contracts;

use WP_Post;

interface Cart extends ShopAwareTrait
{
    /**
     * Ajout d'une ligne au panier
     *
     * @param string $key Identifiant de qualification de la ligne
     * @param array $attributes Liste des attributs de la ligne
     *
     * @return Cart
     */
    public function add(string $key, array $attributes): Cart;

    /**
     * Traitement de l'ajout d'un produit au panier
     *
     * @param string $product_name Identifiant de qualification d'url (Slug) du produit
     *
     * @return mixed
     */
    public function addHandler($product_name);

    /**
     * Url d'action d'ajout d'un produit au panier d'achat
     * @internal Requête de type POST; l'url doit être intégrée en tant qu'attribut "action" d'une balise d'ouverture de
     * formulaire ayant pour attribut "method" POST
     *
     * @param null|int|WP_Post|Product $product Identification du produit.
     * Produit de la page courante|Identifiant WP|Objet Post WP|Objet produit
     *
     * @return string
     */
    public function addUrl($product): string;

    /**
     * Initialisation de la classe.
     *
     * @return void
     */
    public function boot(): void;

    /**
     * Calcul des totaux basés sur le contenu du panier
     *
     * @return CartTotal
     */
    public function calculate(): CartTotal;

    /**
     * Compte le nombre de ligne du panier
     *
     * @return int
     */
    public function count(): int;

    /**
     * Compte la quantité de produits contenus dans le panier.
     *
     * @return int
     */
    public function countQuantity(): int;

    /**
     * Détruit complétement le panier.
     *
     * @return void
     */
    public function destroy(): void;

    /**
     * Vide la liste complète des lignes du panier.
     *
     * @return void
     */
    public function flush(): void;

    /**
     * Compte le poids que représente l'ensemble des ligne de produits du panier
     *
     * @return float
     */
    public function getProductsWeight(): float;

    /**
     * Récupération d'un message de notification
     *
     * @param string $name Nom de qualification du message de notification.
     * @internal successfully_added|successfully_updated|successfully_removed|is_empty
     * @param string $default Valeur de retour par défaut
     *
     * @return string
     */
    public function getNotice(string $name, string $default = ''): string;

    /**
     * Initialisation des messages de notification
     *
     * @return void
     */
    public function initNotices(): void;

    /**
     * Vérifie si le panier est vide
     *
     * @return boolean
     */
    public function isEmpty(): bool;

    /**
     * Récupération d'une ligne du panier
     *
     * @param string $key Identifiant de qualification de la ligne
     *
     * @return CartLine|null
     */
    public function line($key): ?CartLine;

    /**
     * Récupération de l'instance du gestionnaires des lignes du panier.
     *
     * @return CartLinesCollection|CartLine[]
     */
    public function lines(): CartLinesCollection;

    /**
     * Vérifie si le panier nécessite un paiement.
     *
     * @return boolean
     */
    public function needPayment(): bool;

    /**
     * Vérifie si le panier nécessite une livraison.
     *
     * @return boolean
     */
    public function needShipping(): bool;

    /**
     * Suppression d'une ligne du panier
     *
     * @param string $key Identifiant de qualification de la ligne
     *
     * @return mixed
     */
    public function remove(string $key);

    /**
     * Traitement de la suppression d'un produit du panier
     *
     * @param string $key Identifiant de qualification de la ligne du panier à supprimer
     *
     * @return mixed
     */
    public function removeHandler(string $key);

    /**
     * Url d'action de suppression d'un produit du panier d'achat
     *
     * @param string $key Identifiant de qualification de la ligne du panier a supprimer
     *
     * @return string
     */
    public function removeUrl(string $key): string;

    /**
     * Récupération de la classe de rappel de gestion des éléments du panier d'achat stocké en session.
     *
     * @return CartSessionItems
     */
    public function sessionItems(): CartSessionItems;


    /**
     * Récupération de l'instance du total
     *
     * @return CartTotal|null
     */
    public function total(): ?CartTotal;

    /**
     * Mise à jour d'une ligne du panier
     *
     * @param string $key Identifiant de qualification de la ligne
     * @param array $attributes Liste des attributs de la ligne
     *
     * @return CartLinesCollection
     */
    public function update(string $key, array $attributes): CartLinesCollection;

    /**
     * Url d'action de mise à jour des produits du panier d'achat
     * {@internal Requête de type POST; l'url doit être intégrée en tant qu'attribut "action" d'une balise d'ouverture
     * de formulaire ayant pour attribut "method" POST.}
     *
     * @return string
     */
    public function updateUrl(): string;

    /**
     * Traitement de la mise à jour des produits du panier
     *
     * @return void
     */
    public function updateHandler();
}