<?php

namespace tiFy\Plugins\Shop\Settings;

use tiFy\Plugins\Shop\Shop;

interface SettingsInterface
{
    /**
     * Instanciation de la classe.
     * @param Shop $shop Classe de rappel de la boutique.
     * @return self
     */
    public static function make(Shop $shop);

    /**
     * Adresse de la boutique - ligne 1.
     * @return string
     */
    public function storeAddress();

    /**
     * Adresse de la boutique - ligne 2.
     * @return string
     */
    public function storeAddressAdditionnal();

    /**
     * Ville de la boutique.
     * @return string
     */
    public function storeCity();

    /**
     * Code postal de la boutique.
     * @return string
     */
    public function storePostcode();

    /**
     * Pays de la boutique.
     * @return string
     */
    public function storeCountry();

    /**
     * Liste des pays de vente.
     * @return array
     */
    public function allowedCountries();

    /**
     * Liste des pays de livraison.
     * @return array
     */
    public function shippingCountries();

    /**
     * Adresse par défaut du client.
     * @return string
     */
    public function defaultCustomerAddress();

    /**
     * Activation et calcul de la TVA.
     * @return bool
     */
    public function isCalcTaxes();

    /**
     * Devise monétaire des tarifs.
     * @return string
     */
    public function currency();

    /**
     * Position de la devise pour l'affichage des tarifs.
     * @return string left|right|left_space|right_space
     */
    public function currencyPosition();

    /**
     * Séparateur des milliers.
     * @return string
     */
    public function thousandSeparator();

    /**
     * Séparateur des décimales.
     * @return string
     */
    public function decimalSeparator();

    /**
     * Nombre de décimales.
     * @return string
     */
    public function decimalNumber();

    /**
     * Identifiant de qualification de la page d'affichage de l'accueil de la boutique.
     * @return int
     */
    public function shopPageId();

    /**
     * Identifiant de qualification de la page d'affichage du panier.
     * @return int
     */
    public function cartPageId();

    /**
     * Identifiant de qualification de la page de commande.
     * @return int
     */
    public function checkoutPageId();

    /**
     * Identifiant de qualification de la page des conditions générales de vente.
     * @return int
     */
    public function termsPageId();

    /**
     * Redirection vers le panier après l'ajout d'un article.
     * @return bool
     */
    public function cartRedirectAfterAdd();

    /**
     * Activation du bouton d'ajout au panier depuis les pages listes.
     * @return bool
     */
    public function cartEnableListAdd();

    /**
     * Unité de poids.
     * @return string
     */
    public function weightUnit();

    /**
     * Unité de dimension.
     * @return string
     */
    public function dimensionUnit();

    /**
     * Activation de la gestion des stocks.
     * @return bool
     */
    public function isManageStock();

    /**
     * Vérifie si les tarifs des produits saisie inclus la TVA (prix TTC ou HT).
     * @return bool
     */
    public function isPricesIncludeTax();

    /**
     * Vérifie si le calculateur de frais du panier est actif.
     * @return bool
     */
    public function isShippingCalcEnabled();

    /**
     * Vérifie s'il faut masquer les frais de livraison tant qu'aucune adresse de livraison n'est renseignée.
     * @return bool
     */
    public function isShippingCostRequiresAddress();

    /**
     * Destination de livraison.
     * @return string shipping|billing|billing_only
     */
    public function shipToDestination();

    /**
     * Activation du mode de débogage de la livraison.
     * @return bool
     */
    public function isShippingDebugMode();
}