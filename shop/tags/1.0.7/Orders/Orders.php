<?php

/**
 * @name Orders
 * @desc Controller de gestion des commandes
 * @package presstiFy
 * @namespace \tiFy\Plugins\Shop\Orders
 * @version 1.1
 * @since 1.4.2
 *
 * @author Jordy Manner <jordy@tigreblanc.fr>
 * @copyright Milkcreation
 */

namespace tiFy\Plugins\Shop\Orders;

use Illuminate\Support\Collection;
use LogicException;
use tiFy\Core\Db\Db;
use tiFy\Core\Db\Factory as DbFactory;
use tiFy\Core\Query\Controller\AbstractPostQuery;
use tiFy\Plugins\Shop\ServiceProvider\ProvideTraits;
use tiFy\Plugins\Shop\ServiceProvider\ProvideTraitsInterface;
use tiFy\Plugins\Shop\Shop;

class Orders extends AbstractPostQuery implements OrdersInterface, ProvideTraitsInterface
{
    use ProvideTraits;

    /**
     * Instance de la classe
     * @var Orders
     */
    private static $instance;

    /**
     * Classe de rappel de la boutique
     * @var Shop
     */
    protected $shop;

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
    private function __construct(Shop $shop)
    {
        // Définition de la classe de rappel de la boutique
        $this->shop = $shop;

        // Intialisation de la base de données
        $this->initDb();

        // Déclaration des événments
        $this->appAddAction('init');
        $this->appAddAction('get_header', 'onReceived');
    }

    /**
     * Court-circuitage de l'implémentation
     *
     * @return void
     */
    private function __clone()
    {

    }

    /**
     * Court-circuitage de l'implémentation
     *
     * @return void
     */
    private function __wakeup()
    {

    }

    /**
     * Instanciation de la classe.
     *
     * @param Shop $shop Classe de rappel de la boutique.
     *
     * @return Orders
     */
    final public static function make(Shop $shop)
    {
        if (self::$instance) :
            return self::$instance;
        endif;

        self::$instance = new static($shop);

        if(! self::$instance instanceof Orders) :
            throw new LogicException(
                sprintf(
                    __('Le controleur de surcharge doit hériter de %s', 'tify'),
                    Orders::class
                ),
                500
            );
        endif;

        return self::$instance;
    }

    /**
     * Initialisation de la table de base de données.
     *
     * @return DbFactory
     */
    private function initDb()
    {
        $this->db = Db::register(
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
     * Initialisation globale de Wordpress.
     *
     * @return void
     */
    final public function init()
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
    final public function onReceived()
    {
        if ($order_id = $this->appRequest('get')->getInt('order-received', 0)) :
            $order_key = $this->appRequest('get')->get('key', '');

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
     * Récupération du controleur de données d'un élément.
     *
     * @return string
     */
    final public function getItemController()
    {
        return $this->provider()->getMapController('orders.order');
    }

    /**
     * Récupération du controleur de données d'une liste d'éléments.
     *
     * @return string
     */
    final public function getListController()
    {
        return $this->provider()->getMapController('orders.list');
    }

    /**
     * Récupération d'un élément.
     *
     * @param string|int|\WP_Post|null $id Nom de qualification du post WP (slug, post_name)|Identifiant de qualification du post WP|Object post WP|Post WP  de la page courante
     *
     * @return null|object|OrderInterface
     */
    public function get($id = null)
    {
        if (is_string($id)) :
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

        $name = 'tify.query.post.' . $post->ID;
        if (! $this->appHasContainer($name)) :
            $controller = $this->getItemController();

            $this->appAddContainer($name, new $controller($this->shop, $post));
        endif;

        return $this->appGetContainer($name);
    }

    /**
     * Récupération d'un élément selon un attribut particulier.
     *
     * @param string $key Identifiant de qualification de l'attribut. défaut name.
     * @param string $value Valeur de l'attribut
     *
     * @return null|object|OrderInterface
     */
    public function getBy($key = 'name', $value)
    {
        return parent::getBy($key, $value);
    }

    /**
     * Récupération des données d'une liste d'élément selon des critères de requête.
     *
     * @param array $query_args Liste des arguments de requête
     *
     * @return array|OrderListInterface
     */
    public function getList($query_args = [])
    {
        return parent::getList($query_args);
    }

    /**
     * Récupération du controleur de base de données.
     *
     * @return null|DbFactory
     */
    public function getDb()
    {
        if ($this->db instanceof DbFactory) :
            return $this->db;
        endif;

        return null;
    }

    /**
     * Création d'une nouvelle commande.
     *
     * @return null|OrderInterface
     */
    public function create()
    {
       if (! $id = \wp_insert_post(['post_type' => $this->objectName])) :
           return null;
       endif;

       return $this->get($id);
    }

    /**
     * Vérifie d'intégrité d'une commande.
     *
     * @param OrderInterface $order
     *
     * @return bool
     */
    public function is($order)
    {
        return $order instanceof OrderInterface;
    }

    /**
     * Récupération de la liste déclaration de statut de commande.
     *
     * @return array
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
     * Récupération de la liste des statuts.
     *
     * @return array
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
     * Récupération de la liste des statuts en relation avec les post.
     *
     * @return string[]
     */
    public function getRelPostStatuses()
    {
        return array_keys($this->getStatuses());
    }

    /**
     * Vérifie si un statut correspond aux statuts de commandes.
     *
     * @param string $status Identifiant de qualification du statut à contrôler.
     *
     * @return bool
     */
    public function isStatus($status)
    {
        return in_array($status, array_keys($this->getStatuses()));
    }

    /**
     * Récupération du statut de commande par défaut.
     *
     * @return string
     */
    public function getDefaultStatus()
    {
        return 'order-pending';
    }

    /**
     * Récupération de la liste des statuts nécessitant un paiement.
     *
     * @return array
     */
    public function getNotEmptyCartStatus()
    {
        return ['order-cancelled', 'order-failed', 'order-pending'];
    }

    /**
     * Récupération de la liste des statuts nécessitant un paiement.
     *
     * @return array
     */
    public function getNeedPaymentStatuses()
    {
        return ['order-failed', 'order-pending'];
    }

    /**
     * Récupération de la liste des statuts relatif à un processus de paiement abouti.
     *
     * @return array
     */
    public function getPaymentCompleteStatuses()
    {
        return ['order-completed', 'order-processing'];
    }

    /**
     * Récupération de la liste des statuts relatif à un processus de paiement valide.
     *
     * @return array
     */
    public function getPaymentValidStatuses()
    {
        return ['order-failed', 'order-cancelled', 'order-on-hold', 'order-pending'];
    }
}