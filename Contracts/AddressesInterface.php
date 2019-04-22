<?php

namespace tiFy\Plugins\Shop\Contracts;

interface AddressesInterface extends BootableControllerInterface, ShopResolverInterface
{
    /**
     * Récupération du controleur de gestion de l'adresse de facturation.
     *
     * @return AddressBillingInterface
     */
    public function billing();

    /**
     * Définition des champs de formulaire par défaut.
     *
     * @return array
     */
    public function defaultFields();

    /**
     * Récupération du controleur de gestion de l'adresse de facturation.
     *
     * @return AddressShippingInterface
     */
    public function shipping();
}