<?php

namespace tiFy\Plugins\Shop\Orders;

use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use tiFy\PostType\Query\PostQueryItem;
use tiFy\Plugins\Shop\Contracts\GatewayInterface;
use tiFy\Plugins\Shop\Contracts\OrderInterface;
use tiFy\Plugins\Shop\Contracts\OrderItemTypeInterface;
use tiFy\Plugins\Shop\Contracts\OrderItemTypeProductInterface;
use tiFy\Plugins\Shop\Orders\OrderItems\OrderItems;
use tiFy\Plugins\Shop\Shop;
use tiFy\Plugins\Shop\ShopResolverTrait;
use WP_Post;

/**
 * Class Order
 *
 * @desc Controleur de commande.
 */
class Order extends PostQueryItem implements OrderInterface
{
    use ShopResolverTrait;

    /**
     * Liste des données de commande par défaut.
     *
     * @var array
     */
    protected $defaults = [
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
     * Liste des éléments associés à la commande.
     * @var array
     */
    protected $items = [];

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
     * Classe de rappel de traitement de la liste des éléments associés à la commande.
     * @var OrderItems
     */
    protected $order_items;

    /**
     * Identifiant de qualification de la transaction.
     * @return string
     */
    protected $transaction_id = '';

    /**
     * CONSTRUCTEUR.
     *
     * @param WP_Post $post Objet post Wordpress.
     * @param Shop $shop Instance de la boutique.
     *
     * @return void
     */
    public function __construct(WP_Post $post, Shop $shop)
    {
        $this->shop = $shop;

        parent::__construct($post);

        // Définition de la classe de rappel de traitement de la liste des éléments associés à la commande
        $this->order_items = new OrderItems($this, $shop);

        $this->read();
    }

    /**
     * @inheritdoc
     */
    public function addNote($note, $is_customer = false, $by_user = false)
    {
        if ( ! $this->getId()) {
            return 0;
        }

        if (($user = $this->users()->getItem()) && $user->can('edit_shop_order', $this->getId()) && $by_user) {
            $comment_author = $user->getDisplayName();
            $comment_author_email = $user->getEmail();
        } else {
            $comment_author = __('tiFyShop', 'tify');
            $comment_author_email = strtolower(__('tiFyShop', 'tify')) . '@';
            $comment_author_email .= 'noreply.com';
            $comment_author_email = sanitize_email($comment_author_email);
        }

        $commentdata = [
            'comment_post_ID'      => $this->getId(),
            'comment_author'       => $comment_author,
            'comment_author_email' => $comment_author_email,
            'comment_author_url'   => '',
            'comment_content'      => $note,
            'comment_agent'        => 'tiFyShop',
            'comment_type'         => 'order_note',
            'comment_parent'       => 0,
            'comment_approved'     => 1,
        ];

        $comment_id = wp_insert_comment($commentdata);

        if ($is_customer) {
            add_comment_meta($comment_id, 'is_customer_note', 1);
        }
        return $comment_id;
    }

    /**
     * @inheritdoc
     */
    public function addItem($item)
    {
        $type = $item->getType();

        $count       = isset($this->items[$type]) ? count($this->items[$type]) : 0;
        $this->items = Arr::add($this->items, $type . '.new:' . $type . $count, $item);
    }

    /**
     * @inheritdoc
     */
    public function create()
    {
        $this->set('order_key', uniqid('order_'));
    }

    /**
     * @inheritdoc
     */
    public function createItemCoupon()
    {
        return app('shop.orders.item_coupon', [0, $this, $this->shop]);
    }

    /**
     * @inheritdoc
     */
    public function createItemFee()
    {
        return app('shop.orders.item_fee', [0, $this, $this->shop]);
    }

    /**
     * @inheritdoc
     */
    public function createItemProduct()
    {
        return app('shop.orders.order_item_type_product', [0, $this, $this->shop]);
    }

    /**
     * @inheritdoc
     */
    public function createItemShipping()
    {
        return app('shop.orders.item_shipping', [0, $this, $this->shop]);
    }

    /**
     * @inheritdoc
     */
    public function createItemTax()
    {
        return app('shop.orders.item_tax', [0, $this, $this->shop]);
    }

    /**
     * @inheritdoc
     */
    public function getAddressAttr($key, $type = 'billing', $default = '')
    {
        return $this->get("{$type}.{$key}", $default);
    }

    /**
     * @inheritdoc
     */
    public function getCheckoutOrderReceivedUrl()
    {
        return $this->functions()->url()->checkoutOrderReceivedPage([
            'order-received' => $this->getId(),
            'key'            => $this->getOrderKey()
        ]);
    }

    /**
     * @inheritdoc
     */
    public function getCheckoutPaymentUrl()
    {
        return $this->functions()->url()->checkoutOrderPayPage([
            'order-pay' => $this->getId(),
            'key'       => $this->getOrderKey()
        ]);
    }

    /**
     * @inheritdoc
     */
    public function getCustomer()
    {
        return $this->users()->getItem($this->getCustomerId());
    }

    /**
     * @inheritdoc
     */
    public function getCustomerId()
    {
        return (int)$this->get('customer_id', 0);
    }

    /**
     * @inheritdoc
     */
    public function getItems($type = null)
    {
        $items = $type ? Arr::get($this->items, $type) : $this->items;

        return new Collection($items);
    }

    /**
     * @inheritdoc
     */
    public function getOrderKey()
    {
        return (string)$this->get('order_key', '');
    }

    /**
     * @inheritdoc
     */
    public function getPaymentMethod()
    {
        return (string)$this->get('payment_method', '');
    }

    /**
     * Récupération du l'intitulé de qualification de la méthode de paiement.
     *
     * @return string
     */
    public function getPaymentMethodLabel()
    {
        /** @var GatewayInterface $gateway */
        return ($gateway = $this->gateways()->get($this->get('payment_method', '')))
            ? $gateway->getTitle()
            : '';
    }

    /**
     * @inheritdoc
     */
    public function getShortStatus()
    {
        return (string)preg_replace('/^order\-/', '', $this->get('status', $this->orders()->getDefaultStatus()));
    }

    /**
     * @inheritdoc
     */
    public function getStatus()
    {
        return (string)$this->get('status', $this->orders()->getDefaultStatus());
    }

    /**
     * @inheritdoc
     */
    public function getStatusLabel()
    {
        return $this->orders()->getStatusLabel($this->getStatus());
    }

    /**
     * @inheritdoc
     */
    public function getTransactionId()
    {
        return $this->transaction_id;
    }

    /**
     * @inheritdoc
     */
    public function getTotal()
    {
        return (float)$this->get('total', 0);
    }

    /**
     * @inheritdoc
     */
    public function hasStatus($status)
    {
        return in_array($this->getStatus(), (array)$status);
    }

    /**
     * @inheritdoc
     */
    public function isCustomer($customer_id)
    {
        return (bool)($this->getCustomerId() === (int)$customer_id);
    }

    /**
     * @inheritdoc
     */
    public function needPayment()
    {
        return $this->hasStatus($this->orders()->getNeedPaymentStatuses()) && ($this->getTotal() > 0);
    }

    /**
     * @inheritdoc
     */
    public function needProcessing()
    {
        if ( ! $line_items = $this->getItems('line_item')) {
            return false;
        }

        $virtual_and_downloadable = $line_items->filter(function (OrderItemTypeProductInterface $line_item) {
            return ($product = $this->products()->getItem($line_item->getProductId()))
                ? $product->isDownloadable() && $product->isVirtual()
                : false;
        })->all();

        return count($virtual_and_downloadable) === 0;
    }

    /**
     * @inheritdoc
     */
    public function paymentComplete($transaction_id = '')
    {
        try {
            if ( ! $this->getId()) {
                return false;
            }
            $this->session()->pull('order_awaiting_payment', false);

            if ($this->hasStatus($this->orders()->getPaymentValidStatuses())) {
                if (!empty($transaction_id)) {
                    $this->transaction_id = $transaction_id;
                }
                if (!$this->get('date_paid')) {
                    $this->set('date_paid', $this->functions()->date()->utc('U'));
                }
                $this->set('status', $this->needProcessing() ? 'order-processing' : 'order-completed');

                $this->save();
            }
            events()->trigger('shop.order.payment.completed', [$this]);
        } catch (Exception $e) {
            return false;
        }
        return true;
    }

    /**
     * @inheritdoc
     */
    public function productCount()
    {
        return (int)count($this->getItems('line_item'));
    }

    /**
     * @inheritdoc
     */
    public function quantityProductCount()
    {
        return (int)(new Collection($this->getItems('line_item')))->sum('quantity');
    }

    /**
     * @inheritdoc
     */
    public function read()
    {
        $this->attributes = array_merge($this->defaults, $this->attributes);

        if ( ! $id = $this->getId()) {
            return;
        }

        foreach ($this->metas_map as $attr_key => $meta_key) {
            $this->set(
                $attr_key,
                get_post_meta($id, $meta_key, true) ?: $this->get($attr_key, Arr::get($this->defaults, $attr_key))
            );
        }

        foreach (['billing', 'shipping'] as $address_type) {
            if (!$address_data = $this->get($address_type, [])) {
                continue;
            }
            foreach ($address_data as $key => $value) {
                $this->set("{$address_type}.{$key}", get_post_meta($id, "_{$address_type}_{$key}", true));
            }
        }

        $this->set('parent_id', $this->getParentId());
        $this->set('date_created', $this->getDate(true));
        $this->set('date_modified', $this->getModified(true));
        $this->set('status',
            $this->orders()->isStatus($this->post_status) ? $this->post_status : $this->orders()->getDefaultStatus());
        $this->set('customer_note', $this->getExcerpt(true));

        // Récupération de la liste des éléments associé à la commande, enregistré en base de donnée.
        foreach ($this->order_items->getCollection() as $item) {
            /** @var OrderItemTypeInterface $item */
            $this->items[$item->getType()][$item->getId()] = $item;
        }
    }

    /**
     * @inheritdoc
     */
    public function save()
    {
        // Mise à jour des données de post
        $post_data = [
            'ID'                => $this->getId(),
            'post_date'         => $this->functions()->date()->get(),
            'post_date_gmt'     => $this->functions()->date()->utc(),
            'post_status'       => $this->getStatus(),
            'post_parent'       => $this->getParentId(),
            'post_excerpt'      => $this->getExcerpt(true),
            'post_modified'     => $this->functions()->date()->get(),
            'post_modified_gmt' => $this->functions()->date()->utc(),
        ];
        wp_update_post($post_data);

        // Sauvegarde des métadonnées
        $this->saveMetas();

        // Sauvegarde des éléments
        $this->saveItems();
    }

    /**
     * @inheritdoc
     */
    public function saveItems()
    {
        if (! $this->items) {
            return;
        }
        foreach ($this->items as $group => $group_items) {
            foreach ($group_items as $item_key => $item) {
                /** @var OrderItemTypeInterface $item */
                $item->save();
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function saveMetas()
    {
        if (! $this->metas_map || ! $this->getId()) {
            return;
        }
        foreach ($this->metas_map as $attr_key => $meta_key) {
            $meta_value = $this->get($attr_key, '');

            switch ($attr_key) {
                case 'date_paid' :
                    update_post_meta($this->getId(), $meta_key, !is_null($meta_value) ? $meta_value : '');
                    break;
                case 'date_completed' :
                    update_post_meta($this->getId(), $meta_key, !is_null($meta_value) ? $meta_value : '');
                    break;
                default :
                    update_post_meta($this->getId(), $meta_key, $meta_value);
                    break;
            }
        }

        foreach (['billing', 'shipping'] as $address_type) {
            if (!$address_data = $this->get($address_type, [])) {
                continue;
            }
            foreach ($address_data as $key => $value) {
                update_post_meta($this->getId(), "_{$address_type}_{$key}", $value);
            }
            update_post_meta($this->getId(), "_{$address_type}_address_index", implode(' ', $address_data));
        }
    }

    /**
     * @inheritdoc
     */
    public function setBillingAttr($key, $value)
    {
        return $this->set('billing', array_merge($this->get('billing', []), [$key => $value]));
    }

    /**
     * @inheritdoc
     */
    public function setShippingAttr($key, $value)
    {
        return $this->set('shipping', array_merge($this->get('shipping', []), [$key => $value]));
    }

    /**
     * @inheritdoc
     */
    public function updateStatus($new_status)
    {
        if ( ! $this->orders()->isStatus($new_status) || ($this->get('status') === $new_status)) {
            return false;
        }

        $this->set('status', $new_status);

        if ( ! $this->get('date_paid') && $this->hasStatus($this->orders()->getPaymentCompleteStatuses())) {
            $this->set('date_paid', $this->functions()->date()->utc('U'));
        }

        if ( ! $this->get('date_completed') && $this->hasStatus('completed')) {
            $this->set('date_completed', $this->functions()->date()->utc('U'));
        }

        $this->save();

        return true;
    }
}