<?php

namespace tiFy\Plugins\Shop\Contracts;

use tiFy\Plugins\Shop\Shop;

interface CheckoutInterface extends BootableControllerInterface, ShopResolverInterface
{
    /**
     * Ajout des élements de bon de réduction à la commande.
     *
     * @param OrderInterface $order Classe de rappel de la commande relative au paiement.
     *
     * @return void
     */
    public function createOrderItemsCoupon(OrderInterface $order);

    /**
     * Ajout des élements de promotion à la commande.
     *
     * @param OrderInterface $order Classe de rappel de la commande relative au paiement.
     *
     * @return void
     */
    public function createOrderItemsFee(OrderInterface $order);

    /**
     * Ajout des élements du panier à la commande.
     *
     * @param OrderInterface $order Classe de rappel de la commande relative au paiement.
     *
     * @return void
     */
    public function createOrderItemsProduct(OrderInterface $order);

    /**
     * Ajout des élements de livraison à la commande.
     *
     * @param OrderInterface $order Classe de rappel de la commande relative au paiement.
     *
     * @return void
     */
    public function createOrderItemsShipping(OrderInterface $order);

    /**
     * Ajout des élements de taxe à la commande.
     *
     * @param OrderInterface $order Classe de rappel de la commande relative au paiement.
     *
     * @return void
     */
    public function createOrderItemsTax(OrderInterface $order);

    /**
     * Traitement de la commande.
     *
     * @return void
     */
    public function process();

    /**
     * Url d'action d'exécution de la commande.
     * @internal Requête de type POST; l'url doit être intégrée en tant qu'attribut "action" d'une balise d'ouverture de formulaire ayant pour attribut "method" POST.
     *
     * @return string
     */
    public function processUrl();
}