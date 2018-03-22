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

use LogicException;
use tiFy\Core\Db\Db;
use tiFy\Core\Db\Factory as DbFactory;
use tiFy\Core\Query\Controller\AbstractPostQuery;
use tiFy\Plugins\Shop\Shop;

class Orders extends AbstractPostQuery implements OrdersInterface
{
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
     * Type de post Wordpress du controleur
     * @var string|array
     */
    protected $objectName = 'order';

    /**
     * CONSTRUCTEUR
     *
     * @param Shop $shop Classe de rappel de la boutique
     *
     * @return void
     */
    private function __construct(Shop $shop)
    {
        // Définition de la classe de rappel de la boutique
        $this->shop = $shop;

        // Intialisation de la base de données
        $this->initDb();
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
     * Instanciation de la classe
     *
     * @param Shop $shop
     *
     * @return Orders
     */
    final public static function boot(Shop $shop)
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
     * Initialisation de la table de base de données
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
     * Récupération du controleur de données d'un élément
     *
     * @return string
     */
    final public function getItemController()
    {
        return $this->shop->provider()->getMapController('orders.item');
    }

    /**
     * Récupération du controleur de données d'une liste d'éléments
     *
     * @return string
     */
    final public function getListController()
    {
        return $this->shop->provider()->getMapController('orders.list');
    }

    /**
     * Récupération d'un élément
     *
     * @param string|int|\WP_Post|null $id Nom de qualification du post WP (slug, post_name)|Identifiant de qualification du post WP|Object post WP|Post WP  de la page courante
     *
     * @return null|object|OrderInterface
     */
    public function get($id = null)
    {
        if (is_string($id)) :
            return self::getBy('name', $id);
        elseif (!$id) :
            $post = get_the_ID();
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
     * Récupération d'un élément selon un attribut particulier
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
     * Récupération des données d'une liste d'élément selon des critères de requête
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
     * Récupération du controleur de base de données
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
     * Création d'une nouvelle commande
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
     * Vérifie d'intégrité d'une commande
     *
     * @param OrderInterface $order
     *
     * @param bool
     */
    public function is($order)
    {
        return $order instanceof OrderInterface;
    }
}