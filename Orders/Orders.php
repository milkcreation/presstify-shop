<?php

namespace tiFy\Plugins\Shop\Orders;

use Illuminate\{
    Support\Arr,
    Support\Collection
};
use tiFy\Contracts\Db\DbFactory;
use tiFy\PostType\Query\PostQuery;
use tiFy\Plugins\Shop\{
    Contracts\OrdersInterface,
    Contracts\OrderInterface,
    Shop,
    ShopResolverTrait
};
use tiFy\Support\Proxy\{Redirect, Request};
use WP_Post;

/**
 * Class Orders
 *
 * @desc Controleur de gestion des commandes.
 */
class Orders extends PostQuery implements OrdersInterface
{
    use ShopResolverTrait;

    /**
     * Instance de la classe.
     * @var static
     */
    protected static $instance;

    /**
     * Classe de rappel de la base de données
     * @var DbFactory
     */
    protected $db;

    /**
     * Liste des statuts de commande.
     * @var array
     */
    protected $statuses = [];

    /**
     * Type de post Wordpress du controleur
     * @var string|array
     */
    protected $objectName = 'shop_order';

    /**
     * CONSTRUCTEUR.
     *
     * @param Shop $shop Classe de rappel de la boutique.
     *
     * @return void
     */
    protected function __construct(Shop $shop)
    {
        $this->shop = $shop;
    }

    /**
     * Court-circuitage de l'implémentation.
     *
     * @return void
     */
    private function __clone()
    {

    }

    /**
     * Court-circuitage de l'implémentation.
     *
     * @return void
     */
    private function __wakeup()
    {

    }

    /**
     * Instanciation de la classe.
     *
     * @param string $alias Nom de qualification
     * @param Shop $shop
     *
     * @return static
     */
    public static function make($alias, Shop $shop)
    {
        if (self::$instance) :
            return self::$instance;
        endif;

        return self::$instance = new static($shop);
    }

    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        db()->register(
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
        );

        add_action('init', [$this, 'onInit']);
        add_action('get_header', [$this, 'onReceived']);
    }

    /**
     * {@inheritdoc}
     */
    public function create()
    {
        if (! $id = wp_insert_post(['post_type' => $this->objectName])) {
            return null;
        }
        return $this->getItem($id);
    }

    /**
     * {@inheritdoc}
     */
    public function getCollection($query_args = null)
    {
        if (!isset($query_args['post_status'])) {
            $query_args['post_status'] = $this->orders()->getRelPostStatuses();
        }

        return parent::getCollection($query_args);
    }

    /**
     * {@inheritdoc}
     */
    public function getDb()
    {
        if ($this->db instanceof DbFactory) :
            return $this->db;
        else :
            return $this->db = db()->get('shop.order.items');
        endif;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultStatus()
    {
        return 'order-pending';
    }

    /**
     * {@inheritdoc}
     */
    public function getItem($id = null)
    {
        if (!$id) :
            $id = $this->session()->get('order_awaiting_payment', 0);
        endif;

        return parent::getItem($id);
    }

    /**
     * {@inheritdoc}
     */
    public function getNeedPaymentStatuses()
    {
        return ['order-failed', 'order-pending'];
    }

    /**
     * {@inheritdoc}
     */
    public function getNotEmptyCartStatus()
    {
        return ['order-cancelled', 'order-failed', 'order-pending'];
    }

    /**
     * {@inheritdoc}
     */
    public function getPaymentCompleteStatuses()
    {
        return ['order-completed', 'order-processing'];
    }

    /**
     * {@inheritdoc}
     */
    public function getPaymentValidStatuses()
    {
        return ['order-failed', 'order-cancelled', 'order-on-hold', 'order-pending'];
    }

    /**
     * {@inheritdoc}
     */
    public function getRegisteredStatuses()
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
     * {@inheritdoc}
     */
    public function getRelPostStatuses()
    {
        return array_keys($this->getStatuses());
    }

    /**
     * {@inheritdoc}
     */
    public function getStatuses()
    {
        if ($this->statuses) :
            return $this->statuses;
        endif;

        $collect = new Collection($this->getRegisteredStatuses());

        return $this->statuses = $collect->mapWithKeys(
            function($item, $key) {
                return [$key => $item['label']];
            })
            ->all();
    }

    /**
     * {@inheritdoc}
     */
    public function getStatusLabel($name, $default = '')
    {
        return Arr::get($this->getStatuses(), $name, $default);
    }

    /**
     * {@inheritdoc}
     */
    public function is($order)
    {
        return $order instanceof OrderInterface;
    }

    /**
     * {@inheritdoc}
     */
    public function isStatus($status)
    {
        return in_array($status, array_keys($this->getStatuses()));
    }

    /**
     * @inheritDoc
     */
    public function handlePaymentComplete($order_id)
    {
        if (is_user_logged_in() && ($user = $this->users()->getItem())) {
            if ($user->isShopManager() && ($order = $this->orders()->getItem($order_id))) {
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
     * Initialisation globale de Wordpress.
     *
     * @return void
     */
    public function onInit()
    {
        // Déclaration de la liste des statuts de commande
        foreach ($this->getRegisteredStatuses() as $order_status => $values) {
            register_post_status($order_status, $values);
        }
    }

    /**
     * Evénement lancé à l'issue du paiement.
     *
     * @return void
     */
    public function onReceived()
    {
        if ($order_id = request()->query->getInt('order-received', 0)) :
            $order_key = request()->query('key', '');

            if (($order = $this->orders()->getItem($order_id)) && ($order->getOrderKey() === $order_key)) :
                $this->cart()->destroy();
            endif;
        endif;

        if (
            ($order_awaiting_payment = (int)$this->session()->get('order_awaiting_payment')) &&
            ($order = $this->orders()->getItem($order_awaiting_payment)) &&
            ! $order->hasStatus($this->getNotEmptyCartStatus())
        ) :
            $this->cart()->destroy();
        endif;
    }

    /**
     * {@inheritdoc}
     */
    public function resolveCollection($items)
    {
        return app('shop.orders.list', [$items]);
    }

    /**
     * {@inheritdoc}
     */
    public function resolveItem(WP_Post $wp_post)
    {
        return app('shop.orders.order', [$wp_post, $this->shop]);
    }
}