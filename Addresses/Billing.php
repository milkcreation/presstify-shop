<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop\Addresses;

use tiFy\Plugins\Shop\Contracts\AddressBilling as AddressBillingContract;

class Billing extends AbstractAddress implements AddressBillingContract
{
    /**
     * @inheritDoc
     */
    public function fields(): array
    {
        $fields = parent::fields();

        $fields['phone'] = [
            'attrs'    => [
                'autocomplete' => 'phone',
            ],
            'order'    => 100,
            'required' => true,
            'title'    => __('Numéro de téléphone', 'tify'),
            'type'     => 'text',
        ];

        $fields['email'] = [
            'attrs'       => [
                'autocomplete' => 'email',
            ],
            'order'       => 110,
            'required'    => true,
            'validations' => 'email',
            'title'       => __('Adresse de messagerie', 'tify'),
            'type'        => 'text',
        ];

        return $fields;
    }
}