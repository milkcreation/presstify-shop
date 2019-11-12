<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop\Orders;

use Illuminate\Support\Collection;
use tiFy\Plugins\Shop\Contracts\{Order, Orders as OrdersContract, OrdersCollection, Shop};
use tiFy\Plugins\Shop\ShopAwareTrait;
use tiFy\Support\{Arr, Proxy\Redirect, Proxy\Request};

class Orders implements OrdersContract
{
    use ShopAwareTrait;

    /**
     * Liste des statuts de commande.
     * @var array
     */
    protected $statuses = [];

    /**
     * Nombre d'élément total trouvés
     * @var int
     */
    protected $total = 0;

    /**
     * CONSTRUCTEUR.
     *
     * @param Shop $shop
     *
     * @return void
     */
    public function __construct(Shop $shop)
    {
        $this->setShop($shop);

        /* db()->register(
            'shop.order.items',
            [
                'install'    => true,
                'name'       => 'tify_shop_order_items',
                'primary'    => 'order_item_id',
                'col_prefix' => 'order_item_',
                'meta'       => [
                    'meta_type' => 'tify_shop_order_item',
                    'join_col'  => 'order_item_id'
                ],
                'columns'    => [
                    'id'       => [
                        'type'           => 'BIGINT',
                        'size'           => 20,
                        'unsigned'       => true,
                        'auto_increment' => true
                    ],
                    'name'     => [
                        'type' => 'TEXT',
                    ],
                    'type'     => [
                        'type'    => 'VARCHAR',
                        'size'    => 200,
                        'default' => ''
                    ],
                    'order_id' => [
                        'type'     => 'BIGINT',
                        'size'     => 20,
                        'unsigned' => true,
                        'prefix'   => false
                    ]
                ],
                'keys'       => ['order_id' => ['cols' => 'order_id', 'type' => 'INDEX']],
            ]
        ); */

        add_action('init', [$this, 'onInit']);
        add_action('get_header', [$this, 'onReceived']);
    }

    /**
     * @inheritDoc
     */
    public function boot(): void { }

    /**
     * @inheritDoc
     */
    public function create(): ?Order
    {
        if (! $id = wp_insert_post(['post_type' => 'order'])) {
            return null;
        }
        return $this->get($id);
    }

    /**
     * @inheritDoc
     */
    public function get($id = null): ?Order
    {
        if (!$id) {
            $id = $this->shop()->session()->get('order_awaiting_payment', 0);
        }

        return $this->shop()->resolve('order', [$id]);
    }

    /**
     * @inheritDoc
     */
    public function getDefaultStatus(): string
    {
        return 'order-pending';
    }

    /**
     * @inheritDoc
     */
    public function getNeedPaymentStatuses(): array
    {
        return ['order-failed', 'order-pending'];
    }

    /**
     * @inheritDoc
     */
    public function getNotEmptyCartStatus(): array
    {
        return ['order-cancelled', 'order-failed', 'order-pending'];
    }

    /**
     * @inheritDoc
     */
    public function getPaymentCompleteStatuses(): array
    {
        return ['order-completed', 'order-processing'];
    }

    /**
     * @inheritDoc
     */
    public function getPaymentValidStatuses(): array
    {
        return ['order-failed', 'order-cancelled', 'order-on-hold', 'order-pending'];
    }

