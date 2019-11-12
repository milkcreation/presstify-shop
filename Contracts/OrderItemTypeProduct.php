<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop\Contracts;

use tiFy\Support\ParamsBag;

interface OrderItemTypeProduct extends OrderItemType
{
    /**
     * Récupération de la classe de rappel du produit associé.
     *
     * @return ParamsBag
     */
    public function getProduct(): ParamsBag;

    /**
     * Récupération de l'identifiant de qualification du produit associé.
     *
     * @return int
     */
    public function getProductId(): int;

    /**
     * Récupération de la liste des options d'achat
     *
     * @return array
     */
    public function getPurchasingOptions(): array;

    /**
     * Récupération de la quantité d'article du produit associé.
     *
     * @return int
     */
    public function getQuantity(): int;

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
    public function getSubtotalTax(): float;

    /**
     * Récupération de la classe de taxe appliqué à la commande.
     *
     * @return string
     */
    public function getTaxClass(): string;

    /**
     * Récupération de la liste des taxes appliquées à la commande.
     *
     * @return array
     */
    public function getTaxes(): array;

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
    public function getTotalTax(): float;

    /**
     * Récupération du type d'élement associé à la commande.
     *
     * @return string line_item
     */
    public function getType(): string;

    /**
     * Récupération de l'identifiant de qualification de la variation de produit associée.
     *
     * @return int
     */
    public function getVariationId(): int;
}