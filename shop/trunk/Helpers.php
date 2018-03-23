<?php

/**
 * @desc Fonctions d'aide à la saisie
 * @package presstiFy
 * @version 1.1
 * @subpackage Core
 * @since 1.2.596
 *
 * @author Jordy Manner <jordy@tigreblanc.fr>
 * @copyright Milkcreation
 */

use tiFy\Plugins\Shop\Shop;

/**
 * Url de l'action d'un formulaire d'ajout d'un produit au panier d'achat
 * @internal Requête de type POST; l'url doit être intégrée en tant qu'attribut "action" d'une balise d'ouverture de formulaire ayant pour attribut "method" POST
 *
 * @param null|int|\WP_Post|\tiFy\Plugins\Shop\Products\ProductItemInterface $product Identification du produit. Produit de la page courante|Identifiant WP|Objet Post WP|Objet produit
 *
 * @return string
 */
function tify_shop_cart_add_url($product)
{
    return Shop::get()->cart()->addUrl($product);
}

/**
 * Url d'action de mise à jour des produits du panier d'achat
 * @internal Requête de type POST; l'url doit être intégrée en tant qu'attribut "action" d'une balise d'ouverture de formulaire ayant pour attribut "method" POST
 *
 * @return string
 */
function tify_shop_cart_update_url()
{
    return Shop::get()->cart()->updateUrl();
}

/**
 * Url d'action de suppression d'un produit du panier d'achat
 *
 * @param string $key Identifiant de qualification de la ligne du panier a supprimer
 *
 * @return string
 */
function tify_shop_cart_remove_url($key)
{
    return Shop::get()->cart()->removeUrl($key);
}

/**
 * Url d'action d'exécution de la commande
 * @internal Requête de type POST; l'url doit être intégrée en tant qu'attribut "action" d'une balise d'ouverture de formulaire ayant pour attribut "method" POST
 *
 * @return string
 */
function tify_shop_checkout_process_url()
{
    return Shop::get()->checkout()->processUrl();
}

/**
 * Affichage de la liste des messages de notification
 *
 * @return void
 */
function tify_shop_notices()
{
    echo (string)Shop::get()->notices();
}

/**
 * Récupération d'une option de configuration
 *
 * @param string $key Identifiant de qualification de l'option
 * @param string $default Valeur de retour par défaut
 *
 * @return string
 */
function tify_shop_setting($key, $default = '')
{
    return Shop::get()->settings()->get($key, $default);
}

/**
 * Verification du contexte d'affichage de la page courante
 *
 * @param string $name Nom de la page. shop|cart|checkout|terms.
 *
 * @return string
 */
function tify_shop_page_is($name)
{
    return Shop::get()->functions()->page()->is($name);
}

/**
 * Prix d'affichage au format HTML
 *
 * @param float $price Montant à afficher
 * @param string $format d'affichage
 *
 * @return string
 */
function tify_shop_price_html($price, $format = '')
{
    return Shop::get()->functions()->price()->html($price, $format);
}

/**
 * Url vers une page de la boutique
 *
 * @param string $name Nom de la page. shop|cart|checkout|terms.
 *
 * @return string
 */
function tify_shop_url($name)
{
    return Shop::get()->functions()->url()->page($name);
}

/**
 * Formulaire d'adresse de facturation
 *
 * @return void
 */
function tify_shop_billing_form()
{
    echo Shop::get()->addresses()->billing()->form();
}