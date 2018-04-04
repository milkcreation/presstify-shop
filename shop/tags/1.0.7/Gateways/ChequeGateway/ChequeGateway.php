<?php

/**
 * @name ChequeGateway
 * @desc Plateforme de paiement par chèque
 * @package presstiFy
 * @namespace \tiFy\Plugins\Shop\Gateways\ChequeGateway
 * @version 1.1
 * @since 1.2.617
 *
 * @author Jordy Manner <jordy@tigreblanc.fr>
 * @copyright Milkcreation
 */

namespace tiFy\Plugins\Shop\Gateways\ChequeGateway;

use tiFy\Plugins\Shop\Gateways\AbstractGateway;
use tiFy\Plugins\Shop\Orders\OrderInterface;

class ChequeGateway extends AbstractGateway
{
    /**
     * Récupération des attributs de configuration par défaut
     *
     * @return array
     */
    public function getDefaults()
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
     * Procède au paiement de la commande.
     *
     * @param OrderInterface $order Classe de rappel de la commande à régler.
     *
     * @return array {
     *      Liste des attributs de retour.
     *
     *      @var string $result Résultat de paiement success|error.
     *      @var string $redirect Url de retour
     * }
     */
    public function processPayment($order)
    {
        // @todo Mise à jour du status vers en attente

        // @todo Mise à jour des stocks

        // Suppression des éléments du panier
        $this->cart()->destroy();

        return [
            'result'    => 'success',
            'redirect'  => $this->getReturnUrl($order)
        ];
    }
}