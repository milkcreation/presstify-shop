<?php

namespace tiFy\Plugins\Shop\Addresses;

use tiFy\Plugins\Shop\Contracts\AddressBillingInterface;

/**
 * Class Billing
 *
 * @desc Gestion des adresses de facturation.
 */
class Billing extends AbstractAddress implements AddressBillingInterface
{
    /**
     * {@inheritdoc}
     */
    public function fields()
    {
        $fields = parent::fields();

        $fields['phone'] = [
            'title'        => __('Numéro de téléphone', 'tify'),
            'type'         => 'text',
            'required'     => true,
            'attrs'        => [
                'autocomplete' => 'phone',
            ],
            'order'        => 100
        ];

        $fields['email'] = [
            'title'        => __('Adresse de messagerie', 'tify'),
            'type'         => 'text',
            'required'     => true,
            'attrs'        => [
                'autocomplete' => 'email',
            ],
            'order'        => 110
        ];

        return $fields;
    }
}