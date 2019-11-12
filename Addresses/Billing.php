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
            'title'    => __('Numéro de téléphone', 'tify'),
            'type'     => 'text',
            'required' => true,
            'attrs'    => [
                'autocomplete' => 'phone',
            ],
            'order'    => 100,
        ];

        $fields['email'] = [
            'title'    => __('Adresse de messagerie', 'tify'),
            'type'     => 'text',
            'required' => true,
            'attrs'    => [
                'autocomplete' => 'email',
            ],
            'order'    => 110,
        ];

        return $fields;
    }
}