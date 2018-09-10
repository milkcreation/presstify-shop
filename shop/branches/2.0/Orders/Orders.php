<?php

/**
 * @name Orders
 * @desc Controleur de gestion des commandes.
 *
 * @author Jordy Manner <jordy@tigreblanc.fr>
 * @copyright Milkcreation
 */

namespace tiFy\Plugins\Shop\Orders;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use tiFy\Db\Db;
use tiFy\Db\DbControllerInterface;
use tiFy\PostType\Query\PostQuery;
use tiFy\Plugins\Shop\Contracts\OrdersInterface;
use tiFy\Plugins\Shop\Shop;
use tiFy\Plugins\Shop\ShopResolverTrait;

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
     * @var DbControllerInterface
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
     * @return null
     */
    protected function __construct(Shop $shop)
    {
        $this->shop = $shop;
    }

    /**
     * Court-circuitage de l'implémentation.
     *
     * @return null
     */
    private function __clone()
    {

    }

    /**
     * Court-circuitage de l'implémentation.
     *
     * @return null
     */
    private function __wakeup()
    {

    }

    /**
     * Instanciation de la classe.
     *
     * @param Shop $shop
     *
     * @return AddressesInterface
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
        $this->_initDb();

        add_action('init', [$this, 'onInit']);
        add_action('get_header', [$this, 'onReceived']);
    }

    /**
     * Initialisation de la table de base de données.
     *
     * @return DbControllerInterface
     */
    private function _initDb()
    {
        /** @var DB $dbController */
        $dbController = app(Db::class);

        $this->db = $dbController->register(
            '_tiFyShopOrderItems',
            [
                'install'    => false,
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
        $this->db->install();

        return $this->db;
    }

    /**
     * {@inheritdoc}
     */
    public function create()
    {
        if (! $id = \wp_insert_post(['post_type' => $this->objectName])) :
            return null;
        endif;

        return $this->get($id);
    }

    /**
     * {@inheritdoc}
     */
    public function getCollection($query_args = [])
    {
        if (!isset($query_args['post_status'])) :
            $query_args['post_status'] = $this->orders()->getRelPostStatuses();
        endif;

        return parent::getCollection($query_args);
    }

    /**
     * {@inheritdoc}
     */
    public function getDb()
    {
        if ($this->db instanceof DbControllerInterface) :
            return $this->db;
        endif;

        return null;
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
        if (is_numeric($id) && $id > 0) :
            $post = $id;
        elseif (is_string($id)) :
            return self::getBy('name', $id);
        elseif (! $id) :
            $post = $this->session()->get('order_awaiting_payment', 0);
        else :
            $post = $id;
        endif;

        if (!$post = \get_post($post)) :
            return null;
        endif;

        if (!$post instanceof \WP_Post) :
            return null;
        endif;

        if (($post->post_type !== 'any') && !in_array($post->post_type, (array) $this->getObjectName())) :
            return null;
        endif;

        $alias = 'shop.orders.order.' . $post->ID;
        if (!app()->has($alias)) :
            app()->singleton(
                $alias,
                function() use ($post) {
                    return $this->resolveItem($post);
                }
            );
        endif;

        return app()->resolve($alias);
    }

    /**
     * {@inheritdoc}
     */
    public function geItemBy($key = 'name', $value)
    {
        return parent::getBy($key, $value);
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
                'label'                     => _x('En attente de paiement', 'tify_shop_order_status', 'tify'),
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
                'label'                     => _x('En cours', 'tify_shop_order_status', 'tify'),
                'public'                    => false,
                'exclude_from_search'       => false,
                'show_in_admin_all_list'    => true,
                'show_in_admin_status_list' => true,
                'label_count'               => _n_noop(
                    'En cours <span class="count">(%s)</span>',
                    'En cours <span class="count">(%s)</span>',
                    'tify'
                ),
            ],
            'order-on-hold'    => [
                'label'                     => _x('En attente', 'tify_shop_order_status', 'tify'),
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
                'label'                     => _x('Terminée', 'tify_shop_order_status', 'tify'),
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
                'label'                     => _x('Annulée', 'tify_shop_order_status', 'tify'),
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
                'label'                     => _x('Remboursée', 'tify_shop_order_status', 'tify'),
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
                'label'                     => _x('Echouée', 'tify_shop_order_status', 'tify'),
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
     * Initialisation globale de Wordpress.
     *
     * @return void
     */
    public function onInit()
    {
        // Déclaration de la liste des statuts de commande
        foreach ($this->getRegisteredStatuses() as $order_status => $values) :
            \register_post_status($order_status, $values);
        endforeach;
    }

    /**
     * Evénement lancé à l'issue du paiement.
     *
     * @return void
     */
    public function onReceived()
    {
        if ($order_id = request()->getProperty('GET')->getInt('order-received', 0)) :
            $order_key = request()->getProperty('GET')->get('key', '');

            if (($order = $this->orders()->get($order_id)) && ($order->getOrderKey() === $order_key)) :
                $this->cart()->destroy();
            endif;
        endif;

        if ($order_awaiting_payment = (int)$this->session()->get('order_awaiting_payment')) :
            if (($order = $this->orders()->get($order_awaiting_payment)) && ! $order->hasStatus($this->getNotEmptyCartStatus())) :
                $this->cart()->destroy();
            endif;
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
    public function resolveItem($wp_post)
    {
        return app('shop.orders.order', [$wp_post, $this->shop]);
    }
}