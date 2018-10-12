<?php

namespace tiFy\Plugins\Shop\Contracts;

use tiFy\Plugins\Shop\Contracts\ProductItemInterface;

interface OrderItemTypeProductInterface extends OrderItemTypeInterface
{
    /**
     * Récupération de la classe de rappel du produit associé.
     *
     * @return null|ProductItemInterface
     */
    public function getProduct();

    /**
     * Récupération de l'identifiant de qualification du produit associé.
     *
     * @return int
     */
    public function getProductId();

    /**
     * Récupération de la quantité d'article du produit associé.
     *
     * @return int
     */
    public function getQuantity();

    /**
     * Récupération du montant du sous-total de la commande.
     *
     * @return float
     */
    public function getSubtotal();

    /**
     * Récupération du montant du sous-total appliqué à la commande.
     *
     * @return float
     */
    public function getSubtotalTax();

    /**
     * Récupération de la classe de taxe appliqué à la commande.
     *
     * @return string
     */
    public function getTaxClass();

    /**
     * Récupération de la liste des taxes appliquées à la commande.
     *
     * @return array
     */
    public function getTaxes();

    /**
     * Récupération du montant total de la commande.
     *
     * @return float
     */
    public function getTotal();

    /**
     * Récupération du montant total de la taxe appliqué à la commande.
     *
     * @return float
     */
    public function getTotalTax();

    /**
     * Récupération du type d'élement associé à la commande.
     *
     * @return string line_item
     */
    public function getType();

    /**
     * Récupération de l'identifiant de qualification de la variation de produit associée.
     *
     * @return int
     */
    public function getVariationId();
}