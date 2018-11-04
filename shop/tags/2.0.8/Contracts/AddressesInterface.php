<?php

namespace tiFy\Plugins\Shop\Contracts;

use tiFy\Plugins\Shop\Contracts\AddressBillingInterface;
use tiFy\Plugins\Shop\Contracts\AddressShippingInterface;
use tiFy\Plugins\Shop\Contracts\BootableControllerInterface;
use tiFy\Plugins\Shop\Contracts\ShopResolverInterface;

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