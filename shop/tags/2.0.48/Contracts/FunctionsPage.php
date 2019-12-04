<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop\Contracts;

interface FunctionsPage extends ShopAwareTrait
{
    /**
     * Vérifie si la page d'affichage courante correspond à un contexte défini.
     *
     * @param string $name Nom de la page. shop|cart|checkout|terms.
     *
     * @return bool
     */
    public function is(string $name): bool;

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