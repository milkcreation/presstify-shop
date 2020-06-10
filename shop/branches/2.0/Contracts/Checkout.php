<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop\Contracts;

interface Checkout extends ShopAwareTrait
{
    /**
     * Ajout des élements de bon de réduction à la commande.
     *
     * @param Order $order Instance de la commande.
     *
     * @return void
     */
    public function createOrderItemsCoupon(Order $order): void;

    /**
     * Ajout des élements de remise à la commande.
     *
     * @param Order $order Instance de la commande.
     *
     * @return void
     */
    public function createOrderItemsDiscount(Order $order): void;

    /**
     * Ajout des élements de promotion à la commande.
     *
     * @param Order $order Instance de la commande.
     *
     * @return void
     */
    public function createOrderItemsFee(Order $order): void;

    /**
     * Ajout des élements du panier à la commande.
     *
     * @param Order $order Instance de la commande.
     *
     * @return void
     */
    public function createOrderItemsProduct(Order $order): void;

    /**
     * Ajout des élements de livraison à la commande.
     *
     * @param Order $order Instance de la commande.
     *
     * @return void
     */
    public function createOrderItemsShipping(Order $order): void;

    /**
     * Ajout des élements de taxe à la commande.
     *
     * @param Order $order Instance de la commande.
     *
     * @return void
     */
    public function createOrderItemsTax(Order $order): void;

    /**
     * Url d'action d'exécution de la commande.
     * {@internal Requête de type POST; l'url doit être intégrée en tant qu'attribut "action" d'une balise d'ouverture
     * de formulaire ayant pour attribut "method" POST.}
     *
     * @return string
     */
    public function handleUrl(): string;
}