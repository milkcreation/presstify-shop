<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop\Contracts;

interface Addresses extends ShopAwareTrait
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
     * @return AddressBilling
     */
    public function billing(): AddressBilling;

    /**
     * Définition des champs de formulaire par défaut.
     *
     * @return array
     */
    public function defaultFields(): array;

    /**
     * Récupération du controleur de gestion de l'adresse de facturation.
     *
     * @return AddressShipping
     */
    public function shipping(): AddressShipping;
}