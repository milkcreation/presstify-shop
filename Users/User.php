<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop\Users;

use tiFy\Plugins\Shop\Contracts\User as UserContract;
use tiFy\Plugins\Shop\{Shop, ShopAwareTrait};
use tiFy\Wordpress\Query\QueryUser;
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
    public function getBilling(string $key, $default = null)
    {
        return is_multisite()
            ? $this->getOption("_billing_{$key}", $default)
            : $this->getMetaSingle("_billing_{$key}", $default);
    }

    /**
     * @inheritDoc
     */
    public function getBillingAddress1(): ?string
    {
        return $this->getBilling('address_1');
    }

    /**
     * @inheritDoc
     */
    public function getBillingAddress2(): ?string
    {
        return $this->getBilling('address_2');
    }

    /**
     * @inheritDoc
     */
    public function getBillingCity(): ?string
    {
        return $this->getBilling('city');
    }

    /**
     * @inheritDoc
     */
    public function getBillingCompany(): ?string
    {
        return $this->getBilling('company');
    }

    /**
     * @inheritDoc
     */
    public function getBillingCountry(): ?string
    {
        return $this->getBilling('country');
    }

    /**
     * @inheritDoc
     */
    public function getBillingEmail(): ?string
    {
        return $this->getBilling('email');
    }

    /**
     * @inheritDoc
     */
    public function getBillingFirstName(): ?string
    {
        return $this->getBilling('first_name');
    }

    /**
     * @inheritDoc
     */
    public function getBillingLastName(): ?string
    {
        return $this->getBilling('last_name');
    }

    /**
     * @inheritDoc
     */
    public function getBillingPhone(): ?string
    {
        return $this->getBilling('phone');
    }

    /**
     * @inheritDoc
     */
    public function getBillingPostcode(): ?string
    {
        return $this->getBilling('postcode');
    }

    /**
     * @inheritDoc
     */
    public function getShipping(string $key, $default = null)
    {
        return is_multisite()
            ? $this->getOption("_shipping_{$key}", $default)
            : $this->getMetaSingle("_shipping_{$key}", $default);
    }

    /**
     * @inheritDoc
     */
    public function getShippingAddress1(): ?string
    {
        return $this->getShipping('address_1');
    }

    /**
     * @inheritDoc
     */
    public function getShippingAddress2(): ?string
    {
        return $this->getShipping('address_2');
    }

    /**
     * @inheritDoc
     */
    public function getShippingCity(): ?string
    {
        return $this->getShipping('city');
    }

    /**
     * @inheritDoc
     */
    public function getShippingCompany(): ?string
    {
        return $this->getShipping('company');
    }

    /**
     * @inheritDoc
     */
    public function getShippingCountry(): ?string
    {
        return $this->getShipping('country');
    }

    /**
     * @inheritDoc
     */
    public function getShippingEmail(): ?string
    {
        return $this->getShipping('email');
    }

    /**
     * @inheritDoc
     */
    public function getShippingFirstName(): ?string
    {
        return $this->getShipping('first_name');
    }

    /**
     * @inheritDoc
     */
    public function getShippingLastName(): ?string
    {
        return $this->getShipping('last_name');
    }

    /**
     * @inheritDoc
     */
    public function getShippingPhone(): ?string
    {
        return $this->getShipping('phone');
    }

    /**
     * @inheritDoc
     */
    public function getShippingPostcode(): ?string
    {
        return $this->getShipping('postcode');
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