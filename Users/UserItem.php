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
     * @param WP_User $user
     * @param Shop $shop
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
        $value = is_multisite()
            ? get_user_option('_billing_address', $this->getId())
            : get_user_meta($this->getId(), '_billing_address');

        return $value ? : [];
    }

    /**
     * {@inheritdoc}
     */
    public function getBillingAddress1()
    {
        $value = is_multisite()
            ? get_user_option('billing_address_1', $this->getId())
            : get_user_meta($this->getId(), 'billing_address_1');

        return $value ? : '';
    }

    /**
     * {@inheritdoc}
     */
    public function getBillingAddress2()
    {
        $value = is_multisite()
            ? get_user_option('billing_address_2', $this->getId())
            : get_user_meta($this->getId(), 'billing_address_2');

        return $value ? : '';
    }

    /**
     * {@inheritdoc}
     */
    public function getBillingCity()
    {
        $value = is_multisite()
            ? get_user_option('billing_city', $this->getId())
            : get_user_meta($this->getId(), 'billing_city');

        return $value ? : '';
    }

    /**
     * {@inheritdoc}
     */
    public function getBillingCompany()
    {
        $value = is_multisite()
            ? get_user_option('billing_company', $this->getId())
            : get_user_meta($this->getId(), 'billing_company');

        return $value ? : '';
    }

    /**
     * {@inheritdoc}
     */
    public function getBillingCountry()
    {
        $value = is_multisite()
            ? get_user_option('billing_country', $this->getId())
            : get_user_meta($this->getId(), 'billing_country');

        return $value ? : '';
    }

    /**
     * {@inheritdoc}
     */
    public function getBillingEmail()
    {
        $value = is_multisite()
            ? get_user_option('billing_email', $this->getId())
            : get_user_meta($this->getId(), 'billing_email');

        return $value ? : '';
    }

    /**
     * {@inheritdoc}
     */
    public function getBillingFirstName()
    {
        $value = is_multisite()
            ? get_user_option('billing_first_name', $this->getId())
            : get_user_meta($this->getId(), 'billing_first_name');

        return $value ? : '';
    }

    /**
     * {@inheritdoc}
     */
    public function getBillingLastName()
    {
        $value = is_multisite()
            ? get_user_option('billing_last_name', $this->getId())
            : get_user_meta($this->getId(), 'billing_last_name');

        return $value ? : '';
    }

    /**
     * {@inheritdoc}
     */
    public function getBillingPhone()
    {
        $value = is_multisite()
            ? get_user_option('billing_phone', $this->getId())
            : get_user_meta($this->getId(), 'billing_phone');

        return $value ? : '';
    }

    /**
     * {@inheritdoc}
     */
    public function getBillingPostcode()
    {
        $value = is_multisite()
            ? get_user_option('billing_postcode', $this->getId())
            : get_user_meta($this->getId(), 'billing_postcode');

        return $value ? : '';
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