<?php declare(strict_types=1);

use tiFy\Plugins\Shop\Shop;
use tiFy\Plugins\Shop\Contracts\Product;

if (!function_exists('shop')) {
    /**
     * Récupération de l'instance de la boutique
     *
     * @return Shop|null
     */
    function shop(): ?Shop
    {
        try {
            return Shop::instance();
        } catch (Exception $e) {
            return null;
        }
    }
}

if (!function_exists('shop_cart_add_url')) {
    /**
     * Url de l'action d'un formulaire d'ajout d'un produit au panier d'achat
     * {@internal Requête de type POST; l'url doit être intégrée en tant qu'attribut "action" d'une balise d'ouverture
     * de formulaire ayant pour attribut "method" POST.}
     *
     * @param null|int|WP_Post|Product $product Identification du produit.
     * Produit de la page courante|Identifiant WP|Objet Post WP|Objet produit
     *
     * @return string
     */
    function shop_cart_add_url($product): string
    {
        return shop()->cart()->addUrl($product);
    }
}

if (!function_exists('shop_cart_count')) {
    /**
     * Retourne le nombre de produit dans le panier.
     *
     * @param boolean $quantity Activation du calcul sur la base de la quantité de produits.
     * (défaut) true|false: Compte le nombre de ligne de produits.
     *
     * @return int
     */
    function shop_cart_count(bool $quantity = true): int
    {
        return $quantity ? shop()->cart()->quantity() : shop()->cart()->count();
    }
}

if (!function_exists('shop_cart_update_url')) {
    /**
     * Url d'action de mise à jour des produits du panier d'achat
     * {@internal Requête de type POST; l'url doit être intégrée en tant qu'attribut "action" d'une balise d'ouverture
     * de formulaire ayant pour attribut "method" POST.}
     *
     * @return string
     */
    function shop_cart_update_url(): string
    {
        return shop()->cart()->updateUrl();
    }
}

if (!function_exists('shop_cart_remove_url')) {
    /**
     * Url d'action de suppression d'un produit du panier d'achat
     *
     * @param string $key Identifiant de qualification de la ligne du panier a supprimer
     *
     * @return string
     */
    function shop_cart_remove_url(string $key): string
    {
        return ($line = shop()->cart()->get($key)) ? $line->removeUrl() : '';
    }
}

if (!function_exists('shop_checkout_handle_url')) {
    /**
     * Url d'action d'exécution de la commande
     * {@internal Requête de type POST; l'url doit être intégrée en tant qu'attribut "action" d'une balise d'ouverture
     * de formulaire ayant pour attribut "method" POST.}
     *
     * @return string
     */
    function shop_checkout_handle_url(): string
    {
        return shop()->checkout()->handleUrl();
    }
}

if (!function_exists('shop_notices')) {
    /**
     * Affichage de la liste des messages de notification
     *
     * @return string
     */
    function shop_notices(): string
    {
        return (string)shop()->notices();
    }
}

if (!function_exists('shop_price_html')) {
    /**
     * Prix d'affichage au format HTML
     *
     * @param float $price Montant à afficher
     * @param string $format d'affichage
     *
     * @return string
     */
    function shop_price_html(float $price, string $format = ''): string
    {
        return shop()->functions()->price()->html($price, $format);
    }
}

if (!function_exists('shop_product')) {
    /**
     * Récupération des données d'un produit existant.
     *
     * @param null|int|string|WP_Post $product Identification du produit.
     * Produit de la page courante|ID WP|post_name WP|Objet Post WP|Objet produit courant.
     *
     * @return Product
     */
    function shop_product($product): ?Product
    {
        return shop()->product($product);
    }
}