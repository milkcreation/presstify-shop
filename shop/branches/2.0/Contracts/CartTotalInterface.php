<?php

namespace tiFy\Plugins\Shop\Contracts;

use tiFy\Contracts\Kernel\ParamsBag;

interface CartTotalInterface extends ParamsBag
{
    /**
     * Récupération du montant total global.
     *
     * @return float
     */
    public function __toString();

    /**
     * Récupération de la taxe appliquée au montant de la remise.
     *
     * @return float
     */
    public function getDiscountTax();

    /**
     * Récupération du montant total de la remise.
     *
     * @return float
     */
    public function getDiscountTotal();

    /**
     * Récupération de la taxe appliquée au montant des frais.
     *
     * @return float
     */
    public function getFeeTax();

    /**
     * Récupération de la liste des taxes appliquées aux frais.
     *
     * @return array
     */
    public function getFeeTaxes();

    /**
     * Récupération du montant total des frais.
     *
     * @return float
     */
    public function getFeeTotal();

    /**
     * Récupération du montant total global.
     *
     * @return float
     */
    public function getGlobal();

    /**
     * Récupération de la taxe globale.
     *
     * @return float
     */
    public function getGlobalTax();

    /**
     * Récupération du prix du sous-total cumulé des lignes du panier.
     *
     * @return float
     */
    public function getLinesSubtotal();

    /**
     * Récupération de la taxe du sous-total cumulé des lignes du panier.
     *
     * @return float
     */
    public function getLinesSubtotalTax();

    /**
     * Récupération de la liste des taxes appliquées aux lignes du panier.
     *
     * @return array
     */
    public function getLinesTaxes();

    /**
     * Récupération du prix total cumulé des lignes du panier.
     *
     * @return float
     */
    public function getLinesTotal();

    /**
     * Récupération du prix total cumulé des lignes du panier au format HTML.
     *
     * @return string
     */
    public function getLinesTotalHtml();

    /**
     * Récupération de la taxe totale cumulée des lignes du panier.
     *
     * @return float
     */
    public function getLinesTotalTax();

    /**
     * Récupération de la taxe appliquée au montant de la livraison.
     *
     * @return float
     */
    public function getShippingTax();

    /**
     * Récupération de la liste des taxes appliquées à la livraison.
     *
     * @return array
     */
    public function getShippingTaxes();

    /**
     * Récupération du montant total de la livraison.
     *
     * @return float
     */
    public function getShippingTotal();
}