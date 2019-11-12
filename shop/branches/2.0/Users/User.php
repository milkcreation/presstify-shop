<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop\Users;

use tiFy\Plugins\Shop\Contracts\{User as UserContract};
use tiFy\Plugins\Shop\{Shop, ShopAwareTrait};
use tiFy\Wordpress\{Contracts\Query\QueryUser as QueryUserContract, Query\QueryUser};
use WP_User;

class User extends QueryUser implements UserContract
{
    use ShopAwareTrait;

    /**
     * @inheritDoc
     */
    public function __construct(?WP_User $wp_user = null)
    {
        $this->setShop(Shop::instance());

        parent::__construct($wp_user);
    }

    /**
     * @inheritDoc
     */
    public function getBillingAddress(): array
    {
        $value = is_multisite()
            ? get_user_option('_billing_address', $this->getId())
            : get_user_meta($this->getId(), '_billing_address');

        return $value ? : [];
    }

    /**
     * @inheritDoc
     */
    public function getBillingAddress1(): string
    {
        $value = is_multisite()
            ? get_user_option('billing_address_1', $this->getId())
            : get_user_meta($this->getId(), 'billing_address_1');

        return $value ? : '';
    }

    /**
     * @inheritDoc
     */
    public function getBillingAddress2(): string
    {
        $value = is_multisite()
            ? get_user_option('billing_address_2', $this->getId())
            : get_user_meta($this->getId(), 'billing_address_2');

        return $value ? : '';
    }

    /**
     * @inheritDoc
     */
    public function getBillingCity(): string
    {
        $value = is_multisite()
            ? get_user_option('billing_city', $this->getId())
            : get_user_meta($this->getId(), 'billing_city');

        return $value ? : '';
    }

    /**
     * @inheritDoc
     */
    public function getBillingCompany(): string
    {
        $value = is_multisite()
            ? get_user_option('billing_company', $this->getId())
            : get_user_meta($this->getId(), 'billing_company');

        return $value ? : '';
    }

    /**
     * @inheritDoc
     */
    public function getBillingCountry(): string
    {
        $value = is_multisite()
            ? get_user_option('billing_country', $this->getId())
            : get_user_meta($this->getId(), 'billing_country');

        return $value ? : '';
    }

    /**
     * @inheritDoc
     */
    public function getBillingEmail(): string
    {
        $value = is_multisite()
            ? get_user_option('billing_email', $this->getId())
            : get_user_meta($this->getId(), 'billing_email');

        return $value ? : '';
    }

    /**
     * @inheritDoc
     */
    public function getBillingFirstName(): string
    {
        $value = is_multisite()
            ? get_user_option('billing_first_name', $this->getId())
            : get_user_meta($this->getId(), 'billing_first_name');

        return $value ? : '';
    }

    /**
     * @inheritDoc
     */
    public function getBillingLastName(): string
    {
        $value = is_multisite()
            ? get_user_option('billing_last_name', $this->getId())
            : get_user_meta($this->getId(), 'billing_last_name');

        return $value ? : '';
    }

    /**
     * @inheritDoc
     */
    public function getBillingPhone(): string
    {
        $value = is_multisite()
            ? get_user_option('billing_phone', $this->getId())
            : get_user_meta($this->getId(), 'billing_phone');

        return $value ? : '';
    }

    /**
     * @inheritDoc
     */
    public function getBillingPostcode(): string
    {
        $value = is_multisite()
            ? get_user_option('billing_postcode', $this->getId())
            : get_user_meta($this->getId(), 'billing_postcode');

        return $value ? : '';
    }

    /**
     * @inheritDoc
     */
    public function isCustomer(): bool
    {
        return $this->hasRole('customer');
    }

    /**
     * @inheritDoc
     */
    public function isShopManager(): bool
    {
        return $this->hasRole('shop_manager');
    }
}