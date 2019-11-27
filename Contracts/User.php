<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop\Contracts;

use tiFy\Wordpress\Contracts\Query\QueryUser;

interface User extends QueryUser, ShopAwareTrait
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
    public function getBillingAddress1(): string;

    /**
     * Récupération de la ligne complémentaire de l'adresse de facturation.
     *
     * @return string
     */
    public function getBillingAddress2(): string;

    /**
     * Récupération de la ville de l'adresse de facturation.
     *
     * @return string
     */
    public function getBillingCity(): string;

    /**
     * Récupération du nom de famille de l'adresse de facturation.
     *
     * @return string
     */
    public function getBillingCompany(): string;

    /**
     * Récupération du pays de l'adresse de facturation.
     *
     * @return string
     */
    public function getBillingCountry(): string;

    /**
     * Récupération de l'email de l'adresse de facturation.
     *
     * @return string
     */
    public function getBillingEmail(): string;

    /**
     * Récupération du prénom de l'adresse de facturation.
     *
     * @return string
     */
    public function getBillingFirstName(): string;

    /**
     * Récupération du nom de famille de l'adresse de facturation.
     *
     * @return string
     */
    public function getBillingLastName(): string;

    /**
     * Récupération du numéro de téléphone de l'adresse de facturation.
     *
     * @return string
     */
    public function getBillingPhone(): string;

    /**
     * Récupération du code postal de l'adresse de facturation.
     *
     * @return string
     */
    public function getBillingPostcode(): string;

    /**
     * Vérifie si un utilisateur est considéré en tant que client.
     *
     * @return boolean
     */
    public function isCustomer(): bool;

    /**
     * Vérifie si un utilisateur est considéré en tant que client.
     *
     * @return boolean
     */
    public function isShopManager(): bool;
}