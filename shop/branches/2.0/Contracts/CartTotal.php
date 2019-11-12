<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop\Contracts;

use tiFy\Contracts\Support\ParamsBag;

interface CartTotal extends ParamsBag, ShopAwareTrait
{
    /**
     * Récupération du montant total global.
     *
     * @return string
     */
    public function __toString(): string;

    /**
     * Récupération de l'instance du panier associé.
     *
     * @return Cart
     */
    public function cart(): Cart;

    /**
     * Récupération de la taxe appliquée au montant de la remise.
     *
     * @return float
     */
    public function getDiscountTax(): float;

    /**
     * Récupération du montant total de la remise.
     *
     * @return float
     */
    public function getDiscountTotal(): float;

    /**
     * Récupération de la taxe appliquée au montant des frais.
     *
     * @return float
     */
    public function getFeeTax(): float;

    /**
     * Récupération de la liste des taxes appliquées aux frais.
     *
     * @return array
     */
    public function getFeeTaxes(): array;

    /**
     * Récupération du montant total des frais.
     *
     * @return float
     */
    public function getFeeTotal(): float;

    /**
     * Récupération du montant total global.
     *
     * @return float
     */
    public function getGlobal(): float;

    /**
     * Récupération de la taxe globale.
     *
     * @return float
     */
    public function getGlobalTax(): float;

    /**
     * Récupération du prix du sous-total cumulé des lignes du panier.
     *
     * @return float
     */
    public function getLinesSubtotal(): float;

    /**
     * Récupération de la taxe du sous-total cumulé des lignes du panier.
     *
     * @return float
     */
    public function getLinesSubtotalTax(): float;

    /**
     * Récupération de la liste des taxes appliquées aux lignes du panier.
     *
     * @return array
     */
    public function getLinesTaxes(): array;

    /**
     * Récupération du prix total cumulé des lignes du panier.
     *
     * @return float
     */
    public function getLinesTotal(): float;

    /**
     * Récupération du prix total cumulé des lignes du panier au format HTML.
     *
     * @return string
     */
    public function getLinesTotalHtml(): string;

    /**
     * Récupération de la taxe totale cumulée des lignes du panier.
     *
     * @return float
     */
    public function getLinesTotalTax(): float;

    /**
     * Récupération de la taxe appliquée au montant de la livraison.
     *
     * @return float
     */
    public function getShippingTax(): float;

    /**
     * Récupération de la liste des taxes appliquées à la livraison.
     *
     * @return array
     */
    public function getShippingTaxes(): array;

    /**
     * Récupération du montant total de la livraison.
     *
     * @return float
     */
    public function getShippingTotal(): float;
}