<?php

/**
 * @name \tiFy\Plugins\Shop\Gateways\CashOnDelivery\CashOnDelivery
 * @desc Plateforme de paiement à la livraison.
 *
 * @author Jordy Manner <jordy@tigreblanc.fr>
 * @copyright Milkcreation
 */

namespace tiFy\Plugins\Shop\Gateways\CashOnDelivery;

use tiFy\Plugins\Shop\Gateways\AbstractGateway;

class CashOnDelivery extends AbstractGateway
{
    /**
     * {@inheritdoc}
     */
    public function defaults()
    {
        return [
            'order_button_text'    => '',
            'enabled'              => true,
            'title'                => '',
            'description'          => '',
            'method_title'         => __('Paiement à la livraison', 'tify'),
            'method_description'   => __('Permet le paiement à la livraison', 'tify'),
            'has_fields'           => false,
            'countries'            => [],
            'availability'         => '',
            'icon'                 => '',
            'choosen'              => false,
            'supports'             => ['products'],
            'max_amount'           => 0,
            'view_transaction_url' => '',
            'tokens'               => []
        ];
    }
}