<?php

/**
 * @name Settings
 * @desc Controleur de gestion des réglages de la boutique
 * @package presstiFy
 * @namespace \tiFy\Plugins\Shop\Settings
 * @version 1.1
 * @since 1.2.596
 *
 * @author Jordy Manner <jordy@tigreblanc.fr>
 * @copyright Milkcreation
 */

namespace tiFy\Plugins\Shop\Settings;

use Illuminate\Support\Fluent;
use Illuminate\Support\Str;
use tiFy\Plugins\Shop\Shop;

class Settings extends Fluent implements SettingsInterface
{
    /**
     * Liste des réglages disponibles
     * @var array
     */
    protected $settings = [
        // Général > Adresse de la boutique
        'store_address', 'store_address_additionnal', 'store_city', 'store_postcode', 'store_country',
        // Général > Options générales
        'allowed_countries', 'shipping_countries', 'default_customer_address',
        // Général > Options devise
        'is_calc_taxes', 'currency', 'currency_position', 'thousand_separator', 'decimal_separator', 'decimal_number',
        // Produits - Général > Page de boutique
        'shop_page_id', 'cart_redirect_after_add', 'cart_enabled_list_add',
        // Produits - Général > Dimensions
        'weight_unit', 'dimension_unit',
        // Produits - Général > Avis
        // @todo
        // Produits - Inventaire
        'is_manage_stock', // @todo
        // Produits - Produits téléchargeable
        // @todo
        // TVA - Options TVA
        'prices_include_tax',
        // TVA - Taux standards
        // @todo
        // TVA - Taux réduit
        // @todo
        // TVA - Taux zéro
        // @todo
        // Expédition
        // @todo
        // Commande - Options de commande > Processus de commande
        // @todo
        // Commande - Options de commande > Page de commande
        'cart_page_id', 'checkout_page_id', 'terms_page_id'
        // Commande - Options de commande > Terminaisons de commande
        // @todo
        // Commande - Options de commande > Passerelles de paiement
        // @todo
    ];

    /**
     * CONSTRUCTEUR
     *
     * @param array|object $attributes
     *
     * @return void
     */
    public function __construct($attributes = [])
    {
        foreach($this->settings as $setting) :
            if (isset($attributes[$setting])) :
                continue;
            endif;
            if ($value = get_option($setting)) :
                $attributes[$setting] = $value;
            else :
                $method = Str::camel($setting);
                if (method_exists($this, $method)) :
                    $attributes[$setting] = call_user_func([$this, $method]);
                endif;
            endif;
        endforeach;

        parent::__construct($attributes);
    }

    /**
     * Instanciation de la classe
     *
     * @param
     *
     * @return self
     */
    public static function make(Shop $shop)
    {
        return new static($shop->appConfig('settings', []));
    }

    /**
     * Adresse de la boutique - ligne 1
     * @return string
     */
    public function storeAddress()
    {
        return $this->get('store_address', '');
    }

    /**
     * Adresse de la boutique - ligne 2
     * @return string
     */
    public function storeAddressAdditionnal()
    {
        return $this->get('store_address_additionnal', '');
    }

    /**
     * Ville de la boutique
     * @return string
     */
    public function storeCity()
    {
        return $this->get('store_city', '');
    }

    /**
     * Code postal de la boutique
     * @return string
     */
    public function storePostcode()
    {
        return $this->get('store_postcode', '');
    }

    /**
     * Pays de la boutique
     * @return string
     */
    public function storeCountry()
    {
        return $this->get('store_country', '');
    }

    /**
     * Liste des pays de vente
     * @return string|array
     */
    public function allowedCountries()
    {
        return $this->get('allowed_countries', 'all');
    }

    /**
     * Liste des pays de livraison
     * @return string|array
     */
    public function shippingCountries()
    {
        return $this->get('shipping_countries', 'all');
    }

    /**
     * Adresse par défaut du client
     * @return string
     */
    public function defaultCustomerAddress()
    {
        return $this->get('default_customer_address', '');
    }

    /**
     * Activation et calcul de la TVA
     * @return bool
     */
    public function isCalcTaxes()
    {
        return (bool)$this->get('is_calc_taxes', false);
    }

    /**
     * Devise monétaire des tarifs
     * @return string
     */
    public function currency()
    {
        return $this->get('currency', 'EUR');
    }

    /**
     * Position de la devise pour l'affichage des tarifs
     * @return string left|right|left_space|right_space
     */
    public function currencyPosition()
    {
        return in_array($this->get('currency_position'), ['right', 'left', 'right_space', 'left_space']) ? $this->get('currency_position') : 'right';
    }

    /**
     * Séparateur des milliers
     * @return string
     */
    public function thousandSeparator()
    {
        return $this->get('thousand_separator', '');
    }

    /**
     * Séparateur des décimales
     * @return string
     */
    public function decimalSeparator()
    {
        return $this->get('decimal_separator', ',');
    }

    /**
     * Nombre de décimales
     * @return int
     */
    public function decimalNumber()
    {
        return (int)$this->get('decimal_number', 2);
    }

    /**
     * Identifiant de qualification de la page d'affichage de l'accueil de la boutique
     * @return int
     */
    public function shopPageId()
    {
        return (int)$this->get('shop_page_id', 0);
    }

    /**
     * Redirection vers le panier après l'ajout d'un article
     * @return bool
     */
    public function cartRedirectAfterAdd()
    {
        return $this->get('cart_redirect_after_add', false);
    }

    /**
     * Activation du bouton d'ajout au panier depuis les pages listes
     * @return bool
     */
    public function cartEnableListAdd()
    {
        return $this->get('cart_enabled_list_add', false);
    }

    /**
     * Unité de poids
     * @return string
     */
    public function weightUnit()
    {
        return $this->get('weight_unit', 'kg');
    }

    /**
     * Unité de dimension
     * @return string
     */
    public function dimensionUnit()
    {
        return $this->get('dimension_unit', 'cm');
    }

    /**
     * Activation de la gestion des stocks
     * @return bool
     */
    public function isManageStock()
    {
        return (bool)$this->get('is_manage_stock', false);
    }

    /**
     * Vérifie si les tarifs des produits saisie inclus la TVA (prix TTC ou HT)
     * @return bool
     */
    public function isPricesIncludeTax()
    {
        return (bool)$this->get('prices_include_tax', false);
    }

    /**
     * Identifiant de qualification de la page d'affichage du panier
     * @return int
     */
    public function cartPageId()
    {
        return (int)$this->get('cart_page_id', 0);
    }

    /**
     * Identifiant de qualification de la page de commande
     * @return int
     */
    public function checkoutPageId()
    {
        return (int)$this->get('checkout_page_id', 0);
    }

    /**
     * Identifiant de qualification de la page des conditions générales de vente
     * @return int
     */
    public function termsPageId()
    {
        return (int)$this->get('terms_page_id', 0);
    }
}