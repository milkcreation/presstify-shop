<?php

namespace tiFy\Plugins\Shop\Checkout;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use tiFy\Plugins\Shop\Shop;

interface CheckoutInterface
{

    /**
     * Instanciation de la classe
     * @param Shop $shop
     * @return Checkout
     */
    public static function make(Shop $shop);

    /**
     * Url d'action d'exécution de la commande
     * @internal Requête de type POST; l'url doit être intégrée en tant qu'attribut "action" d'une balise d'ouverture de formulaire ayant pour attribut "method" POST
     * @return string
     */
    public function processUrl();

    /**
     * Traitement de la commande
     * @param ServerRequestInterface $psrRequest Requête HTTP Psr-7
     * @param ResponseInterface $psrResponse Requête HTTP Psr-7
     * @return void
     */
    public function process(ServerRequestInterface $psrRequest, ResponseInterface $psrResponse);
}