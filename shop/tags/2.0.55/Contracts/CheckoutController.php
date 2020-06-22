<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop\Contracts;

use tiFy\Contracts\Http\Response;

interface CheckoutController extends ShopAwareTrait
{
    /**
     * Traitement du paiement de la commande.
     *
     * @return Response
     */
    public function handle(): Response;
}