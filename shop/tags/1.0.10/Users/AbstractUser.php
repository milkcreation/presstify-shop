<?php

namespace tiFy\Plugins\Shop\Users;

use tiFy\Core\Query\Controller\AbstractUserItem;
use tiFy\Plugins\Shop\ServiceProvider\ProvideTraits;
use tiFy\Plugins\Shop\ServiceProvider\ProvideTraitsInterface;
use tiFy\Plugins\Shop\Shop;
use WP_User;

abstract class AbstractUser extends AbstractUserItem implements UserInterface, ProvideTraitsInterface
{
    use ProvideTraits;

    /**
     * Classe de rappel de la boutique
     * @var Shop
     */
    protected $shop;

    /**
     * CONSTRUCTEUR.
     *
     * @return void
     */
    public function __construct(WP_User $user, Shop $shop)
    {
        // Définition de la classe de rappel de la boutique
        $this->shop = $shop;

        parent::__construct($user);
    }

    /**
     * Vérifie si un utilisateur est considéré en tant que client
     *
     * @return bool
     */
    public function isCustomer()
    {
        return $this->hasRole('customer');
    }

    /**
     * Vérifie si un utilisateur est considéré en tant que client
     *
     * @return bool
     */
    public function isShopManager()
    {
        return $this->hasRole('shop_manager');
    }

    /**
     * Récupération du prénom de l'adresse de facturation
     *
     * @return string
     */
    public function getBillingFirstName()
    {
        return get_user_meta($this->getId(), 'billing_first_name', true) ? : $this->getFirstName();
    }

    /**
     * Récupération du nom de famille de l'adresse de facturation
     *
     * @return string
     */
    public function getBillingLastName()
    {
        return get_user_meta($this->getId(), 'billing_last_name', true) ? : $this->getLastName();
    }

    /**
     * Récupération du nom de famille de l'adresse de facturation
     *
     * @return string
     */
    public function getBillingCompany()
    {
        return get_user_meta($this->getId(), 'billing_company', true) ? : '';
    }

    /**
     * Récupération de la ligne principale de l'adresse de facturation
     *
     * @return string
     */
    public function getBillingAddress1()
    {
        return get_user_meta($this->getId(), 'billing_address_1', true) ? : '';
    }

    /**
     * Récupération de la ligne complémentaire de l'adresse de facturation
     *
     * @return string
     */
    public function getBillingAddress2()
    {
        return get_user_meta($this->getId(), 'billing_address_2', true) ? : '';
    }

    /**
     * Récupération de la ville de l'adresse de facturation
     *
     * @return string
     */
    public function getBillingCity()
    {
        return get_user_meta($this->getId(), 'billing_city', true) ? : '';
    }

    /**
     * Récupération du code postal de l'adresse de facturation
     *
     * @return string
     */
    public function getBillingPostcode()
    {
        return get_user_meta($this->getId(), 'billing_postcode', true) ? : '';
    }

    /**
     * Récupération du pays de l'adresse de facturation
     *
     * @return string
     */
    public function getBillingCountry()
    {
        return get_user_meta($this->getId(), 'billing_country', true) ? : '';
    }

    /**
     * Récupération du numéro de téléphone de l'adresse de facturation
     *
     * @return string
     */
    public function getBillingPhone()
    {
        return get_user_meta($this->getId(), 'billing_phone', true) ? : '';
    }

    /**
     * Récupération de l'email de l'adresse de facturation
     *
     * @return string
     */
    public function getBillingEmail()
    {
        return get_user_meta($this->getId(), 'billing_email', true) ? : $this->getEmail();
    }

    /**
     * Récupération de l'adresse de facturation
     *
     * @return mixed
     */
    public function getBillingAddress()
    {
        return get_user_meta($this->getId(), '_billing_address', true) ? : [];
    }
}