    /**
     * @inheritDoc
     */
    public function getRegisteredStatuses(): array
    {
        return [
            'order-pending'    => [
                'label'                     => _x('En attente de paiement', 'shop_order_status', 'tify'),
                'public'                    => false,
                'exclude_from_search'       => false,
                'show_in_admin_all_list'    => true,
                'show_in_admin_status_list' => true,
                'label_count'               => _n_noop(
                    'En attente de paiement <span class="count">(%s)</span>',
                    'En attente de paiement <span class="count">(%s)</span>',
                    'tify'
                ),
            ],
            'order-processing' => [
                'label'                     => _x('En préparation', 'shop_order_status', 'tify'),
                'public'                    => false,
                'exclude_from_search'       => false,
                'show_in_admin_all_list'    => true,
                'show_in_admin_status_list' => true,
                'label_count'               => _n_noop(
                    'En préparation <span class="count">(%s)</span>',
                    'En préparation <span class="count">(%s)</span>',
                    'tify'
                ),
            ],
            'order-on-hold'    => [
                'label'                     => _x('En attente', 'shop_order_status', 'tify'),
                'public'                    => false,
                'exclude_from_search'       => false,
                'show_in_admin_all_list'    => true,
                'show_in_admin_status_list' => true,
                'label_count'               => _n_noop(
                    'En attente <span class="count">(%s)</span>',
                    'En attente <span class="count">(%s)</span>',
                    'tify'
                ),
            ],
            'order-completed'  => [
                'label'                     => _x('Terminée', 'shop_order_status', 'tify'),
                'public'                    => false,
                'exclude_from_search'       => false,
                'show_in_admin_all_list'    => true,
                'show_in_admin_status_list' => true,
                'label_count'               => _n_noop(
                    'Terminée <span class="count">(%s)</span>',
                    'Terminée <span class="count">(%s)</span>',
                    'tify'
                ),
            ],
            'order-cancelled'  => [
                'label'                     => _x('Annulée', 'shop_order_status', 'tify'),
                'public'                    => false,
                'exclude_from_search'       => false,
                'show_in_admin_all_list'    => true,
                'show_in_admin_status_list' => true,
                'label_count'               => _n_noop(
                    'Annulée <span class="count">(%s)</span>',
                    'Annulée <span class="count">(%s)</span>',
                    'tify'
                ),
            ],
            'order-refunded'   => [
                'label'                     => _x('Remboursée', 'shop_order_status', 'tify'),
                'public'                    => false,
                'exclude_from_search'       => false,
                'show_in_admin_all_list'    => true,
                'show_in_admin_status_list' => true,
                'label_count'               => _n_noop(
                    'Remboursée <span class="count">(%s)</span>',
                    'Remboursée <span class="count">(%s)</span>',
                    'tify'
                ),
            ],
            'order-failed'     => [
                'label'                     => _x('Echouée', 'shop_order_status', 'tify'),
                'public'                    => false,
                'exclude_from_search'       => false,
                'show_in_admin_all_list'    => true,
                'show_in_admin_status_list' => true,
                'label_count'               => _n_noop(
                    'Echouée <span class="count">(%s)</span>',
                    'Echouée <span class="count">(%s)</span>',
                    'tify'
                ),
            ],
        ];
    }

    /**
     * @inheritDoc
     */
    public function getRelPostStatuses(): array
    {
        return array_keys($this->getStatuses());
    }

    /**
     * @inheritDoc
     */
    public function getStatuses(): array
    {
        if ($this->statuses) {
            return $this->statuses;
        }

        return $this->statuses = (new Collection($this->getRegisteredStatuses()))->mapWithKeys(function($item, $key) {
            return [$key => $item['label']];
        })->all();
    }

    /**
     * @inheritDoc
     */
    public function getStatusLabel($name, $default = ''): string
    {
        return Arr::get($this->getStatuses(), $name, $default);
    }

    /**
     * Récupération du nombre d'enregistrement total.
     *
     * @return int
     */
    public function getTotal(): int
    {
        return $this->total;
    }

    /**
     * Définition du nombre d'enregistrement total.
     *
     * @param int $total
     *
     * @return $this
     */
    public function setTotal($total): self
    {
        $this->total = $total;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function handlePaymentComplete($order_id)
    {
        if (is_user_logged_in() && ($user = $this->shop()->users()->get())) {
            if ($user->isShopManager() && ($order = $this->get($order_id))) {
                $order->paymentComplete();
            }

            $location = Request::input('_wp_http_referer')
                ?: (Request::instance()->headers->get('referer') ?: site_url('/'));

            return Redirect::to($location);
        } else {
            wp_die(
                __('Votre utilisateur n\'est pas habilité à effectuer cette action', 'tify'),
                __('Mise à jour de la commande impossible', 'tify'),
                500
            );
            return '';
        }
    }

    /**
     * @inheritDoc
     */
    public function is($order): bool
    {
        return $order instanceof Order;
    }

    /**
     * @inheritDoc
     */
    public function isStatus(string $status): bool
    {
        return in_array($status, array_keys($this->getStatuses()));
    }

    /**
     * Initialisation globale de Wordpress.
     *
     * @return void
     */
    public function onInit(): void
    {
        foreach ($this->getRegisteredStatuses() as $order_status => $values) {
            register_post_status($order_status, $values);
        }
    }

    /**
     * Evénement lancé à l'issue du paiement.
     *
     * @return void
     */
    public function onReceived(): void
    {
        if ($order_id = Request::instance()->query->getInt('order-received', 0)) {
            $order_key = Request::input('key', '');

            if (($order = $this->get($order_id)) && ($order->getOrderKey() === $order_key)) {
                $this->shop()->cart()->destroy();
            }
        }

        if (
            ($order_awaiting_payment = (int)$this->shop()->session()->get('order_awaiting_payment')) &&
            ($order = $this->get($order_awaiting_payment)) &&
            ! $order->hasStatus($this->getNotEmptyCartStatus())
        ) {
            $this->shop()->cart()->destroy();
        }
    }

    /**
     * @inheritDoc
     */
    public function query($query_args = null): OrdersCollection
    {
        if (!isset($query_args['post_status'])) {
            $query_args['post_status'] = $this->getRelPostStatuses();
        }

        return $this->shop()->resolve('orders.collection')->query($query_args);
    }
}