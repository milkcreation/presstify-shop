<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop\Api\Endpoint;

use League\Fractal\Resource\Collection as FractalCollect;
use tiFy\Plugins\Shop\Contracts\{
    ApiEndpointBaseWpPost as BaseWpPostContract,
    ApiEndpointOrders as OrdersContract,
    Order,
    OrderItemProduct
};
use tiFy\Support\DateTime;
use tiFy\Support\Proxy\Request;

class Orders extends BaseWpPost implements OrdersContract
{
    /**
     * Liste des statuts disponibles.
     * @var array
     */
    protected $statuses = [
        'cancelled',
        'completed',
        'failed',
        'on-hold',
        'pending',
        'processing',
    ];

    /**
     * @inheritDoc
     */
    public function defaults(): array
    {
        return array_merge(parent::defaults(), [
            'per_page' => 100,
            'status'   => 'processing',
        ]);
    }

    /**
     * @inheritDoc
     */
    public function fetch(): void
    {
        $this->parse();

        if ($id = $this->getId()) {
            $this->args('p', $id);
        } else {
            $this
                ->fetchTransactionId()
                ->fetchDateRange()
                ->fetchPage()
                ->fetchPerPage()
                ->fetchOrder()
                ->fetchOrderBy()
                ->fetchStatus();
        }
    }

    /**
     * {@inheritDoc}
     *
     * @return $this
     */
    public function fetchStatus(): BaseWpPostContract
    {
        parent::fetchStatus();

        $status = $this->args('post_status', []);

        $status = ($status === 'any') ? $this->statuses : (array)$status;

        array_walk($status, function (&$value) {
            $value = "order-{$value}";
        });

        $this->args(['post_status' => $status]);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function fetchTransactionId(): OrdersContract
    {
        if ($transaction = Request::input('transaction_id', '')) {
            $transIds = array_unique(array_map('trim', explode(',', $transaction)));

            $meta_query = $this->args('meta_query', []);

            if (!isset($meta_query['relation'])) {
                $meta_query['relation'] = 'AND';
            }

            $meta_query[] = [
                'key'     => '_transaction_id',
                'value'   => $transIds,
                'compare' => 'IN',
            ];

            $this->args(['meta_query' => $meta_query]);
        }

        return $this;
    }

    /**
     * {@inheritDoc}
     *
     * @param Order $order
     */
    public function mapData($order): array
    {
        $lines = new FractalCollect($order->getOrderItems('line_item'), [$this, 'mapOrderItem']);

        return [
            'id'             => $order->getId(),
            'title'          => $order->getTitle(),
            'date'           => $order->getDate(),
            'status'         => $order->getShortStatus(),
            'order_total'    => $order->getTotal(),
            'total_paid'     => $order->get('date_paid') ? $order->getTotal() : 0,
            'date_paid'      => $order->get('date_paid')
                ? DateTime::createFromTimestamp($order->get('date_paid'), DateTime::getGlobalTimeZone())
                    ->toDateTimeString() : '0000-00-00 00:00:00',
            'transaction_id' => $order->get('transaction_id') ?: 0,
            'payment_method' => $order->getPaymentMethod(),
            'customer'       => ($user = get_userdata($order->getCustomerId()))
                ? array_intersect_key(
                    $user->to_array(),
                    array_flip(['ID', 'user_login', 'user_email', 'display_name'])
                ) : $order->getCustomerId(),
            'billing'        => $order->get('billing'),
            'lines'          => $this->manager()->createData($lines)->toArray(),
        ];
    }

    /**
     * {@inheritDoc}
     *
     * @param OrderItemProduct $orderItem
     */
    public function mapOrderItem(OrderItemProduct $orderItem): array
    {
        return [
            'id'                 => $orderItem->getId(),
            'sku'                => $orderItem->get('product_sku'),
            'title'              => $orderItem->getName(),
            'quantity'           => $orderItem->getQuantity(),
            'total'              => $orderItem->getTotal(),
            'purchasing_options' => $orderItem->get('purchasing_options', []),
        ];
    }

    /**
     * @inheritDoc
     */
    public function query(string $key, $default = null)
    {
        if (is_null($this->query)) {
            $order = $this->shop()->order();


            $this->query = $order::fetchFromArgs($this->args()->all());
        }

        return $this->query->get($key, $default);
    }
}