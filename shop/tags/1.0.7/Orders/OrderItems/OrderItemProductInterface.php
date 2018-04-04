<?php

namespace tiFy\Plugins\Shop\Orders\OrderItems;

use tiFy\Plugins\Shop\Products\ProductItemInterface;

interface OrderItemProductInterface extends OrderItemInterface
{
    /**
     * Récupération de la classe de rappel du produit associé.
     * @return ProductItemInterface
     */
    public function getProduct();

    /**
     * Récupération de l'identifiant de qualification du produit associé.
     * @return int
     */
    public function getProductId();

    /**
     * Récupération de l'identifiant de qualification de la variation de produit associée.
     * @return int
     */
    public function getVariationId();

    /**
     * Récupération de la quantité d'article du produit associé.
     * @return int
     */
    public function getQuantity();

    /**
     * Récupération de la classe de taxe appliqué à la commande.
     * @return string
     */
    public function getTaxClass();

    /**
     * Récupération du montant du sous-total de la commande.
     * @return float
     */
    public function getSubtotal();

    /**
     * Récupération du montant du sous-total appliqué à la commande.
     * @return float
     */
    public function getSubtotalTax();

    /**
     * Récupération du montant total de la commande.
     * @return float
     */
    public function getTotal();

    /**
     * Récupération du montant total de la taxe appliqué à la commande.
     * @return float
     */
    public function getTotalTax();

    /**
     * Récupération de la liste des taxes appliquées à la commande.
     * @return array
     */
    public function getTaxes();
}