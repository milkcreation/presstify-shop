<?php

namespace tiFy\Plugins\Shop\Users;

use tiFy\User\Query\UserQueryItem;
use tiFy\Plugins\Shop\Contracts\UserItemInterface;
use tiFy\Plugins\Shop\Shop;
use tiFy\Plugins\Shop\ShopResolverTrait;
use \WP_User;

class UserItem extends UserQueryItem implements UserItemInterface
{
    use ShopResolverTrait;

    /**
     * CONSTRUCTEUR.
     *
     * @return void
     */
    public function __construct(WP_User $user, Shop $shop)
    {
        $this->shop = $shop;

        parent::__construct($user);
    }

    /**
     * {@inheritdoc}
     */
    public function getBillingAddress()
    {
        return get_user_meta($this->getId(), '_billing_address', true) ? : [];
    }

    /**
     * {@inheritdoc}
     */
    public function getBillingAddress1()
    {
        return get_user_meta($this->getId(), 'billing_address_1', true) ? : '';
    }

    /**
     * {@inheritdoc}
     */
    public function getBillingAddress2()
    {
        return get_user_meta($this->getId(), 'billing_address_2', true) ? : '';
    }

    /**
     * {@inheritdoc}
     */
    public function getBillingCity()
    {
        return get_user_meta($this->getId(), 'billing_city', true) ? : '';
    }

    /**
     * {@inheritdoc}
     */
    public function getBillingCompany()
    {
        return get_user_meta($this->getId(), 'billing_company', true) ? : '';
    }

    /**
     * {@inheritdoc}
     */
    public function getBillingCountry()
    {
        return get_user_meta($this->getId(), 'billing_country', true) ? : '';
    }

    /**
     * {@inheritdoc}
     */
    public function getBillingEmail()
    {
        return get_user_meta($this->getId(), 'billing_email', true) ? : $this->getEmail();
    }

    /**
     * {@inheritdoc}
     */
    public function getBillingFirstName()
    {
        return get_user_meta($this->getId(), 'billing_first_name', true) ? : $this->getFirstName();
    }

    /**
     * {@inheritdoc}
     */
    public function getBillingLastName()
    {
        return get_user_meta($this->getId(), 'billing_last_name', true) ? : $this->getLastName();
    }

    /**
     * {@inheritdoc}
     */
    public function getBillingPhone()
    {
        return get_user_meta($this->getId(), 'billing_phone', true) ? : '';
    }

    /**
     * {@inheritdoc}
     */
    public function getBillingPostcode()
    {
        return get_user_meta($this->getId(), 'billing_postcode', true) ? : '';
    }

    /**
     * {@inheritdoc}
     */
    public function isCustomer()
    {
        return $this->hasRole('customer');
    }

    /**
     * {@inheritdoc}
     */
    public function isShopManager()
    {
        return $this->hasRole('shop_manager');
    }
}