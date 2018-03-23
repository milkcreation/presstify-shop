<?php

/**
 * @name Order
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

use Illuminate\Support\Arr;
use tiFy\Core\Query\Controller\AbstractPostItem;
use tiFy\Plugins\Shop\Orders\OrderItem\OrderItemInterface;
use tiFy\Plugins\Shop\Products\ProductItemInterface;
use tiFy\Plugins\Shop\Orders\OrderItem\OrderItemCouponInterface;
use tiFy\Plugins\Shop\Orders\OrderItem\OrderItemFeeInterface;
use tiFy\Plugins\Shop\Orders\OrderItem\OrderItemProductInterface;
use tiFy\Plugins\Shop\Orders\OrderItem\OrderItemShippingInterface;
use tiFy\Plugins\Shop\Orders\OrderItem\OrderItemTaxInterface;
use tiFy\Plugins\Shop\Shop;

class Order extends AbstractPostItem implements OrderInterface
{
    /**
     * Classe de rappel de la boutique
     * @var Shop
     */
    protected $shop;

    /**
     * Liste des éléments associé à la commande
     * @var array
     */
    protected $items = [];

    /**
     * Liste des données de commande par défaut
     *
     * @var array
     */
    protected $defaults = [
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
        'billing'              => [
            'first_name' => '',
            'last_name'  => '',
            'company'    => '',
            'address_1'  => '',
            'address_2'  => '',
            'city'       => '',
            'state'      => '',
            'postcode'   => '',
            'country'    => '',
            'email'      => '',
            'phone'      => '',
        ],
        'shipping'             => [
            'first_name' => '',
            'last_name'  => '',
            'company'    => '',
            'address_1'  => '',
            'address_2'  => '',
            'city'       => '',
            'state'      => '',
            'postcode'   => '',
            'country'    => '',
        ],
        'payment_method'       => '',
        'payment_method_title' => '',
        'transaction_id'       => '',
        'customer_ip_address'  => '',
        'customer_user_agent'  => '',
        'created_via'          => '',
        'customer_note'        => '',
        'date_completed'       => null,
        'date_paid'            => null,
        'cart_hash'            => '',
    ];

    /**
     * Cartographie des attributs en correspondance avec les métadonnées enregistrées en base.
     * @var array
     */
    protected $metas_map = [
        'order_key'            => '_order_key',
        'customer_id'          => '_customer_user',
        'payment_method'       => '_payment_method',
        'payment_method_title' => '_payment_method_title',
        'transaction_id'       => '_transaction_id',
        'customer_ip_address'  => '_customer_ip_address',
        'customer_user_agent'  => '_customer_user_agent',
        'created_via'          => '_created_via',
        'date_completed'       => '_date_completed',
        'date_paid'            => '_date_paid',
        'cart_hash'            => '_cart_hash',
        'currency'             => '_order_currency',
        'discount_total'       => '_cart_discount',
        'discount_tax'         => '_cart_discount_tax',
        'shipping_total'       => '_order_shipping',
        'shipping_tax'         => '_order_shipping_tax',
        'cart_tax'             => '_order_tax',
        'total'                => '_order_total',
        'version'              => '_order_version',
        'prices_include_tax'   => '_prices_include_tax',
    ];

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

    /**
     *
     */
    public function create()
    {
        $this->set('order_key', uniqid('order_'));
    }

    /**
     * Récupération de la liste des attributs.
     *
     * @return array
     */
    public function all()
    {
        return $this->toArray();
    }

    /**
     * Définition d'un attribut.
     *
     * @param string $key Identifiant de qualification déclaré.
     * @param mixed $value Valeur de définition de l'attribut.
     *
     * @return mixed
     */
    public function set($key, $value)
    {
        if (!isset($this->defaults[$key])) :
            return null;
        endif;

        return $this[$key] = $value;
    }

    /**
     * Définition d'un attribut de l'adresse de facturation.
     *
     * @param string $key Identifiant de qualification de l'attribut.
     * @param mixed $value Valeur de définition de l'attribut.
     *
     * @return mixed
     */
    public function setBillingAttr($key, $value)
    {
        return $this->set('billing', array_merge($this->get('billing', []), [$key => $value]));
    }

    /**
     * Définition d'un attribut de l'adresse de livraison.
     *
     * @param string $key Identifiant de qualification de l'attribut.
     * @param mixed $value Valeur de définition de l'attribut.
     *
     * @return mixed
     */
    public function setShippingAttr($key, $value)
    {
        return $this->set('shipping', array_merge($this->get('shipping', []), [$key => $value]));
    }

    /**
     * Récupération du statut de publication
     *
     * @return string
     */
    public function getStatus()
    {
        return (string)$this->get('status', $this->shop->orders()->getDefaultStatus());
    }

    /**
     * Récupération de la valeur brute ou formatée de l'extrait
     *
     * @param bool $raw Formatage de la valeur
     *
     * @return string
     */
    public function getExcerpt($raw = false)
    {
        $excerpt = (string)$this->get('customer_note', '');

        if ($raw) :
            return $excerpt;
        else :
            return \apply_filters('get_the_excerpt', $excerpt, $this->getPost());
        endif;
    }

    /**
     * Création d'une ligne de coupon de réduction.
     *
     * @return object|OrderItemCouponInterface
     */
    public function createItemCoupon()
    {

    }

    /**
     * Création d'une ligne de promotion.
     *
     * @return object|OrderItemFeeInterface
     */
    public function createItemFee()
    {

    }

    /**
     * Création d'une ligne de produit.
     *
     * @return object|OrderItemProductInterface
     */
    public function createItemProduct(ProductItemInterface $product)
    {
        return $this->shop->provide('orders.item_product', [$product, $this->shop, $this]);
    }

    /**
     * Création d'une ligne de livraison.
     *
     * @return object|OrderItemShippingInterface
     */
    public function createItemShipping()
    {

    }

    /**
     * Création d'une ligne de taxe.
     *
     * @return object|OrderItemTaxInterface
     */
    public function createItemTax()
    {

    }

    /**
     * Ajout d'une ligne d'élément associé à la commande.
     *
     * @param OrderItemInterface $item
     *
     * @return void
     */
    public function addItem($item)
    {
        $type = $item->getType();

        $count = isset($this->items[$type]) ? count($this->items[$type]) : 0;
        $this->items = Arr::add($this->items, $type . '.new:' . $type . $count, $item);
    }

    /**
     * Récupération de la liste des éléments associés à la commande.
     *
     * @return array
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * Sauvegarde de la commande
     *
     * @return void
     */
    public function save()
    {
        // Mise à jour des données de post
        // @todo
        $post_data = [
            'ID'                => $this->getId(),
            'post_date'         => $this->shop->functions()->date()->utc(),
            'post_date_gmt'     => $this->shop->functions()->date()->get(),
            'post_status'       => $this->getStatus(),
            'post_parent'       => $this->getParentId(),
            'post_excerpt'      => $this->getExcerpt(true),
            'post_modified'     => $this->shop->functions()->date()->utc(),
            'post_modified_gmt' => $this->shop->functions()->date()->get(),
        ];
        \wp_update_post($post_data);

        // Sauvegarde des métadonnées
        $this->saveMetas();

        // Sauvegarde des éléments
        $this->saveItems();
    }

    /**
     * Enregistrement de la liste des métadonnées déclarées.
     *
     * @return void
     */
    public function saveMetas()
    {
        if (!$this->metas_map || ! $this->getId()) :
            return;
        endif;

        foreach ($this->metas_map as $attr_key => $meta_key) :
            $meta_value = $this->get($attr_key, '');

            switch($attr_key) :
                case 'date_paid' :
                    \update_post_meta($this->getId(), $meta_key, ! is_null($meta_value) ? $meta_value->getTimestamp() : '');
                    break;
                case 'date_completed' :
                    \update_post_meta($this->getId(), $meta_key, ! is_null($meta_value) ? $meta_value->getTimestamp() : '');
                    break;
                default :
                    \update_post_meta($this->getId(), $meta_key, $meta_value);
                    break;
            endswitch;
        endforeach;

        foreach(['billing', 'shipping'] as $address_type) :
            if (!$address_data = $this->get($address_type, [])) :
                continue;
            endif;
            foreach($address_data as $key => $value) :
                \update_post_meta($this->getId(), "_{$address_type}_{$key}", $value);
            endforeach;

            \update_post_meta($this->getId(), "_{$address_type}_address_index", implode(' ', $address_data));
        endforeach;
    }

    /**
     * Sauvegarde d'une métadonnée
     *
     * @param string $meta_key Clé d'identification de la métadonnée.
     * @param mixed $meta_value Valeur de la métadonnée
     * @param bool $unique Enregistrement unique d'une valeur pour la clé d'identification fournie.
     *
     * @return int Valeur de la clé primaire sauvegardée
     */
    public function addMeta($meta_key, $meta_value, $unique = true)
    {
        if (!$this->id) :
            return 0;
        endif;

        $db = $this->shop->orders()->getDb();

        return $db->meta()->add($this->id, $meta_key, $meta_value);
    }

    /**
     * Sauvegarde de la liste des éléments
     *
     * @return void
     */
    public function saveItems()
    {
        if (!$this->items) :
            return;
        endif;

        foreach ($this->items as $group => $group_items) :
            foreach ($group_items as $item_key => $item) :
                /** @var OrderItemInterface $item */
                $item->save();
            endforeach;
        endforeach;
    }
}