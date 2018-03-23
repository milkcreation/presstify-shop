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
     * Url vers la page des conditions générales de vente
     * @return string
     */
    public function termsPage();
}