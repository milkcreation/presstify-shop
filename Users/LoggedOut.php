<?php

namespace tiFy\Plugins\Shop\Users;

use tiFy\Plugins\Shop\Contracts\UserItemInterface;

class LoggedOut implements UserItemInterface
{
    /**
     * {@inheritdoc}
     */
    public function can($capability)
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getBillingAddress()
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function getBillingAddress1()
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function getBillingAddress2()
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function getBillingCity()
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function getBillingCompany()
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function getBillingCountry()
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function getBillingEmail()
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function getBillingFirstName()
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function getBillingLastName()
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function getBillingPhone()
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function getBillingPostcode()
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function getDisplayName()
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function getEmail()
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function getFirstName()
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 0;
    }

    /**
     * {@inheritdoc}
     */
    public function getLastName()
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function getLogin()
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function getNicename()
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function getNickname()
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function getPass()
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function getRegistered()
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function getRoles()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getUrl()
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function getUser()
    {
        return new \WP_User();
    }

    /**
     * {@inheritdoc}
     */
    public function hasRole($role)
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function isCustomer()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function isLoggedIn()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function isShopManager()
    {
        return false;
    }
}