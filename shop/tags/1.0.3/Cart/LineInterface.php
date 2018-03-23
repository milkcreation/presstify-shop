<?php

namespace tiFy\Plugins\Shop\Cart;

interface LineInterface
{
    /**
     * Récupération de la clé d'identification de l'article dans le panier
     * @return string
     */
    public function getKey();

    /**
     * Récupération de la quantité du produit associé à l'article dans le panier
     * @return int
     */
    public function getQuantity();

    /**
     * Récupération de l'identifiant de qualification du produit associé à l'article du panier
     * @return int
     */
    public function getProductId();

    /**
     * Récupération des données du produit associé à l'article du panier
     * @return \tiFy\Plugins\Shop\Products\ProductItemInterface
     */
    public function getProduct();

    /**
     * @return float
     */
    public function getTotal();

    /**
     * @return float
     */
    public function getTax();

    /**
     * @return float
     */
    public function getSubtotal();

    /**
     * @return float
     */
    public function getSubtotalTax();

    /**
     * @return mixed
     */
    public function getTaxClass();

    /**
     * @return string
     */
    public function getTaxable();

    /**
     * @return string
     */
    public function getPriceIncludesTax();

    /**
     * @return float
     */
    public function getPrice();

    /**
     * @return array
     */
    public function getTaxRates();

    /**
     * Url de suppression de l'article dans le panier d'achat
     * @return string
     */
    public function removeUrl();

    /**
     * Nom du champ de modification d'un attribut dans le panier
     * @param string $attribute_name Nom de l'attribut du champ
     * @return string
     */
    public function cartFieldName($attribute_name);

    /**
     * Vérifie si l'article nécessite une livraison
     * @return bool
     */
    public function needShipping();
}