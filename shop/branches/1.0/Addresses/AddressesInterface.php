<?php

namespace tiFy\Plugins\Shop\Addresses;

interface AddressesInterface
{

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