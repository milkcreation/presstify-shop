<?php

namespace tiFy\Plugins\Shop\Contracts;

use tiFy\Contracts\Support\ParamsBag;

interface CartLineInterface extends ParamsBag
{
    /**
     * Nom du champ de modification d'un attribut dans le panier.
     *
     * @param string $attribute_name Nom de l'attribut du champ.
     *
     * @return string
     */
    public function cartFieldName($attribute_name);

    /**
     * Récupération de l'identifiant de qualification de l'article dans le panier.
     *
     * @return string
     */
    public function getKey();

    /**
     * Récupération du prix de vente.
     *
     * @return float
     */
    public function getPrice();

    /**
     * Récupération de l'affichage HTML du prix de vente.
     *
     * @return string
     */
    public function getPriceHtml();

    /**
     * Indique si le prix de vente tient compte de la taxe.
     *
     * @return string
     */
    public function getPriceIncludesTax();

    /**
     * Récupération des données du produit associé à l'article du panier.
     *
     * @return ProductItemInterface
     */
    public function getProduct();

    /**
     * Récupération de l'identifiant de qualification du produit associé à l'article du panier.
     *
     * @return int
     */
    public function getProductId();

    /**
     * Récupération des options d'achat par produit.
     *
     * @return array
     */
    public function getPurchasingOptions();

    /**
     * Récupération de la quantité du produit associé à l'article du panier
     *
     * @return int
     */
    public function getQuantity();

    /**
     * @return float
     */
    public function getSubtotal();

    /**
     * @return float
     */
    public function getSubtotalTax();

    /**
     * @return float
     */
    public function getTax();

    /**
     * @return false|string
     */
    public function getTaxable();

    /**
     * @return string
     */
    public function getTaxClass();

    /**
     * @return array
     */
    public function getTaxes();

    /**
     * @return array
     */
    public function getTaxRates();

    /**
     * @return float
     */
    public function getTotal();

    /**
     * Vérifie si l'article nécessite une livraison.
     *
     * @return bool
     */
    public function needShipping();

    /**
     * Url de suppression de l'article dans le panier d'achat.
     *
     * @return string
     */
    public function removeUrl();
}