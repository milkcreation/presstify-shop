<?php

namespace tiFy\Plugins\Shop\Contracts;

interface FunctionsPageInterface
{
    /**
     * Vérifie si la page d'affichage courante correspond à un contexte défini.
     *
     * @param string $name Nom de la page. shop|cart|checkout|terms.
     *
     * @return bool
     */
    public function is($name);

    /**
     * Vérifie si la page d'affichage courante est la page du panier.
     *
     * @return bool
     */
    public function isCart();

    /**
     * Vérifie si la page d'affichage courante est la page de commande.
     *
     * @return bool
     */
    public function isCheckout();

    /**
     * Vérifie si la page d'affichage courante est la page d'accueil de la boutique.
     *
     * @return bool
     */
    public function isShop();

    /**
     * Vérifie si la page d'affichage courante est la page des conditions générales de vente.
     *
     * @return bool
     */
    public function isTerms();
}