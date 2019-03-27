<?php

use tiFy\Plugins\Shop\Shop;

if (!function_exists('shop')) {
    /**
     * Récupération de l'instance de la boutique
     *
     * @return string
     */
    function shop(): ?Shop
    {
        return app('shop');
    }
}


if (!function_exists('shop_action')) :
    /**
     * Récupération de l'url d'une action de traitement.
     *
     * @param string $alias Alias de qualification de l'action.
     * @param array $parameters Liste des variables passées en argument dans l'url.
     * @param boolean $absolute Format de sortie de l'url. Url relative par défaut.
     *
     * @return string
     */
    function shop_action($alias, $parameters = [], $absolute = false)
    {
        return shop()->action($alias, $parameters, $absolute);
    }
endif;

if (!function_exists('shop_cart_add_url')) :
    /**
     * Url de l'action d'un formulaire d'ajout d'un produit au panier d'achat
     * {@internal Requête de type POST; l'url doit être intégrée en tant qu'attribut "action" d'une balise d'ouverture
     * de formulaire ayant pour attribut "method" POST.}
     *
     * @param null|int|\WP_Post|\tiFy\Plugins\Shop\Products\ProductItem $product Identification du produit.
     * Produit de la page courante|Identifiant WP|Objet Post WP|Objet produit
     *
     * @return string
     */
    function shop_cart_add_url($product)
    {
        return shop()->cart()->addUrl($product);
    }
endif;

if (!function_exists('shop_cart_count')) :
    /**
     * Retourne le nombre de produit dans le panier.
     *
     * @param boolean $quantity Activation du calcul sur la base de la quantité de produits.
     * (défaut) true|false: Compte le nombre de ligne de produits.
     *
     * @return int
     */
    function shop_cart_count($quantity = true)
    {
        return $quantity ? shop()->cart()->countQuantity() : shop()->cart()->count();
    }
endif;

if (!function_exists('shop_cart_update_url')) :
    /**
     * Url d'action de mise à jour des produits du panier d'achat
     * {@internal Requête de type POST; l'url doit être intégrée en tant qu'attribut "action" d'une balise d'ouverture
     * de formulaire ayant pour attribut "method" POST.}
     *
     * @return string
     */
    function shop_cart_update_url()
    {
        return shop()->cart()->updateUrl();
    }
endif;

if (!function_exists('shop_cart_remove_url')) :
    /**
     * Url d'action de suppression d'un produit du panier d'achat
     *
     * @param string $key Identifiant de qualification de la ligne du panier a supprimer
     *
     * @return string
     */
    function shop_cart_remove_url($key)
    {
        return shop()->cart()->removeUrl($key);
    }
endif;

if (!function_exists('shop_checkout_process_url')) :
    /**
     * Url d'action d'exécution de la commande
     * {@internal Requête de type POST; l'url doit être intégrée en tant qu'attribut "action" d'une balise d'ouverture
     * de formulaire ayant pour attribut "method" POST.}
     *
     * @return string
     */
    function shop_checkout_process_url()
    {
        return shop()->checkout()->processUrl();
    }
endif;

if (!function_exists('shop_form_billing')) :
    /**
     * Formulaire d'adresse de facturation
     *
     * @return string
     */
    function shop_form_billing()
    {
        return shop()->addresses()->billing()->form();
    }
endif;

if (!function_exists('shop_notices')) :
    /**
     * Affichage de la liste des messages de notification
     *
     * @return string
     */
    function shop_notices()
    {
        return (string)shop()->notices();
    }
endif;

if (!function_exists('shop_page_is')) :
    /**
     * Verification du contexte d'affichage de la page courante
     *
     * @param string $name Nom de la page. shop|cart|checkout|terms.
     *
     * @return string
     */
    function shop_page_is($name)
    {
        return shop()->functions()->page()->is($name);
    }
endif;

if (!function_exists('shop_price_html')) :
    /**
     * Prix d'affichage au format HTML
     *
     * @param float $price Montant à afficher
     * @param string $format d'affichage
     *
     * @return string
     */
    function shop_price_html($price, $format = '')
    {
        return shop()->functions()->price()->html($price, $format);
    }
endif;

if (!function_exists('shop_product')) :
    /**
     * Récupération des données d'un produit existant.
     *
     * @param null|int|string|\WP_Post $product Identification du produit.
     * Produit de la page courante|ID WP|post_name WP|Objet Post WP|Objet produit courant.
     *
     * @return tiFy\Plugins\Shop\Contracts\ProductItemInterface
     */
    function shop_product($product)
    {
        return shop()->products()->getItem($product);
    }
endif;

if (!function_exists('shop_setting')) :
    /**
     * Récupération d'une option de configuration.
     *
     * @param string $key Identifiant de qualification de l'option
     * @param string $default Valeur de retour par défaut
     *
     * @return string
     */
    function shop_setting($key, $default = '')
    {
        return shop()->settings()->get($key, $default);
    }
endif;

if (!function_exists('shop_signin')) :
    /**
     * Récupération de l'authentification à la boutique.
     *
     * @return tiFy\Contracts\User\SigninFactory
     */
    function shop_signin()
    {
        return shop()->users()->signin();
    }
endif;

if (!function_exists('shop_url')) :
    /**
     * Url vers une page de la boutique
     *
     * @param string $name Nom de la page. shop|cart|checkout|terms.
     *
     * @return string
     */
    function shop_url($name)
    {
        return shop()->functions()->url()->page($name);
    }
endif;