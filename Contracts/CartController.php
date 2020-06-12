<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop\Contracts;

use tiFy\Contracts\Http\Response;

interface CartController extends ShopAwareTrait
{
    /**
     * Traitement de l'ajout d'un article au panier.
     *
     * @param string $product_name
     *
     * @return Response
     */
    public function add(string $product_name): Response;

    /**
     * Traitement de la suppression d'un article du panier.
     *
     * @param string $line_key
     *
     * @return Response
     */
    public function delete(string $line_key): Response;

    /**
     * Traitement de la mise à jour du panier de commande.
     *
     * @return Response
     */
    public function update(): Response;
}