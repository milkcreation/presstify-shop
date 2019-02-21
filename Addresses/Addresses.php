<?php

namespace tiFy\Plugins\Shop\Addresses;

use tiFy\Plugins\Shop\AbstractShopSingleton;
use tiFy\Plugins\Shop\Contracts\AddressesInterface;
use tiFy\Plugins\Shop\Shop;

/**
 * Class Addresses
 *
 * @desc Gestion des adresses : livraison|facturation|pays
 */
class Addresses extends AbstractShopSingleton implements AddressesInterface
{
    /**
     * {@inheritdoc}
     */
    public function __construct(Shop $shop)
    {
        parent::__construct($shop);
    }

    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        form()->addonRegister(
            'shop.addresses.form_handler',
            app('shop.addresses.form_handler', [$this->shop])
        );

        $this->billing();
        $this->shipping();
    }

    /**
     * {@inheritdoc}
     */
    public function billing()
    {
        return app('shop.addresses.billing', [$this, $this->shop]);
    }

    /**
     * {@inheritdoc}
     */
    public function defaultFields()
    {
        return [
            'first_name' => [
                'title'        => __('Prénom', 'tify'),
                'type'         => 'text',
                'required'     => true,
                'attrs'        => [
                    'autocomplete' => 'given-name',
                ],
                'order'        => 10,
            ],
            'last_name'  => [
                'title'        => __('Nom de famille', 'tify'),
                'type'         => 'text',
                'required'     => true,
                'attrs'        => [
                    'autocomplete' => 'family-name',
                ],
                'order'        => 20,
            ],
            'company'    => [
                'title'        => __('Société', 'tify'),
                'type'         => 'text',
                'attrs'        => [
                    'autocomplete' => 'organization',
                ],
                'order'        => 30,
            ],
            'country'    => [
                'title'        => __('Pays', 'tify'),
                'type'         => 'text',
                'required'     => true,
                'attrs'        => [
                    'autocomplete' => 'country',
                ],
                'order'        => 40,
            ],
            'address_1'  => [
                'title'        => __('Numéro et nom de rue', 'tify'),
                'type'         => 'text',
                'required'     => true,
                'attrs'        => [
                    'autocomplete' => 'address-line1',
                    'placeholder'  => esc_attr__('N° et rue', 'tify'),
                ],
                'order'        => 50,
            ],
            'address_2'  => [
                'title'        => __('Complément d\'adresse', 'tify'),
                'type'         => 'text',
                'attrs'        => [
                    'autocomplete' => 'address-line2',
                    'placeholder'  => esc_attr__('Appartement, porte, bureau, etc. (optionnel)', 'tify'),
                ],
                'order'        => 60,
            ],
            'city'       => [
                'title'        => __('Ville', 'tify'),
                'type'         => 'text',
                'required'     => true,
                'attrs'        => [
                    'autocomplete' => 'address-level2',
                ],
                'order'        => 70,
            ],
            'state'      => [
                'title'        => __('Département / Région', 'tify'),
                'type'         => 'text',
                'required'     => true,
                'attrs'        => [
                    'autocomplete' => 'address-level1',
                ],
                'order'        => 80,
            ],
            'postcode'   => [
                'title'        => __('Code postal', 'tify'),
                'type'         => 'text',
                'required'     => true,
                'attrs'        => [
                    'autocomplete' => 'postal-code',
                ],
                'order'        => 90,
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function shipping()
    {
        return app('shop.addresses.shipping', [$this, $this->shop]);
    }
}