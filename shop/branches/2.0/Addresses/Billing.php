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
     * Récupération de la liste des champs de formulaire
     * @see \tiFy\Form\Controller\Field
     *
     * @return array
     */
    public function fields()
    {
        $fields = parent::fields();

        $fields['phone'] = [
            'label'        => __('Numéro de téléphone', 'tify'),
            'required'     => true,
            'autocomplete' => 'phone',
            'order'        => 100
        ];

        $fields['email'] = [
            'label'        => __('Adresse de messagerie', 'tify'),
            'required'     => true,
            'autocomplete' => 'email',
            'order'        => 110
        ];

        return $fields;
    }
}