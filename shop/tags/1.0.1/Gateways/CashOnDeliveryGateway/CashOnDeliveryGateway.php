<?php

/**
 * @name CashOnDeliveryGateway
 * @desc Plateforme de paiement à la livraison
 * @package presstiFy
 * @namespace \tiFy\Plugins\Shop\Gateways\CashOnDeliveryGateway
 * @version 1.1
 * @since 1.2.617
 *
 * @author Jordy Manner <jordy@tigreblanc.fr>
 * @copyright Milkcreation
 */

namespace tiFy\Plugins\Shop\Gateways\CashOnDeliveryGateway;

use tiFy\Plugins\Shop\Gateways\AbstractGateway;

class CashOnDeliveryGateway extends AbstractGateway
{
    /**
     * Récupération des attributs de configuration par défaut
     *
     * @return array
     */
    public function getDefaults()
    {
        return [
            'id'                   => 'cash_on_delivery',
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