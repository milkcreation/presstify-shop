<?php

/**
 * @name Cart
 * @desc Gestion du panier d'achat
 * @package presstiFy
 * @namespace \tiFy\Plugins\Shop\Cart
 * @version 1.1
 * @since 1.2.535
 *
 * @author Jordy Manner <jordy@tigreblanc.fr>
 * @copyright Milkcreation
 */

namespace tiFy\Plugins\Shop\Cart;

use Illuminate\Support\Fluent;
use tiFy\App\Traits\App as TraitsApp;
use tiFy\Plugins\Shop\Shop;

class Total extends Fluent implements TotalInterface
{
    use TraitsApp;

    /**
     * Classe de rappel de la boutique
     * @var Shop
     */
    protected $shop;

    /**
     * Classe de rappel de gestion des données des élements contenu dans le panier
     * @var Cart
     */
    private $cart;

    /**
     * Stockage des totaux
     *
     * @var array
     */
    protected $defaults = [
        'lines_subtotal'     => 0,
        'lines_subtotal_tax' => 0,
        'lines_total'        => 0,
        'lines_total_tax'    => 0,
        'lines_taxes'        => [],
        'total'              => 0,
        'shipping_total'     => 0,
        'shipping_tax_total' => 0,
        'shipping_taxes'     => [],
        'discount_total'     => 0,
        'fee_total'          => 0,
        'fee_total_tax'      => 0,
        'fee_taxes'          => []
    ];

    /**
     * CONSTRUCTEUR
     *
     * @return void
     */
    public function __construct(Shop $shop, Cart $cart)
    {
        parent::__construct($this->defaults);

        // Définition de la classe de rappel de la boutique
        $this->shop = $shop;

        // Définition du panier
        $this->cart = $cart;

        if ($lines = $cart->lines()) :
            foreach ($lines as $line) :
                // Sous-totaux
                $line['line_tax_data'] = ['subtotal' => []];
                $line['line_subtotal'] = $line->getPrice();
                $line['line_subtotal_tax'] = 0;

                // Totaux
                $line['line_tax_data'] = array_merge($line['line_tax_data'], ['total' => []]);
                $line['line_total'] = $line->getPrice();
                $line['line_tax'] = 0;
            endforeach;

            //array_map( 'round', array_values( wp_list_pluck( $this->items, 'subtotal' ) ) ) ) );

            // Calcul des sous-totaux
            $this['lines_subtotal'] = $lines->sum('line_subtotal');
            $this['lines_subtotal_tax'] = $lines->sum('line_subtotal_tax');

            // Calcul des totaux
            $this['lines_total'] = $lines->sum('line_total');
            $this['lines_total_tax'] = $lines->sum('line_tax');

            $this['total'] = $this['lines_total'] + $this['fees_total'] + $this['shipping_total'];
        endif;
    }

    /**
     * Récupération du prix du sous-total cumulé des lignes du panier
     *
     * @return float
     */
    public function getLinesSubtotal()
    {
        return $this->get('lines_subtotal', 0);
    }

    /**
     * Récupération de la taxe du sous-total cumulé des lignes du panier
     *
     * @return float
     */
    public function getLinesSubtotalTax()
    {
        return $this->get('lines_subtotal_tax', 0);
    }

    /**
     * Récupération du prix total cumulé des lignes du panier
     *
     * @return float
     */
    public function getLinesTotal()
    {
        return $this->get('lines_total', 0);
    }

    /**
     * Récupération du prix total cumulé des lignes du panier au format HTML
     *
     * @return float
     */
    public function getLinesTotalHtml()
    {
        return $this->shop->providers()->price()->html($this->getLinesTotal());
    }

    /**
     * Récupération de la taxe totale cumulée des lignes du panier
     *
     * @return float
     */
    public function getLinesTotalTax()
    {
        return $this->get('lines_total_tax', 0);
    }

    /**
     * Récupération de la liste des taxes appliquées aux lignes du panier
     *
     * @return array
     */
    public function getLinesTaxes()
    {
        return [];
    }

    /**
     * Récupération du montant total global
     *
     * @return float
     */
    public function getGlobal()
    {
        return $this->get('total', 0);
    }

    /**
     * Récupération de la taxe globale
     *
     * @return float
     */
    public function getGlobalTax()
    {
        return $this->get('total_tax', 0);
    }

    /**
     * Récupération du montant total de la livraison
     *
     * @return float
     */
    public function getShippingTotal()
    {
        return $this->get('shipping_total', 0);
    }

    /**
     * Récupération de la taxe appliquée au montant de la livraison
     *
     * @return float
     */
    public function getShippingTax()
    {
        return $this->get('shipping_tax_total', 0);
    }

    /**
     * Récupération de la liste des taxes appliquées à la livraison
     *
     * @return array
     */
    public function getShippingTaxes()
    {
        return [];
    }

    /**
     * Récupération du montant total de la remise
     *
     * @return float
     */
    public function getDiscountTotal()
    {
        return $this->get('discount_total', 0);
    }

    /**
     * Récupération de la taxe appliquée au montant de la remise
     *
     * @return float
     */
    public function getDiscountTax()
    {
        return $this->get('discount_tax', 0);
    }

    /**
     * Récupération du montant total des frais
     *
     * @return float
     */
    public function getFeeTotal()
    {
        return $this->get('fee_total', 0);
    }

    /**
     * Récupération de la taxe appliquée au montant des frais
     *
     * @return float
     */
    public function getFeeTax()
    {
        return $this->get('fee_total_tax', 0);
    }

    /**
     * Récupération de la liste des taxes appliquées aux frais
     *
     * @return array
     */
    public function getFeeTaxes()
    {
        return [];
    }

    /**
     * Récupération du montant total global
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->getGlobal();
    }
}