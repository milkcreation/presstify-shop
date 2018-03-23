<?php

namespace tiFy\Plugins\Shop\Functions;

interface UrlInterface
{
    /**
     * Url vers une page de la boutique
     *
     * @param string $name Nom de la page. shop|cart|checkout|terms.
     *
     * @return string
     */
    public function page($name);
    /**
     * Url vers la page d'accueil de la boutique
     * @return string
     */
    public function shopPage();

    /**
     * Url vers la page d'affichage du panier
     * @return string
     */
    public function cartPage();

    /**
     * Url vers la page de commande
     * @return string
     */
    public function checkoutPage();

    /**
     * Url vers la page de commande reçue.
     * @param array $args Liste des arguments de requête
     * @return string
     */
    public function checkoutOrderReceivedPage($args = []);

    /**
     * Url vers la page d'ajout de moyen de paiement.
     * @return string
     */
    public function checkoutAddPaymentMethodPage();

    /**
     * Url vers la page de suppression de moyen de paiement.
     *
     * @return string
     */
    public function checkoutDeletePaymentMethodPage();

    /**
     * Url vers la page de définition du moyen de paiement par défaut.
     *
     * @return string
     */
    public function checkoutSetDefaultPaymentMethodPage();

    /**
     * Url vers la page des conditions générales de vente
     * @return string
     */
    public function termsPage();
}