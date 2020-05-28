<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop\Addresses;

use tiFy\Plugins\Shop\Contracts\{Addresses as AddressesContract, AddressBilling, AddressShipping, Shop};
use tiFy\Plugins\Shop\ShopAwareTrait;

class Addresses implements AddressesContract
{
    use ShopAwareTrait;

    /**
     * CONSTRUCTEUR
     *
     * @param Shop $shop
     *
     * @return void
     */
    public function __construct(Shop $shop)
    {
        $this->setShop($shop);

        $this->boot();

        form()->addonRegister('shop.addresses.form-handler', function () {
            return $this->shop()->resolve('addresses.form-handler');
        });

        $this->billing();
        $this->shipping();
    }

    /**
     * @inheritDoc
     */
    public function billing(): AddressBilling
    {
        return $this->shop()->getContainer()->get('shop.addresses.billing');
    }

    /**
     * @inheritDoc
     */
    public function boot(): void { }

    /**
     * @inheritDoc
     */
    public function defaultFields(): array
    {
        return [
            'first_name' => [
                'title'    => __('Prénom', 'tify'),
                'type'     => 'text',
                'required' => true,
                'attrs'    => [
                    'autocomplete' => 'given-name',
                ],
                'order'    => 10,
            ],
            'last_name'  => [
                'title'    => __('Nom de famille', 'tify'),
                'type'     => 'text',
                'required' => true,
                'attrs'    => [
                    'autocomplete' => 'family-name',
                ],
                'order'    => 20,
            ],
            'company'    => [
                'title' => __('Société', 'tify'),
                'type'  => 'text',
                'attrs' => [
                    'autocomplete' => 'organization',
                ],
                'order' => 30,
            ],
            'country'    => [
                'title'    => __('Pays', 'tify'),
                'type'     => 'text',
                'required' => true,
                'attrs'    => [
                    'autocomplete' => 'country',
                ],
                'order'    => 40,
            ],
            'address_1'  => [
                'title'    => __('Numéro et nom de rue', 'tify'),
                'type'     => 'text',
                'required' => true,
                'attrs'    => [
                    'autocomplete' => 'address-line1',
                    'placeholder'  => esc_attr__('N° et rue', 'tify'),
                ],
                'order'    => 50,
            ],
            'address_2'  => [
                'title' => __('Complément d\'adresse', 'tify'),
                'type'  => 'text',
                'attrs' => [
                    'autocomplete' => 'address-line2',
                    'placeholder'  => esc_attr__('Appartement, porte, bureau, etc. (optionnel)', 'tify'),
                ],
                'order' => 60,
            ],
            'city'       => [
                'title'    => __('Ville', 'tify'),
                'type'     => 'text',
                'required' => true,
                'attrs'    => [
                    'autocomplete' => 'address-level2',
                ],
                'order'    => 70,
            ],
            'state'      => [
                'title'    => __('Département / Région', 'tify'),
                'type'     => 'text',
                'required' => true,
                'attrs'    => [
                    'autocomplete' => 'address-level1',
                ],
                'order'    => 80,
            ],
            'postcode'   => [
                'title'    => __('Code postal', 'tify'),
                'type'     => 'text',
                'required' => true,
                'attrs'    => [
                    'autocomplete' => 'postal-code',
                ],
                'order'    => 90,
            ],
        ];
    }

    /**
     * @inheritDoc
     */
    public function shipping(): AddressShipping
    {
        return $this->shop()->getContainer()->get('shop.addresses.shipping');
    }
}