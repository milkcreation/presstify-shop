<?php

namespace tiFy\Plugins\Shop\Contracts;

use tiFy\Contracts\User\UserQueryItemInterface;

interface UserItemInterface extends UserQueryItemInterface
{
    /**
     * Récupération de l'adresse de facturation.
     *
     * @return mixed
     */
    public function getBillingAddress();

    /**
     * Récupération de la ligne principale de l'adresse de facturation.
     *
     * @return string
     */
    public function getBillingAddress1();

    /**
     * Récupération de la ligne complémentaire de l'adresse de facturation.
     *
     * @return string
     */
    public function getBillingAddress2();

    /**
     * Récupération de la ville de l'adresse de facturation.
     *
     * @return string
     */
    public function getBillingCity();

    /**
     * Récupération du nom de famille de l'adresse de facturation.
     *
     * @return string
     */
    public function getBillingCompany();

    /**
     * Récupération du pays de l'adresse de facturation.
     *
     * @return string
     */
    public function getBillingCountry();

    /**
     * Récupération de l'email de l'adresse de facturation.
     *
     * @return string
     */
    public function getBillingEmail();

    /**
     * Récupération du prénom de l'adresse de facturation.
     *
     * @return string
     */
    public function getBillingFirstName();

    /**
     * Récupération du nom de famille de l'adresse de facturation.
     *
     * @return string
     */
    public function getBillingLastName();

    /**
     * Récupération du numéro de téléphone de l'adresse de facturation.
     *
     * @return string
     */
    public function getBillingPhone();

    /**
     * Récupération du code postal de l'adresse de facturation.
     *
     * @return string
     */
    public function getBillingPostcode();

    /**
     * Vérifie si un utilisateur est considéré en tant que client.
     *
     * @return bool
     */
    public function isCustomer();
    /**
     * Vérifie si un utilisateur est considéré en tant que client.
     *
     * @return bool
     */
    public function isShopManager();
}