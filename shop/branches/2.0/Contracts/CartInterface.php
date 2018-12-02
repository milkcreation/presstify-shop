<?php

namespace tiFy\Plugins\Shop\Contracts;

interface CartInterface extends BootableControllerInterface, ShopResolverInterface
{
    /**
     * Ajout d'une ligne au panier
     *
     * @param string $key Identifiant de qualification de la ligne
     * @param array $attributes Liste des attributs de la ligne
     *
     * @return CartLineListInterface
     */
    public function add($key, $attributes);

    /**
     * Traitement de l'ajout d'un produit au panier
     *
     * @param string $product_name Identifiant de qualification d'url (Slug) du produit
     *
     * @return void
     */
    public function addHandler($product_name);

    /**
     * Url d'action d'ajout d'un produit au panier d'achat
     * @internal Requête de type POST; l'url doit être intégrée en tant qu'attribut "action" d'une balise d'ouverture de
     * formulaire ayant pour attribut "method" POST
     *
     * @param null|int|\WP_Post|ProductItemInterface $product Identification du produit.
     * Produit de la page courante|Identifiant WP|Objet Post WP|Objet produit
     *
     * @return string
     */
    public function addUrl($product);

    /**
     * Calcul des totaux basés sur le contenu du panier
     *
     * @return CartTotalInterface
     */
    public function calculate();

    /**
     * Compte le nombre de ligne du panier
     *
     * @return int
     */
    public function count();

    /**
     * Compte la quantité de produits contenus dans le panier.
     *
     * @return int
     */
    public function countQuantity();

    /**
     * Détruit complétement le panier.
     *
     * @return void
     */
    public function destroy();

    /**
     * Vide la liste complète des lignes du panier.
     *
     * @return void
     */
    public function flush();

    /**
     * Récupération d'une ligne du panier
     *
     * @param string $key Identifiant de qualification de la ligne
     *
     * @return array|CartLineInterface
     */
    public function get($key);

    /**
     * Compte le poids que représente l'ensemble des ligne de produits du panier
     *
     * @return float
     */
    public function getProductsWeight();

    /**
     * Récupération de la liste des lignes du panier
     *
     * @return array|CartLineInterface[]
     */
    public function getList();

    /**
     * Récupération d'un message de notification
     *
     * @param string $name Nom de qualification du message de notification.
     * @internal successfully_added|successfully_updated|successfully_removed|is_empty
     * @param string $default Valeur de retour par défaut
     *
     * @return string
     */
    public function getNotice($name, $default = '');

    /**
     * Récupération des totaux
     *
     * @return CartTotalInterface
     */
    public function getTotals();

    /**
     * Initialisation des messages de notification
     *
     * @return void
     */
    public function initNotices();

    /**
     * Vérifie si le panier est vide
     *
     * @return bool
     */
    public function isEmpty();

    /**
     * Récupération de la classe de rappel de gestion des lignes du panier.
     *
     * @return CartLineListInterface|CartLineInterface[]
     */
    public function lines();

    /**
     * Vérifie si le panier nécessite un paiement.
     *
     * @return bool
     */
    public function needPayment();

    /**
     * Vérifie si le panier nécessite une livraison.
     *
     * @return bool
     */
    public function needShipping();

    /**
     * Suppression d'une ligne du panier
     *
     * @param string $key Identifiant de qualification de la ligne
     *
     * @return mixed
     */
    public function remove($key);

    /**
     * Traitement de la suppression d'un produit du panier
     *
     * @param string $key Identifiant de qualification de la ligne du panier à supprimer
     *
     * @return bool
     */
    public function removeHandler($key);

    /**
     * Url d'action de suppression d'un produit du panier d'achat
     *
     * @param string $key Identifiant de qualification de la ligne du panier a supprimer
     *
     * @return string
     */
    public function removeUrl($key);

    /**
     * Récupération de la classe de rappel de gestion des éléments du panier d'achat stocké en session.
     *
     * @return CartSessionItemsInterface
     */
    public function sessionItems();

    /**
     * Mise à jour d'une ligne du panier
     *
     * @param string $key Identifiant de qualification de la ligne
     * @param array $attributes Liste des attributs de la ligne
     *
     * @return CartLineList
     */
    public function update($key, $attributes);

    /**
     * Url d'action de mise à jour des produits du panier d'achat
     * @internal Requête de type POST; l'url doit être intégrée en tant qu'attribut "action" d'une balise d'ouverture de formulaire ayant pour attribut "method" POST
     *
     * @param null|int|\WP_Post|\tiFy\Plugins\Shop\Products\ProductItem $product Identification du produit. Produit de la page courante|Identifiant WP|Objet Post WP|Objet produit
     *
     * @return string
     */
    public function updateUrl();

    /**
     * Traitement de la mise à jour des produits du panier
     *
     * @return void
     */
    public function updateHandler();
}