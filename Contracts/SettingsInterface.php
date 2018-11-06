<?php

namespace tiFy\Plugins\Shop\Contracts;

use tiFy\Contracts\Kernel\ParamsBag;

interface SettingsInterface extends BootableControllerInterface, ParamsBag, ShopResolverInterface
{
    /**
     * Liste des pays de vente.
     *
     * @return string|array
     */
    public function allowedCountries();

    /**
     * Activation du bouton d'ajout au panier depuis les pages listes.
     *
     * @return bool
     */
    public function cartEnableListAdd();

    /**
     * Identifiant de qualification de la page d'affichage du panier.
     *
     * @return int
     */
    public function cartPageId();

    /**
     * Redirection vers le panier après l'ajout d'un article.
     *
     * @return bool
     */
    public function cartRedirectAfterAdd();

    /**
     * Identifiant de qualification de la page de commande.
     *
     * @return int
     */
    public function checkoutPageId();

    /**
     * Devise monétaire des tarifs.
     *
     * @return string
     */
    public function currency();

    /**
     * Position de la devise pour l'affichage des tarifs.
     *
     * @return string left|right|left_space|right_space
     */
    public function currencyPosition();

    /**
     * Nombre de décimales.
     *
     * @return int
     */
    public function decimalNumber();

    /**
     * Séparateur des décimales.
     *
     * @return string
     */
    public function decimalSeparator();

    /**
     * Adresse par défaut du client.
     *
     * @return string
     */
    public function defaultCustomerAddress();

    /**
     * Unité de dimension.
     *
     * @return string
     */
    public function dimensionUnit();

    /**
     * Activation et calcul de la TVA.
     *
     * @return bool
     */
    public function isCalcTaxes();

    /**
     * Activation de la gestion des stocks.
     *
     * @return bool
     */
    public function isManageStock();

    /**
     * Vérifie si les tarifs des produits saisie inclus la TVA (prix TTC ou HT).
     *
     * @return bool
     */
    public function isPricesIncludeTax();

    /**
     * Vérifie si le calculateur de frais du panier est actif.
     *
     * @return bool
     */
    public function isShippingCalcEnabled();

    /**
     * Vérifie s'il faut masquer les frais de livraison tant qu'aucune adresse de livraison n'est renseignée.
     *
     * @return bool
     */
    public function isShippingCostRequiresAddress();

    /**
     * Activation du mode de débogage de la livraison.
     *
     * @return bool
     */
    public function isShippingDebugMode();

    /**
     * Liste des pays de livraison.
     *
     * @return string|array
     */
    public function shippingCountries();

    /**
     * Destination de livraison.
     *
     * @return string shipping|billing|billing_only
     */
    public function shipToDestination();

    /**
     * Identifiant de qualification de la page d'affichage de l'accueil de la boutique.
     *
     * @return int
     */
    public function shopPageId();

    /**
     * Adresse de la boutique - ligne 1.
     *
     * @return string
     */
    public function storeAddress();

    /**
     * Adresse de la boutique - ligne 2.
     *
     * @return string
     */
    public function storeAddressAdditionnal();

    /**
     * Ville de la boutique.
     *
     * @return string
     */
    public function storeCity();

    /**
     * Pays de la boutique.
     *
     * @return string
     */
    public function storeCountry();

    /**
     * Code postal de la boutique.
     *
     * @return string
     */
    public function storePostcode();

    /**
     * Identifiant de qualification de la page des conditions générales de vente.
     *
     * @return int
     */
    public function termsPageId();

    /**
     * Séparateur des milliers.
     *
     * @return string
     */
    public function thousandSeparator();

    /**
     * Unité de poids.
     *
     * @return string
     */
    public function weightUnit();
}