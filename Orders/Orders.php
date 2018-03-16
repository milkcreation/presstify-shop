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

use tiFy\Core\Db\Db;
use tiFy\Core\Db\Factory as DbFactory;
use tiFy\Core\Query\Controller\AbstractPostQuery;
use tiFy\Plugins\Shop\Shop;

class Orders extends AbstractPostQuery
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
     * Controleur de données d'un élément
     * @var string
     */
    protected $itemController = 'tiFy\Plugins\Shop\Orders\OrderItem';

    /**
     * Controleur de données d'une liste d'éléments
     * @var string
     */
    protected $listController = 'tiFy\Plugins\Shop\Orders\OrderList';

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
    final public static function make(Shop $shop)
    {
        if (self::$instance) :
            return self::$instance;
        endif;

        return self::$instance = new self($shop);
    }

    /**
     * Récupération d'un élément
     *
     * @param string|int|\WP_Post|null $id Nom de qualification du post WP (slug, post_name)|Identifiant de qualification du post WP|Object post WP|Post WP  de la page courante
     *
     * @return null|object|OrderItemInterface
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
     * @return null|object|OrderItemInterface
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
     * Création d'une nouvelle commande
     *
     * @return null|OrderItemInterface
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
     * @param OrderItemInterface $order
     *
     * @param bool
     */
    public function is($order)
    {
        return $order instanceof OrderItemInterface;
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
                'meta'       => 'tify_shop_order_item',
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
}