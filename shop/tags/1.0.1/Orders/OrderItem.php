<?php

/**
 * @name OrderItem
 * @desc Controleur de commande
 * @package presstiFy
 * @namespace \tiFy\Plugins\Shop\Orders
 * @version 1.1
 * @since 1.4.2
 *
 * @author Jordy Manner <jordy@tigreblanc.fr>
 * @copyright Milkcreation
 */

namespace tiFy\Plugins\Shop\Orders;

use tiFy\Core\Query\Controller\AbstractPostItem;
use tiFy\Plugins\Shop\Shop;

class OrderItem extends AbstractPostItem implements OrderItemInterface
{
    /**
     * Classe de rappel de la boutique
     * @var Shop
     */
    protected $shop;

    /**
     * Liste des données de commande par défaut
     *
     * @var array
     */
    protected $defaults = array(
        // Abstract order props
        'parent_id'            => 0,
        'status'               => '',
        'currency'             => '',
        'version'              => '',
        'prices_include_tax'   => false,
        'date_created'         => null,
        'date_modified'        => null,
        'discount_total'       => 0,
        'discount_tax'         => 0,
        'shipping_total'       => 0,
        'shipping_tax'         => 0,
        'cart_tax'             => 0,
        'total'                => 0,
        'total_tax'            => 0,

        // Order props
        'customer_id'          => 0,
        'order_key'            => '',
        'billing'              => array(
            'first_name'       => '',
            'last_name'        => '',
            'company'          => '',
            'address_1'        => '',
            'address_2'        => '',
            'city'             => '',
            'state'            => '',
            'postcode'         => '',
            'country'          => '',
            'email'            => '',
            'phone'            => '',
        ),
        'shipping'             => array(
            'first_name'       => '',
            'last_name'        => '',
            'company'          => '',
            'address_1'        => '',
            'address_2'        => '',
            'city'             => '',
            'state'            => '',
            'postcode'         => '',
            'country'          => '',
        ),
        'payment_method'       => '',
        'payment_method_title' => '',
        'transaction_id'       => '',
        'customer_ip_address'  => '',
        'customer_user_agent'  => '',
        'created_via'          => '',
        'customer_note'        => '',
        'date_completed'       => null,
        'date_paid'            => null,
        'cart_hash'            => ''
    );

    /**
     * CONSTRUCTEUR
     *
     * @param Shop $shop Classe de rappel de la boutique
     * @param \WP_Post $post
     *
     * @return void
     */
    public function __construct(Shop $shop, \WP_Post $post)
    {
        // Définition de la classe de rappel de la boutique
        $this->shop = $shop;

        parent::__construct($post);
        $this->attributes = array_merge($this->attributes, $this->defaults);
    }
}