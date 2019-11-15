<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop\Api\Endpoint;

use League\Fractal\Resource\Collection;
use tiFy\Contracts\Http\Request;
use tiFy\Plugins\Shop\Contracts\{Order, OrderItemTypeProduct};
use tiFy\Support\DateTime;

class Orders extends EndpointWpPost
{
    /**
     * Statut par défaut.
     * @var string
     */
    protected $status = 'processing';

    /**
     * Nombre d'élément par page par défaut.
     * @var int
     */
    protected $per_page = 100;

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
        'processing'
    ];

    /**
     * {@inheritdoc}
     */
    public function getItems(Request $request)
    {
        $this->set($request->all())->parse();

        return $this->shop()->orders()->query($this->get('query_args', []));
    }

    /**
     * {@inheritdoc}
     */
    public function parse()
    {
        parent::parse();

        if (!$this->id) {
            $meta_query = ['relation' => 'AND'];
            if ($shop = $this->parseShop()) {
                $meta_query[] = [
                    'key'     => '_shop_pxv_id',
                    'value'   => $shop,
                    'compare' => 'IN'
                ];
            }
            if ($transaction_id = $this->parseTransactionId()) {
                $meta_query[] = [
                    'key'     => '_transaction_id',
                    'value'   => $transaction_id,
                    'compare' => 'IN'
                ];
            }
            $this->set('query_args.meta_query', $meta_query);
        }
    }

    /**
     * Traitement de la liste des boutiques.
     *
     * @return array
     */
    public function parseShop()
    {
        if ($shop = request()->get('shop', '')) {
            $shop = array_unique(array_map('trim', explode(',', $shop)));
        }
        return $shop;
    }

    /**
     * {@inheritdoc}
     */
    public function parseStatus()
    {
        $status = parent::parseStatus();

        return array_map(function ($value) {
            return "order-{$value}";
        }, $status);
    }

    /**
     * Traitement du numéro de transaction.
     *
     * @return array
     */
    public function parseTransactionId()
    {
        if ($transaction_id = request()->get('transaction_id', '')) {
            $transaction_id = array_unique(array_map('trim', explode(',', $transaction_id)));
        }
        return $transaction_id;
    }

    /**
     * {@inheritdoc}
     *
     * @param Order $item
     *
     * @return array
     */
    public function setItem(Order $item)
    {
        $lines = new Collection($item->getItems('line_item'), [$this, 'setLineItem']);

        return [
            'id'             => $item->getId(),
            'title'          => $item->getTitle(),
            'date'           => $item->getDate(),
            'status'         => $item->getShortStatus(),
            'order_total'    => $item->getTotal(),
            'total_paid'     => $item->get('date_paid') ? $item->getTotal() : 0,
            'date_paid'      => $item->get('date_paid')
                ? DateTime::createFromTimestamp($item->get('date_paid'), DateTime::getGlobalTimeZone())
                    ->toDateTimeString()
                : '0000-00-00 00:00:00',
            'transaction_id' => $item->get('transaction_id') ?: 0,
            'payment_method' => $item->getPaymentMethod(),
            'customer'       => ($user = get_userdata($item->getCustomerId()))
                ? array_intersect_key(
                    $user->to_array(),
                    array_flip(['ID', 'user_login', 'user_email', 'display_name'])
                )
                : $item->getCustomerId(),
            'billing'        => $item->get('billing'),
            'lines'          => $this->getManager()->createData($lines)->toArray()
        ];
    }

    /**
     * {@inheritdoc}
     *
     * @param OrderItemTypeProduct $item
     *
     * @return array
     */
    public function setLineItem(OrderItemTypeProduct $item)
    {
        return [
            'id'                 => $item->getId(),
            'sku'                => $item->get('product_sku'),
            'title'              => $item->getName(),
            'quantity'           => $item->getQuantity(),
            'total'              => $item->getTotal(),
            'purchasing_options' => $item->get('purchasing_options', []),
        ];
    }
}