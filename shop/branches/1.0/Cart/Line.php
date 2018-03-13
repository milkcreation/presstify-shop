<?php

/**
 * @name Line
 * @desc Controleur de récupération des données d'une ligne d'article dans le panier d'achat
 * @package presstiFy
 * @namespace \tiFy\Plugins\Shop\Cart
 * @version 1.1
 * @since 1.2.596
 *
 * @author Jordy Manner <jordy@tigreblanc.fr>
 * @copyright Milkcreation
 */

namespace tiFy\Plugins\Shop\Cart;

use Illuminate\Support\Fluent;
use tiFy\App\Traits\App as TraitsApp;
use tiFy\Plugins\Shop\Shop;

class Line extends Fluent implements LineInterface
{
    use TraitsApp;

    /**
     * Classe de rappel de la boutique
     * @var Shop
     */
    protected $shop;

    /**
     * Classe de rappel du controleur de panier
     * @var CartInterface
     */
    protected $cart;

    /**
     * CONSTRUCTEUR
     *
     * @param Shop $shop Classe de rappel de la boutique
     * @param CartInterface $cart Classe de rappel du controleur de panier
     * @param array Liste des attributs de l'article dans le panier
     *
     * @return void
     */
    public function __construct(Shop $shop, CartInterface $cart, $attributes)
    {
        parent::__construct($attributes);

        // Définition de la classe de rappel de la boutique
        $this->shop = $shop;

        // Définition de la classe de rappel du controleur de panier
        $this->cart = $cart;

        // Définition de l'identifiant du produit
        if ($this->getProduct()) :
            $this['product_id'] = $this->getProductId();
        endif;
    }

    /**
     * Récupération de l'identifiant de qualification de l'article dans le panier
     *
     * @return string
     */
    public function getKey()
    {
        return $this->get('key', '');
    }

    /**
     * Récupération de la quantité du produit associé à l'article du panier
     *
     * @return int
     */
    public function getQuantity()
    {
        return $this->get('quantity', 0);
    }

    /**
     * Récupération de l'identifiant de qualification du produit associé à l'article du panier
     *
     * @return int
     */
    public function getProductId()
    {
        return $this->getProduct()->getId();
    }

    /**
     * Récupération des données du produit associé à l'article du panier
     *
     * @return \tiFy\Plugins\Shop\Products\ProductItemInterface
     */
    public function getProduct()
    {
        return $this->get('product', null);
    }

    /**
     *
     *
     * @return float
     */
    public function getTotal()
    {
        return $this->get('line_total', 0);
    }

    /**
     *
     *
     * @return float
     */
    public function getTax()
    {
        return $this->get('line_tax', 0);
    }

    /**
     *
     *
     * @return float
     */
    public function getSubtotal()
    {
        return $this->get('line_subtotal', 0);
    }

    /**
     *
     *
     * @return float
     */
    public function getSubtotalTax()
    {
        return $this->get('line_subtotal_tax', 0);
    }

    /**
     *
     * @return array
     */
    public function getTaxes()
    {
        return $this->get('line_tax_data', ['subtotal' => 0, 'total' => 0]);
    }

    /**
     *
     *
     * @return string
     */
    public function getTaxClass()
    {
        return $this->get('tax_class', '');
    }

    /**
     *
     *
     * @return false|string
     */
    public function getTaxable()
    {
        return $this->get('taxable', false);
    }

    /**
     * Indique si le prix de vente tient compte de la taxe
     *
     * @return string
     */
    public function getPriceIncludesTax()
    {
        return $this->get('price_includes_tax', false);
    }

    /**
     * Récupération du prix de vente
     *
     * @return float
     */
    public function getPrice()
    {
        return (float)$this->getProduct()->getRegularPrice() * $this->getQuantity();
    }

    /**
     * Récupération de l'affichage HTML du prix de vente
     *
     * @return string
     */
    public function getPriceHtml()
    {
        return $this->shop->providers()->price()->html($this->getPrice());
    }

    /**
     *
     * @return array
     */
    public function getTaxRates()
    {
        return [];
    }

    /**
     * Url de suppression de l'article dans le panier d'achat
     *
     * @return string
     */
    public function removeUrl()
    {
        return $this->cart->removeUrl($this->getKey());
    }

    /**
     * Nom du champ de modification d'un attribut dans le panier
     *
     * @param string $attribute_name Nom de l'attribut du champ
     *
     * @return string
     */
    public function cartFieldName($attribute_name)
    {
        return "cart[{$this->getKey()}][{$attribute_name}]";
    }

    /**
     * Vérifie si l'article nécessite une livraison
     *
     * @return bool
     */
    public function needShipping()
    {
        return false;
    }
}