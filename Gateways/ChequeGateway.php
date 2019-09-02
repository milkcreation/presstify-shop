<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop\Gateways;

use tiFy\Plugins\Shop\Contracts\OrderInterface;

/**
 * Plateforme de paiement par chèque.
 */
class ChequeGateway extends AbstractGateway
{
    /**
     * Identifiant de qualification de la plateforme.
     * @var string
     */
    protected $id = 'cheque';

    /**
     * @inheritDoc
     */
    public function defaults(): array
    {
        return array_merge(parent::defaults(), [
            'availability'         => '',
            'choosen'              => false,
            'countries'            => [],
            'description'          => __('Validation de la commande après réception', 'tify'),
            'has_fields'           => false,
            'icon'                 => '',
            'logger'               => [],
            'max_amount'           => 0,
            'method_title'         => __('Paiement par chèque', 'tify'),
            'method_description'   => __('Permet le paiement par chèque', 'tify'),
            'order_button_text'    => '',
            'supports'             => ['products'],
            'title'                => __('Chèque', 'tify'),
            'tokens'               => [],
            'view_transaction_url' => '',
        ]);
    }

    /**
     * @inheritDoc
     */
    public function processPayment(OrderInterface $order): array
    {
        // @todo Mise à jour du status vers en attente.
        if ($order->getTotal() > 0) {
            $order->updateStatus('order-on-hold');
            $order->addNote(__('En attente du réglement par chèque', 'tify'));
        } else {
            $order->paymentComplete();
        }

        // @todo Mise à jour des stocks.

        // Suppression des éléments du panier.
        $this->shop->cart()->destroy();

        return [
            'result'   => 'success',
            'redirect' => $this->getReturnUrl($order),
        ];
    }
}