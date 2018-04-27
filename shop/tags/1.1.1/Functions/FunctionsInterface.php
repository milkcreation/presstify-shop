<?php

namespace tiFy\Plugins\Shop\Functions;

use tiFy\Plugins\Shop\Shop;

interface FunctionsInterface
{
    /**
     * Instanciation de la classe.
     * @param Shop $shop
     * @return Functions
     */
    public static function make(Shop $shop);

    /**
     * Alias de récupération du fournisseur de gestion des contextes d'affichage.
     * @param string $time Date à traité. now par défaut.
     * @see http://php.net/manual/fr/class.datetime.php
     * @return object|DateInterface
     */
    public function date($time = 'now', $timezone = true);

    /**
     * Alias de récupération du fournisseur de gestion des contextes d'affichage.
     *
     * @return object|PageInterface
     */
    public function page();

    /**
     * Alias de récupération du fournisseur de gestion des tarifs de la boutique.
     * @return object|PriceInterface
     */
    public function price();

    /**
     * Alias de récupération du fournisseur de gestion des urls de la boutique.
     * @return object|UrlInterface
     */
    public function url();
}