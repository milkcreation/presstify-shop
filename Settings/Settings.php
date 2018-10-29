<?php

/**
 * @name Settings
 * @desc Controleur de gestion des réglages de la boutique.
 *
 * @author Jordy Manner <jordy@tigreblanc.fr>
 * @copyright Milkcreation
 */

namespace tiFy\Plugins\Shop\Settings;

use Illuminate\Support\Fluent;
use Illuminate\Support\Str;
use tiFy\Plugins\Shop\Contracts\SettingsInterface;
use tiFy\Plugins\Shop\Shop;
use tiFy\Plugins\Shop\ShopResolverTrait;

class Settings extends Fluent implements SettingsInterface
{
    use ShopResolverTrait;

    /**
     * Instance de la classe.
     * @var static
     */
    protected static $instance;

    /**
     * Liste des réglages disponibles.
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

        // Expédition - Zone d'expédition
        // @todo

        // Expédition - Options de livraison
        'enable_shipping_calc', 'shipping_cost_requires_address', 'ship_to_destination', 'shipping_debug_mode',

        // Expédition - Classes de livraison
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
     * CONSTRUCTEUR.
     *
     * @param Shop $shop Instance de la boutique.
     *
     * @return void
     */
    public function __construct(Shop $shop)
    {
        $this->shop = $shop;
        $attrs = $this->config('settings', []);

        foreach($this->settings as $setting) :
            if (isset($attrs[$setting])) :
                continue;
            endif;

            if ($value = get_option($setting)) :
                $attrs[$setting] = $value;
            else :
                $method = Str::camel($setting);
                if (method_exists($this, $method)) :
                    $attrs[$setting] = call_user_func([$this, $method]);
                endif;
            endif;
        endforeach;

        parent::__construct($attrs);
    }

    /**
     * Court-circuitage de l'implémentation.
     *
     * @return void
     */
    private function __clone()
    {

    }

    /**
     * Court-circuitage de l'implémentation.
     *
     * @return void
     */
    private function __wakeup()
    {

    }

    /**
     * Instanciation de la classe.
     *
     * @param Shop $shop Classe de rappel de la boutique.
     *
     * @return self
     */
    public static function make($alias, Shop $shop)
    {
        if (self::$instance) :
            return self::$instance;
        endif;

        return self::$instance = new self($shop);
    }

    /**
     * {@inheritdoc}
     */
    public function boot()
    {

    }

    /**
     * {@inheritdoc}
     */
    public function allowedCountries()
    {
        return $this->get('allowed_countries', 'all');
    }

    /**
     * {@inheritdoc}
     */
    public function cartEnableListAdd()
    {
        return $this->get('cart_enabled_list_add', false);
    }

    /**
     * {@inheritdoc}
     */
    public function cartPageId()
    {
        return (int)$this->get('cart_page_id', 0);
    }

    /**
     * {@inheritdoc}
     */
    public function cartRedirectAfterAdd()
    {
        return $this->get('cart_redirect_after_add', false);
    }

    /**
     * {@inheritdoc}
     */
    public function checkoutPageId()
    {
        return (int)$this->get('checkout_page_id', 0);
    }

    /**
     * {@inheritdoc}
     */
    public function currency()
    {
        return $this->get('currency', 'EUR');
    }

    /**
     * {@inheritdoc}
     */
    public function currencyPosition()
    {
        return in_array($this->get('currency_position'), ['right', 'left', 'right_space', 'left_space']) ? $this->get('currency_position') : 'right';
    }

    /**
     * {@inheritdoc}
     */
    public function decimalNumber()
    {
        return (int)$this->get('decimal_number', 2);
    }

    /**
     * {@inheritdoc}
     */
    public function decimalSeparator()
    {
        return $this->get('decimal_separator', ',');
    }

    /**
     * {@inheritdoc}
     */
    public function defaultCustomerAddress()
    {
        return $this->get('default_customer_address', '');
    }

    /**
     * {@inheritdoc}
     */
    public function dimensionUnit()
    {
        return $this->get('dimension_unit', 'cm');
    }

    /**
     * {@inheritdoc}
     */
    public function isCalcTaxes()
    {
        return (bool)$this->get('is_calc_taxes', false);
    }

    /**
     * {@inheritdoc}
     */
    public function isManageStock()
    {
        return (bool)$this->get('is_manage_stock', false);
    }

    /**
     * {@inheritdoc}
     */
    public function isPricesIncludeTax()
    {
        return (bool)$this->get('prices_include_tax', false);
    }

    /**
     * {@inheritdoc}
     */
    public function isShippingCalcEnabled()
    {
        return (bool)$this->get('enable_shipping_calc', false);
    }

    /**
     * {@inheritdoc}
     */
    public function isShippingCostRequiresAddress()
    {
        return (bool)$this->get('shipping_cost_requires_address', false);
    }

    /**
     * {@inheritdoc}
     */
    public function isShippingDebugMode()
    {
        return (bool)$this->get('shipping_debug_mode', false);
    }

    /**
     * {@inheritdoc}
     */
    public function shippingCountries()
    {
        return $this->get('shipping_countries', 'all');
    }

    /**
     * {@inheritdoc}
     */
    public function shipToDestination()
    {
        return (string)$this->get('ship_to_destination', 'billing_only');
    }

    /**
     * {@inheritdoc}
     */
    public function shopPageId()
    {
        return (int)$this->get('shop_page_id', 0);
    }

    /**
     * {@inheritdoc}
     */
    public function storeAddress()
    {
        return $this->get('store_address', '');
    }

    /**
     * {@inheritdoc}
     */
    public function storeAddressAdditionnal()
    {
        return $this->get('store_address_additionnal', '');
    }

    /**
     * {@inheritdoc}
     */
    public function storeCity()
    {
        return $this->get('store_city', '');
    }

    /**
     * {@inheritdoc}
     */
    public function storeCountry()
    {
        return $this->get('store_country', '');
    }

    /**
     * {@inheritdoc}
     */
    public function storePostcode()
    {
        return $this->get('store_postcode', '');
    }

    /**
     * {@inheritdoc}
     */
    public function termsPageId()
    {
        return (int)$this->get('terms_page_id', 0);
    }

    /**
     * {@inheritdoc}
     */
    public function thousandSeparator()
    {
        return $this->get('thousand_separator', '');
    }

    /**
     * {@inheritdoc}
     */
    public function weightUnit()
    {
        return $this->get('weight_unit', 'kg');
    }
}