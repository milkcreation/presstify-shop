<?php

namespace tiFy\Plugins\Shop\Checkout;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use tiFy\Plugins\Shop\Shop;
use tiFy\Plugins\Shop\Orders\OrderInterface;

interface CheckoutInterface
{

    /**
     * Instanciation de la classe.
     *
     * @param Shop $shop Classe de rappel de la boutique.
     *
     * @return Checkout
     */
    public static function make(Shop $shop);

    /**
     * Url d'action d'exécution de la commande.
     *
     * @internal Requête de type POST; l'url doit être intégrée en tant qu'attribut "action" d'une balise d'ouverture de formulaire ayant pour attribut "method" POST.
     *
     * @return string
     */
    public function processUrl();

    /**
     * Traitement de la commande.
     *
     * @param ServerRequestInterface $psrRequest Requête HTTP Psr-7.
     * @param ResponseInterface $psrResponse Requête HTTP Psr-7.
     *
     * @return void
     */
    public function process(ServerRequestInterface $psrRequest, ResponseInterface $psrResponse);

    /**
     * Ajout des élements du panier à la commande.
     *
     * @param OrderInterface $order Classe de rappel de la commande relative au paiement.
     *
     * @return void
     */
    public function createOrderItemsProduct(OrderInterface $order);

    /**
     * Ajout des élements de promotion à la commande.
     *
     * @param OrderInterface $order Classe de rappel de la commande relative au paiement.
     *
     * @return void
     */
    public function createOrderItemsFee(OrderInterface $order);

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
     * Ajout des élements de bon de réduction à la commande.
     *
     * @param OrderInterface $order Classe de rappel de la commande relative au paiement.
     *
     * @return void
     */
    public function createOrderItemsCoupon(OrderInterface $order);
}