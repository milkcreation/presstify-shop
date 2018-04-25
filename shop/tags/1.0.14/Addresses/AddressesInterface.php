<?php

namespace tiFy\Plugins\Shop\Addresses;

use tiFy\Plugins\Shop\Shop;

interface AddressesInterface
{
    /**
     * Instanciation de la classe
     * @param Shop $shop
     * @return AddressesInterface
     */
    public static function make(Shop $shop);

    /**
     * Récupération du controleur de gestion de l'adresse de facturation
     *
     * @return Billing
     */
    public function billing();

    /**
     * Récupération du controleur de gestion de l'adresse de facturation
     *
     * @return Shipping
     */
    public function shipping();

    /**
     * Définition des champs de formulaire par défaut
     *
     * @return array
     */
    public function defaultFields();
}