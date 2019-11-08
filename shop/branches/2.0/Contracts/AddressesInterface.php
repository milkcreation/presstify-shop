<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop\Contracts;

interface AddressesInterface extends BootableControllerInterface, ShopAwareTrait
{
    /**
     * Initialisation de la classe.
     *
     * @return void
     */
    public function boot(): void;

    /**
     * Récupération du controleur de gestion de l'adresse de facturation.
     *
     * @return AddressBillingInterface
     */
    public function billing(): AddressBillingInterface;

    /**
     * Définition des champs de formulaire par défaut.
     *
     * @return array
     */
    public function defaultFields(): array;

    /**
     * Récupération du controleur de gestion de l'adresse de facturation.
     *
     * @return AddressShippingInterface
     */
    public function shipping(): AddressShippingInterface;
}