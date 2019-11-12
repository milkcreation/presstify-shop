<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop\Settings;

use tiFy\Plugins\Shop\Contracts\{Settings as SettingsContract, Shop};
use tiFy\Plugins\Shop\ShopAwareTrait;
use tiFy\Support\{ParamsBag, Str};

class Settings extends ParamsBag implements SettingsContract
{
    use ShopAwareTrait;

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
        $this->setShop($shop);

        $attrs = $this->shop()->config('settings', []);

        $this->boot();

        foreach($this->settings as $setting) {
            if (!isset($attrs[$setting])) {
                if ($value = get_option($setting)) {
                    $attrs[$setting] = $value;
                } else {
                    $method = Str::camel($setting);
                    if (method_exists($this, $method)) {
                        $attrs[$setting] = call_user_func([$this, $method]);
                    }
                }
            }
        }

        $this->set($attrs)->parse();
    }

    /**
     * @inheritDoc
     */
    public function boot(): void { }

    /**
     * @inheritDoc
     */
    public function allowedCountries()
    {
        return $this->get('allowed_countries', 'all');
    }

    /**
     * @inheritDoc
     */
    public function cartEnableListAdd(): bool
    {
        return $this->get('cart_enabled_list_add', false);
    }

    /**
     * @inheritDoc
     */
    public function cartPageId(): int
    {
        return (int)$this->get('cart_page_id', 0);
    }

    /**
     * @inheritDoc
     */
    public function cartRedirectAfterAdd(): bool
    {
        return $this->get('cart_redirect_after_add', false);
    }

    /**
     * @inheritDoc
     */
    public function checkoutPageId(): int
    {
        return (int)$this->get('checkout_page_id', 0);
    }

    /**
     * @inheritDoc
     */
    public function currency(): string
    {
        return $this->get('currency', 'EUR');
    }

    /**
     * @inheritDoc
     */
    public function currencyPosition(): string
    {
        return in_array($this->get('currency_position'), ['right', 'left', 'right_space', 'left_space'])
            ? $this->get('currency_position') : 'right';
    }

    /**
     * @inheritDoc
     */
    public function decimalNumber(): int
    {
        return (int)$this->get('decimal_number', 2);
    }

    /**
     * @inheritDoc
     */
    public function decimalSeparator(): string
    {
        return $this->get('decimal_separator', ',');
    }

    /**
     * @inheritDoc
     */
    public function defaultCustomerAddress(): string
    {
        return $this->get('default_customer_address', '');
    }

    /**
     * @inheritDoc
     */
    public function dimensionUnit(): string
    {
        return $this->get('dimension_unit', 'cm');
    }

    /**
     * @inheritDoc
     */
    public function isCalcTaxes(): bool
    {
        return (bool)$this->get('is_calc_taxes', false);
    }

    /**
     * @inheritDoc
     */
    public function isManageStock(): bool
    {
        return (bool)$this->get('is_manage_stock', false);
    }

    /**
     * @inheritDoc
     */
    public function isPricesIncludeTax(): bool
    {
        return (bool)$this->get('prices_include_tax', false);
    }

    /**
     * @inheritDoc
     */
    public function isShippingCalcEnabled(): bool
    {
        return (bool)$this->get('enable_shipping_calc', false);
    }

    /**
     * @inheritDoc
     */
    public function isShippingCostRequiresAddress(): bool
    {
        return (bool)$this->get('shipping_cost_requires_address', false);
    }

    /**
     * @inheritDoc
     */
    public function isShippingDebugMode(): bool
    {
        return (bool)$this->get('shipping_debug_mode', false);
    }

    /**
     * @inheritDoc
     */
    public function shippingCountries()
    {
        return $this->get('shipping_countries', 'all');
    }

    /**
     * @inheritDoc
     */
    public function shipToDestination(): string
    {
        return (string)$this->get('ship_to_destination', 'billing_only');
    }

    /**
     * @inheritDoc
     */
    public function shopPageId(): int
    {
        return (int)$this->get('shop_page_id', 0);
    }

    /**
     * @inheritDoc
     */
    public function storeAddress(): string
    {
        return $this->get('store_address', '');
    }

    /**
     * @inheritDoc
     */
    public function storeAddressAdditionnal(): string
    {
        return $this->get('store_address_additionnal', '');
    }

    /**
     * @inheritDoc
     */
    public function storeCity(): string
    {
        return $this->get('store_city', '');
    }

    /**
     * @inheritDoc
     */
    public function storeCountry(): string
    {
        return $this->get('store_country', '');
    }

    /**
     * @inheritDoc
     */
    public function storePostcode(): string
    {
        return $this->get('store_postcode', '');
    }

    /**
     * @inheritDoc
     */
    public function termsPageId(): int
    {
        return (int)$this->get('terms_page_id', 0);
    }

    /**
     * @inheritDoc
     */
    public function thousandSeparator(): string
    {
        return $this->get('thousand_separator', '');
    }

    /**
     * @inheritDoc
     */
    public function weightUnit(): string
    {
        return $this->get('weight_unit', 'kg');
    }
}