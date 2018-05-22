<?php

/**
 * @name Billing
 * @desc Gestion des adresses de facturation
 * @package presstiFy
 * @namespace \tiFy\Plugins\Shop\Address
 * @version 1.1
 * @since 1.2.600
 *
 * @author Jordy Manner <jordy@tigreblanc.fr>
 * @copyright Milkcreation
 */

namespace tiFy\Plugins\Shop\Addresses;

class Billing extends AbstractAddress implements BillingInterface
{
    /**
     * Récupération de la liste des champs de formulaire.
     *
     * @return array
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