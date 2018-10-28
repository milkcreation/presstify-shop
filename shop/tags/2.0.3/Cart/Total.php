<?php

/**
 * @name Total
 * @desc Gestion du calcul des totaux du panier d'achat.
 *
 * @author Jordy Manner <jordy@tigreblanc.fr>
 * @copyright Milkcreation
 */

namespace tiFy\Plugins\Shop\Cart;

use Illuminate\Support\Fluent;
use tiFy\Plugins\Shop\Contracts\CartTotalInterface;
use tiFy\Plugins\Shop\Shop;
use tiFy\Plugins\Shop\ShopResolverTrait;

class Total extends Fluent implements CartTotalInterface
{
    use ShopResolverTrait;

    /**
     * Instance du controleur de panier.
     * @var CartInterface
     */
    protected $cart;

    /**
     * Liste des attributs.
     * @var array
     */
    protected $attributes = [
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
     * CONSTRUCTEUR.
     *
     * @param CartInterface $cart Instance de gestion des données des élements contenu dans le panier.
     * @param Shop $shop Instance de la boutique.
     *
     * @return void
     */
    public function __construct(Cart $cart, Shop $shop)
    {
        $this->cart = $cart;
        $this->shop = $shop;

        parent::__construct($this->attributes);

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
     * {@inheritdoc}
     */
    public function getDiscountTax()
    {
        return (float)$this->get('discount_tax', 0);
    }

    /**
     * {@inheritdoc}
     */
    public function getDiscountTotal()
    {
        return (float)$this->get('discount_total', 0);
    }

    /**
     * {@inheritdoc}
     */
    public function getFeeTax()
    {
        return (float)$this->get('fee_total_tax', 0);
    }

    /**
     * {@inheritdoc}
     */
    public function getFeeTaxes()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getFeeTotal()
    {
        return (float)$this->get('fee_total', 0);
    }

    /**
     * {@inheritdoc}
     */
    public function getGlobal()
    {
        return (float)$this->get('total', 0);
    }

    /**
     * {@inheritdoc}
     */
    public function getGlobalTax()
    {
        return (float)$this->get('total_tax', 0);
    }

    /**
     * {@inheritdoc}
     */
    public function getLinesSubtotal()
    {
        return (float)$this->get('lines_subtotal', 0);
    }

    /**
     * {@inheritdoc}
     */
    public function getLinesSubtotalTax()
    {
        return (float)$this->get('lines_subtotal_tax', 0);
    }

    /**
     * {@inheritdoc}
     */
    public function getLinesTaxes()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getLinesTotal()
    {
        return (float)$this->get('lines_total', 0);
    }

    /**
     * {@inheritdoc}
     */
    public function getLinesTotalHtml()
    {
        return (string)$this->functions()->price()->html($this->getLinesTotal());
    }

    /**
     * {@inheritdoc}
     */
    public function getLinesTotalTax()
    {
        return (float)$this->get('lines_total_tax', 0);
    }

    /**
     * {@inheritdoc}
     */
    public function getShippingTax()
    {
        return (float)$this->get('shipping_tax_total', 0);
    }

    /**
     * {@inheritdoc}
     */
    public function getShippingTaxes()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getShippingTotal()
    {
        return (float)$this->get('shipping_total', 0);
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return (string) $this->getGlobal();
    }
}