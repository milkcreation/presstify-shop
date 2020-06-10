<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop\Contracts;

interface Route extends ShopAwareTrait
{
    /**
     * Initialisation.
     *
     * @return static
     */
    public function boot(): Route;

    /**
     * Url d'ajout d'un article au panier.
     *
     * @param string $product_name
     *
     * @return string
     */
    public function cartAddUrl(string $product_name): string;

    /**
     * Url de suppression d'une ligne du panier.
     *
     * @param string $line_key
     *
     * @return string
     */
    public function cartDeleteUrl(string $line_key): string;

    /**
     * Url de mise à jour du panier.
     *
     * @return string
     */
    public function cartUpdateUrl(): string;

    /**
     * Url de traitement du paiement.
     *
     * @return string
     */
    public function checkoutHandleUrl(): string;
}