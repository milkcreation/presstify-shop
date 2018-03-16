<?php

namespace tiFy\Plugins\Shop\Users;

use tiFy\Core\Query\Controller\UserItemInterface;

interface UserInterface extends UserItemInterface
{
    /**
     * Vérifie si un utilisateur est considéré en tant que client
     *
     * @return bool
     */
    public function isCustomer();

    /**
     * Vérifie si un utilisateur est considéré en tant que client
     *
     * @return bool
     */
    public function isShopManager();

    /**
     * Récupération du prénom de l'adresse de facturation
     * @return string
     */
    public function getBillingFirstName();

    /**
     * Récupération du nom de famille de l'adresse de facturation
     * @return string
     */
    public function getBillingLastName();

    /**
     * Récupération du nom de famille de l'adresse de facturation
     * @return string
     */
    public function getBillingCompany();

    /**
     * Récupération de la ligne principale de l'adresse de facturation
     * @return string
     */
    public function getBillingAddress1();

    /**
     * Récupération de la ligne complémentaire de l'adresse de facturation
     * @return string
     */
    public function getBillingAddress2();

    /**
     * Récupération de la ville de l'adresse de facturation
     * @return string
     */
    public function getBillingCity();

    /**
     * Récupération du code postal de l'adresse de facturation
     * @return string
     */
    public function getBillingPostcode();

    /**
     * Récupération du pays de l'adresse de facturation
     * @return string
     */
    public function getBillingCountry();

    /**
     * Récupération du numéro de téléphone de l'adresse de facturation
     * @return string
     */
    public function getBillingPhone();

    /**
     * Récupération de l'email de l'adresse de facturation
     * @return string
     */
    public function getBillingEmail();
}