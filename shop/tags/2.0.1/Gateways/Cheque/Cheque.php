<?php

/**
 * @name ChequeGateway
 * @desc Plateforme de paiement par chèque.
 *
 * @author Jordy Manner <jordy@tigreblanc.fr>
 * @copyright Milkcreation
 */

namespace tiFy\Plugins\Shop\Gateways\Cheque;

use tiFy\Plugins\Shop\Gateways\AbstractGateway;

class Cheque extends AbstractGateway
{
    /**
     * {@inheritdoc}
     */
    public function defaults()
    {
        return [
            'order_button_text'    => '',
            'enabled'              => true,
            'title'                => __('Chèque', 'tify'),
            'description'          => __('Validation de la commande après réception', 'tify'),
            'method_title'         => __('Paiement par chèque', 'tify'),
            'method_description'   => __('Permet le paiement par chèque', 'tify'),
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

    /**
     * {@inheritdoc}
     */
    public function processPayment($order)
    {
        // @todo Mise à jour du status vers en attente.

        // @todo Mise à jour des stocks.

        // Suppression des éléments du panier.
        $this->cart()->destroy();

        return [
            'result'    => 'success',
            'redirect'  => $this->getReturnUrl($order)
        ];
    }
}