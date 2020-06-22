<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop\Contracts;

interface PageFunctions extends ShopAwareTrait
{
    /**
     * Url d'ajout d'un moyen de paiement.
     *
     * @return string
     */
    public function addPaymentMethodPageUrl(): string;

    /**
     * Url de la page d'affichage du panier.
     *
     * @return string
     */
    public function cartPageUrl(): string;

    /**
     * Url de suppression d'un moyen de paiement.
     *
     * @return string
     */
    public function deletePaymentMethodPageUrl(): string;

    /**
     * Url de la page de paiement.
     *
     * @return string
     */
    public function checkoutPageUrl(): string;

    /**
     * Url de la page de paiement.
     *
     * @param string[] $args
     *
     * @return string
     */
    public function orderPayPageUrl(array $args = []): string;

    /**
     * Url de la page de paiement reçu.
     *
     * @param string[] $args
     *
     * @return string
     */
    public function orderReceivedPageUrl(array $args = []): string;

    /**
     * Url de la boutique.
     *
     * @return string
     */
    public function shopPageUrl(): string;

    /**
     * Url des conditions générales de vente.
     *
     * @return string
     */
    public function termsPageUrl(): string;

    /**
     * Vérifie si la page d'affichage courante est la page du panier.
     *
     * @return boolean
     */
    public function isCart(): bool;

    /**
     * Vérifie si la page d'affichage courante est la page de commande.
     *
     * @return boolean
     */
    public function isCheckout(): bool;

    /**
     * Vérifie si la page d'affichage courante est la page d'accueil de la boutique.
     *
     * @return boolean
     */
    public function isShop(): bool;

    /**
     * Vérifie si la page d'affichage courante est la page des conditions générales de vente.
     *
     * @return boolean
     */
    public function isTerms(): bool;
}