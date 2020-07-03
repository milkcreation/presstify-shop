<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop\Contracts;

use tiFy\Contracts\Support\ParamsBag;

interface Settings extends ParamsBag, ShopAwareTrait
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
    public function cartEnableListAdd(): bool;

    /**
     * Identifiant de qualification de la page d'affichage du panier.
     *
     * @return integer
     */
    public function cartPageId(): int;

    /**
     * Redirection vers le panier après l'ajout d'un article.
     *
     * @return bool
     */
    public function cartRedirectAfterAdd(): bool;

    /**
     * Identifiant de qualification de la page de commande.
     *
     * @return integer
     */
    public function checkoutPageId(): int;

    /**
     * Devise monétaire des tarifs.
     *
     * @return string
     */
    public function currency(): string;

    /**
     * Position de la devise pour l'affichage des tarifs.
     *
     * @return string left|right|left_space|right_space
     */
    public function currencyPosition(): string;

    /**
     * Nombre de décimales.
     *
     * @return integer
     */
    public function decimalNumber(): int;

    /**
     * Séparateur des décimales.
     *
     * @return string
     */
    public function decimalSeparator(): string;

    /**
     * Adresse par défaut du client.
     *
     * @return string
     */
    public function defaultCustomerAddress(): string;

    /**
     * Unité de dimension.
     *
     * @return string
     */
    public function dimensionUnit(): string;

    /**
     * Activation et calcul de la TVA.
     *
     * @return bool
     */
    public function isCalcTaxes(): bool;

    /**
     * Activation de la gestion des stocks.
     *
     * @return bool
     */
    public function isManageStock(): bool;

    /**
     * Vérifie si les tarifs des produits saisie inclus la TVA (prix TTC ou HT).
     *
     * @return bool
     */
    public function isPricesIncludeTax(): bool;

    /**
     * Vérification d'activation de la livraison.
     * 
     * @return bool
     */
    public function isShippingEnabled(): bool;
    
    /**
     * Vérifie si le calculateur de frais du panier est actif.
     *
     * @return bool
     */
    public function isShippingCalcEnabled(): bool;

    /**
     * Vérifie s'il faut masquer les frais de livraison tant qu'aucune adresse de livraison n'est renseignée.
     *
     * @return bool
     */
    public function isShippingCostRequiresAddress(): bool;

    /**
     * Activation du mode de débogage de la livraison.
     *
     * @return bool
     */
    public function isShippingDebugMode(): bool;

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
    public function shipToDestination(): string;

    /**
     * Identifiant de qualification de la page d'affichage de l'accueil de la boutique.
     *
     * @return integer
     */
    public function shopPageId(): int;

    /**
     * Adresse de la boutique - ligne 1.
     *
     * @return string
     */
    public function storeAddress(): string;

    /**
     * Adresse de la boutique - ligne 2.
     *
     * @return string
     */
    public function storeAddressAdditionnal(): string;

    /**
     * Ville de la boutique.
     *
     * @return string
     */
    public function storeCity(): string;

    /**
     * Pays de la boutique.
     *
     * @return string
     */
    public function storeCountry(): string;

    /**
     * Code postal de la boutique.
     *
     * @return string
     */
    public function storePostcode(): string ;

    /**
     * Identifiant de qualification de la page des conditions générales de vente.
     *
     * @return integer
     */
    public function termsPageId(): int;

    /**
     * Séparateur des milliers.
     *
     * @return string
     */
    public function thousandSeparator(): string;

    /**
     * Unité de poids.
     *
     * @return string
     */
    public function weightUnit(): string;
}