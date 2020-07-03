<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop\Contracts;

use tiFy\Wordpress\Contracts\Query\QueryUser;

interface User extends QueryUser, ShopAwareTrait
{
    /**
     * Récupération d'attribut d'adresse de facturation.
     *
     * @param string $key
     * @param mixed $default
     *
     * @return mixed
     */
    public function getBilling(string $key, $default = null);

    /**
     * Récupération de la ligne principale de l'adresse de facturation.
     *
     * @return string|null
     */
    public function getBillingAddress1(): ?string;

    /**
     * Récupération de la ligne complémentaire de l'adresse de facturation.
     *
     * @return string|null
     */
    public function getBillingAddress2(): ?string;

    /**
     * Récupération de la ville de l'adresse de facturation.
     *
     * @return string|null
     */
    public function getBillingCity(): ?string;

    /**
     * Récupération du nom de la société de l'adresse de facturation.
     *
     * @return string|null
     */
    public function getBillingCompany(): ?string;

    /**
     * Récupération du pays de l'adresse de facturation.
     *
     * @return string|null
     */
    public function getBillingCountry(): ?string;

    /**
     * Récupération de l'email de l'adresse de facturation.
     *
     * @return string|null
     */
    public function getBillingEmail(): ?string;

    /**
     * Récupération du prénom de l'adresse de facturation.
     *
     * @return string|null
     */
    public function getBillingFirstName(): ?string;

    /**
     * Récupération du nom de famille de l'adresse de facturation.
     *
     * @return string|null
     */
    public function getBillingLastName(): ?string;

    /**
     * Récupération du numéro de téléphone de l'adresse de facturation.
     *
     * @return string|null
     */
    public function getBillingPhone(): ?string;

    /**
     * Récupération du code postal de l'adresse de facturation.
     *
     * @return string|null
     */
    public function getBillingPostcode(): ?string;

    /**
     * Récupération d'attribut d'adresse de livraison.
     *
     * @param string $key
     * @param mixed $default
     *
     * @return mixed
     */
    public function getShipping(string $key, $default = null);

    /**
     * Récupération de la ligne principale de l'adresse de livraison.
     *
     * @return string|null
     */
    public function getShippingAddress1(): ?string;

    /**
     * Récupération de la ligne complémentaire de l'adresse de livraison.
     *
     * @return string|null
     */
    public function getShippingAddress2(): ?string;

    /**
     * Récupération de la ville de l'adresse de livraison.
     *
     * @return string|null
     */
    public function getShippingCity(): ?string;

    /**
     * Récupération du nom de la société de l'adresse de livraison.
     *
     * @return string|null
     */
    public function getShippingCompany(): ?string;

    /**
     * Récupération du pays de l'adresse de livraison.
     *
     * @return string|null
     */
    public function getShippingCountry(): ?string;

    /**
     * Récupération de l'email de l'adresse de livraison.
     *
     * @return string|null
     */
    public function getShippingEmail(): ?string;

    /**
     * Récupération du prénom de l'adresse de livraison.
     *
     * @return string|null
     */
    public function getShippingFirstName(): ?string;

    /**
     * Récupération du nom de famille de l'adresse de livraison.
     *
     * @return string|null
     */
    public function getShippingLastName(): ?string;

    /**
     * Récupération du numéro de téléphone de l'adresse de livraison.
     *
     * @return string|null
     */
    public function getShippingPhone(): ?string;

    /**
     * Récupération du code postal de l'adresse de livraison.
     *
     * @return string|null
     */
    public function getShippingPostcode(): ?string;

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