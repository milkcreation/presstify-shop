<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop\Contracts;

interface ApiEndpointOrders extends ApiEndpointBaseWpPost
{
    /**
     * {@inheritDoc}
     *
     * @return static
     */
    public function fetchStatus(): ApiEndpointBaseWpPost;

    /**
     * Traitement du numéro de transaction.
     *
     * @return static
     */
    public function fetchTransactionId(): ApiEndpointOrders;

    /**
     * {@inheritDoc}
     *
     * @param Order $order
     *
     * @return array
     */
    public function mapData($order): array;

    /**
     * {@inheritDoc}
     *
     * @param OrderItemProduct $orderItem
     *
     * @return array
     */
    public function mapOrderItem(OrderItemProduct $orderItem): array;
}