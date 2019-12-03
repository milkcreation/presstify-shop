<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop\Contracts;

interface FunctionsUrl extends ShopAwareTrait
{
    /**
     * Url vers la page d'affichage du panier.
     *
     * @return string
     */
    public function cartPage(): string;

    /**
     * Url vers la page d'ajout de moyen de paiement.
     * @todo EVOLUTION : Mettre en oeuvre
     *
     * @return string
     */
    public function checkoutAddPaymentMethodPage(): string;

    /**
     * Url vers la page de suppression de moyen de paiement.
     * @todo EVOLUTION : Mettre en oeuvre
     *
     * @return string
     */
    public function checkoutDeletePaymentMethodPage(): string;

    /**
     * Url vers la page de commande.
     *
     * @return string
     */
    public function checkoutPage(): string;

    /**
     * Url vers la page de demande de paiement de la commande.
     *
     * @param array $args Liste des arguments de requête.
     *
     * @return string
     */
    public function checkoutOrderPayPage(array $args = []): string;

    /**
     * Url vers la page de commande reçue.
     *
     * @param array $args Liste des arguments de requête.
     *
     * @return string
     */
    public function checkoutOrderReceivedPage(array $args = []): string;

    /**
     * Url vers la page de définition du moyen de paiement par défaut.
     * @todo EVOLUTION : Mettre en oeuvre
     *
     * @return string
     */
    public function checkoutSetDefaultPaymentMethodPage(): string;

    /**
     * Url vers une page de la boutique.
     *
     * @param string $name Nom de la page. shop|cart|checkout|terms.
     *
     * @return string
     */
    public function page(string $name): string;

    /**
     * Url vers la page d'accueil de la boutique.
     *
     * @return string
     */
    public function shopPage(): string;

    /**
     * Url vers la page des conditions générales de vente.
     *
     * @return string
     */
    public function termsPage(): string;
}