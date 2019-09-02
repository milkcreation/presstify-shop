<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop\Gateways;

/**
 * Plateforme de paiement à la livraison.
 */
class CashOnDeliveryGateway extends AbstractGateway
{
    /**
     * Identifiant de qualification de la plateforme.
     * @var string
     */
    protected $id = 'cash_on_delivery';

    /**
     * @inheritDoc
     */
    public function defaults(): array
    {
        return array_merge(parent::defaults(), [
            'availability'         => '',
            'choosen'              => false,
            'countries'            => [],
            'description'          => '',
            'has_fields'           => false,
            'icon'                 => '',
            'logger'               => [],
            'max_amount'           => 0,
            'method_title'         => __('Paiement à la livraison', 'tify'),
            'method_description'   => __('Permet le paiement à la livraison', 'tify'),
            'order_button_text'    => '',
            'supports'             => ['products'],
            'title'                => '',
            'tokens'               => [],
            'view_transaction_url' => '',
        ]);
    }
}