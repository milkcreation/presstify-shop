<?php

/**
 * @name Line
 * @desc Controleur de récupération des données d'une ligne d'article dans le panier d'achat.
 *
 * @author Jordy Manner <jordy@tigreblanc.fr>
 * @copyright Milkcreation
 */

namespace tiFy\Plugins\Shop\Cart;

use Illuminate\Support\Fluent;
use tiFy\Plugins\Shop\Contracts\CartInterface;
use tiFy\Plugins\Shop\Contracts\CartLineInterface;
use tiFy\Plugins\Shop\Products\ProductItem;
use tiFy\Plugins\Shop\Products\ProductPurchasingOption;
use tiFy\Plugins\Shop\Shop;
use tiFy\Plugins\Shop\ShopResolverTrait;

class Line extends Fluent implements CartLineInterface
{
    use ShopResolverTrait;

    /**
     * Instance du controleur de panier.
     * @var CartInterface
     */
    protected $cart;

    /**
     * CONSTRUCTEUR.
     *
     * @param array Liste des attributs de l'article dans le panier.
     * @param CartInterface $cart Instance du controleur de panier.
     * @param Shop $shop Instance de la boutique.
     *
     * @return void
     */
    public function __construct($attributes, CartInterface $cart, Shop $shop)
    {
        $this->cart = $cart;
        $this->shop = $shop;

        parent::__construct($attributes);

        if ($this->getProduct()) :
            $this['product_id'] = $this->getProductId();
        endif;
    }

    /**
     * {@inheritdoc}
     */
    public function cartFieldName($attribute_name)
    {
        return "cart[{$this->getKey()}][{$attribute_name}]";
    }

    /**
     * {@inheritdoc}
     */
    public function getKey()
    {
        return (string)$this->get('key', '');
    }

    /**
     * {@inheritdoc}
     */
    public function getPrice()
    {
        return (float)$this->getProduct()->getRegularPrice() * $this->getQuantity();
    }

    /**
     * {@inheritdoc}
     */
    public function getPriceHtml()
    {
        return $this->functions()->price()->html($this->getPrice());
    }

    /**
     * {@inheritdoc}
     */
    public function getPriceIncludesTax()
    {
        return $this->get('price_includes_tax', false);
    }

    /**
     * {@inheritdoc}
     */
    public function getProduct()
    {
        return $this->get('product', null);
    }

    /**
     * {@inheritdoc}
     */
    public function getProductId()
    {
        return (int)$this->getProduct()->getId();
    }

    /**
     * {@inheritdoc}
     */
    public function getPurchasingOptions()
    {
        $purchasing_options = [];
        foreach($this->get('purchasing_options',[]) as $product_id => $opts) :
            if ($product = $this->products()->getItem($product_id)) :
                foreach($opts as $name => $selected) :
                    if ($po = $product->getPurchasingOption($name)) :
                        $po->setSelected($selected);
                        $purchasing_options[] = $po;
                    endif;
                endforeach;
            endif;
        endforeach;

        return $purchasing_options;
    }

    /**
     * {@inheritdoc}
     */
    public function getQuantity()
    {
        return (int)$this->get('quantity', 0);
    }

    /**
     * {@inheritdoc}
     */
    public function getSubtotal()
    {
        return (float)$this->get('line_subtotal', 0);
    }

    /**
     * {@inheritdoc}
     */
    public function getSubtotalTax()
    {
        return (float)$this->get('line_subtotal_tax', 0);
    }

    /**
     * {@inheritdoc}
     */
    public function getTax()
    {
        return (float)$this->get('line_tax', 0);
    }

    /**
     * {@inheritdoc}
     */
    public function getTaxable()
    {
        return $this->get('taxable', false);
    }

    /**
     * {@inheritdoc}
     */
    public function getTaxClass()
    {
        return (string)$this->get('tax_class', '');
    }

    /**
     * {@inheritdoc}
     */
    public function getTaxes()
    {
        return (array)$this->get('line_tax_data', ['subtotal' => 0, 'total' => 0]);
    }

    /**
     * {@inheritdoc}
     */
    public function getTaxRates()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getTotal()
    {
        return (float)$this->get('line_total', 0);
    }

    /**
     * {@inheritdoc}
     */
    public function needShipping()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function removeUrl()
    {
        return $this->cart->removeUrl($this->getKey());
    }
}