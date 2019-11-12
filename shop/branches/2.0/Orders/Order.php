<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop\Orders;

use Exception;
use Illuminate\Support\Collection;
use tiFy\Wordpress\Query\QueryPost;
use tiFy\Plugins\Shop\Contracts\{Gateway,
    Order as OrderContract,
    OrderItemType,
    OrderItemTypeCoupon,
    OrderItemTypeFee,
    OrderItemTypeProduct,
    OrderItems,
    OrderItemTypeShipping,
    OrderItemTypeTax,
    UserCustomer};
use tiFy\Plugins\Shop\{Shop, ShopAwareTrait};
use tiFy\Support\{Arr, DateTime};
use WP_Post;

class Order extends QueryPost implements OrderContract
{
    use ShopAwareTrait;

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
    protected $metasMap = [
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
     * Instance du gestionnaires d'éléments associés à la commande.
     * @var OrderItems
     */
    protected $orderItems;

    /**
     * Identifiant de qualification de la transaction.
     * @return string
     */
    protected $transactionId = '';

    /**
     * CONSTRUCTEUR.
     *
     * @param WP_Post $post Objet post Wordpress.
     *
     * @return void
     */
    public function __construct(?WP_Post $post)
    {
        $this->setShop(Shop::instance());

        parent::__construct($post);

        $this->read();
    }

    /**
     * @inheritDoc
     */
    public function addNote(string $note, bool $is_customer = false, bool $by_user = false): int
    {
        if (!$this->getId()) {
            return 0;
        }

        if (($user = $this->shop()->users()->get()) && $user->can('edit_shop_order', $this->getId()) && $by_user) {
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
     * @inheritDoc
     */
    public function addItem($item): void
    {
        $type = $item->getType();

        $count = isset($this->items[$type]) ? count($this->items[$type]) : 0;
        $this->items = Arr::add($this->items, $type . '.new:' . $type . $count, $item);
    }

    /**
     * @inheritDoc
     */
    public function createItemCoupon(): ?OrderItemTypeCoupon
    {
        return app('shop.order.item.coupon', [0, $this, $this->shop]);
    }

    /**
     * @inheritDoc
     */
    public function createItemFee(): ?OrderItemTypeFee
    {
        return app('shop.order.item.fee', [0, $this, $this->shop]);
    }

    /**
     * @inheritDoc
     */
    public function createItemProduct(): ?OrderItemTypeProduct
    {
        return app('shop.order.item.product', [0, $this, $this->shop]);
    }

    /**
     * @inheritdoc
     */
    public function createItemShipping(): ?OrderItemTypeShipping
    {
        return app('shop.orders.item_shipping', [0, $this, $this->shop]);
    }

    /**
     * @inheritDoc
     */
    public function createItemTax(): ?OrderItemTypeTax
    {
        return app('shop.orders.item_tax', [0, $this, $this->shop]);
    }

    /**
     * @inheritDoc
     */
    public function getAddressAttr($key, $type = 'billing', $default = '')
    {
        return $this->get("{$type}.{$key}", $default);
    }

    /**
     * @inheritDoc
     */
    public function getCheckoutOrderReceivedUrl(): string
    {
        return $this->shop()->functions()->url()->checkoutOrderReceivedPage([
            'order-received' => $this->getId(),
            'key'            => $this->getOrderKey()
        ]);
    }

    /**
     * @inheritDoc
     */
    public function getCheckoutPaymentUrl(): string
    {
        return $this->shop()->functions()->url()->checkoutOrderPayPage([
            'order-pay' => $this->getId(),
            'key'       => $this->getOrderKey()
        ]);
    }

    /**
     * @inheritDoc
     */
    public function getCustomer(): UserCustomer
    {
        return $this->shop()->users()->get($this->getCustomerId());
    }

    /**
     * @inheritDoc
     */
    public function getCustomerId(): int
    {
        return (int)$this->get('customer_id', 0);
    }

    /**
     * @inheritDoc
     */
    public function getItems($type = null)
    {
        $items = $type ? Arr::get($this->items, $type) : $this->items;

        return new Collection($items);
    }

    /**
     * @inheritDoc
     */
    public function getOrderKey(): string
    {
        return (string)$this->get('order_key', '');
    }

    /**
     * @inheritDoc
     */
    public function getPaidDatetime(): ?DateTime
    {
        return ($date = $this->get('date_paid', 0)) ? DateTime::createFromTimestamp($date) : null;
    }

    /**
     * @inheritDoc
     */
    public function getPaymentMethod(): string
    {
        return (string)$this->get('payment_method', '');
    }

    /**
     * Récupération du l'intitulé de qualification de la méthode de paiement.
     *
     * @return string
     */
    public function getPaymentMethodLabel(): string
    {
        /** @var Gateway $gateway */
        return ($gateway = $this->shop()->gateways()->get($this->get('payment_method', '')))
            ? $gateway->getTitle()
            : '';
    }

    /**
     * @inheritDoc
     */
    public function getShortStatus(): string
    {
        return (string)preg_replace(
            '/^order\-/', '',
            $this->get('status', $this->shop()->orders()->getDefaultStatus())
        );
    }

    /**
     * @inheritDoc
     */
    public function getStatus()
    {
        return (string)$this->get('status', $this->shop()->orders()->getDefaultStatus());
    }

    /**
     * @inheritDoc
     */
    public function getStatusLabel()
    {
        return $this->shop()->orders()->getStatusLabel($this->getStatus());
    }

    /**
     * @inheritDoc
     */
    public function getTransactionId()
    {
        return $this->transactionId;
    }

    /**
     * @inheritDoc
     */
    public function getTotal(): float
    {
        return (float)$this->get('total', 0);
    }

    /**
     * @inheritDoc
     */
    public function hasStatus($status): bool
    {
        return in_array($this->getStatus(), (array)$status);
    }

    /**
     * @inheritDoc
     */
    public function isCustomer($customer_id): bool
    {
        return (bool)($this->getCustomerId() === (int)$customer_id);
    }

    /**
     * @inheritDoc
     */
    public function needPayment(): bool
    {
        return $this->hasStatus($this->shop()->orders()->getNeedPaymentStatuses()) && ($this->getTotal() > 0);
    }

    /**
     * @inheritDoc
     */
    public function needProcessing(): bool
    {
        if (!$line_items = $this->getItems('line_item')) {
            return false;
        }

        $virtual_and_downloadable = $line_items->filter(function (OrderItemTypeProduct $line_item) {
            return ($product = $this->shop()->products()->get($line_item->getProductId()))
                ? $product->isDownloadable() && $product->isVirtual()
                : false;
        })->all();

        return count($virtual_and_downloadable) === 0;
    }

    /**
     * Récupération de l'instance du gestionnaire d'éléments associé à la commande.
     *
     * @return OrderItems
     */
    public function orderItems(): OrderItems
    {
        if (is_null($this->orderItems)) {
            $this->orderItems = $this->shop()->resolve('order.items', [$this]);
        }

        return $this->orderItems;
    }

    /**
     * @inheritDoc
     */
    public function parse()
    {
        parent::parse();

        $this->set('order_key', $this->get('order_key', uniqid('order_')));

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function paymentComplete($transaction_id = ''): bool
    {
        try {
            if (!$this->getId()) {
                return false;
            }
            $this->shop()->session()->pull('order_awaiting_payment', false);

            if ($this->hasStatus($this->shop()->orders()->getPaymentValidStatuses())) {
                if (!empty($transaction_id)) {
                    $this->transactionId = $transaction_id;
                }
                if (!$this->get('date_paid')) {
                    $this->set('date_paid', $this->shop()->functions()->date()->utc('U'));
                }
                $this->set('status', $this->needProcessing() ? 'order-processing' : 'order-completed');

                $this->update();
            }
            events()->trigger('shop.order.payment.completed', [$this]);
        } catch (Exception $e) {
            return false;
        }
        return true;
    }

    /**
     * @inheritDoc
     */
    public function productCount(): int
    {
        return (int)count($this->getItems('line_item'));
    }

    /**
     * @inheritDoc
     */
    public function quantityProductCount(): int
    {
        return (int)(new Collection($this->getItems('line_item')))->sum('quantity');
    }

    /**
     * @inheritDoc
     */
    public function read(): void
    {
        $this->attributes = array_merge($this->defaults, $this->attributes);

        if (!$id = $this->getId()) {
            return;
        }

        foreach ($this->metasMap as $attr_key => $meta_key) {
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
        $this->set('status', $this->shop()->orders()->isStatus($this->post_status)
            ? $this->post_status : $this->shop()->orders()->getDefaultStatus()
        );
        $this->set('customer_note', $this->getExcerpt(true));

        foreach ($this->orderItems()->query() as $item) {
            /** @var OrderItemType $item */
            $this->items[$item->getType()][$item->getId()] = $item;
        }
    }

    /**
     * @inheritDoc
     */
    public function removeItems(?string $type = null): void
    {
        if (!empty($type)) {
            $this->order_items->delete($type);
            unset($this->items[$type]);
        } else {
            $this->order_items->delete();
            $this->items = [];
        }
    }

    /**
     * @inheritDoc
     */
    public function update()
    {
        $post_data = [
            'ID'                => $this->getId(),
            'post_date'         => $this->shop()->functions()->date()->get(),
            'post_date_gmt'     => $this->shop()->functions()->date()->utc(),
            'post_status'       => $this->getStatus(),
            'post_parent'       => $this->getParentId(),
            'post_excerpt'      => $this->getExcerpt(true),
            'post_modified'     => $this->shop()->functions()->date()->get(),
            'post_modified_gmt' => $this->shop()->functions()->date()->utc(),
        ];
        wp_update_post($post_data);

        $this->saveMetas();

        $this->saveItems();
    }

    /**
     * @inheritDoc
     */
    public function saveItems(): void
    {
        if ($this->items) {
            foreach ($this->items as $group => $group_items) {
                foreach ($group_items as $item_key => $item) {
                    /** @var OrderItemType $item */
                    $item->save();
                }
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function saveMetas(): void
    {
        if (!$this->metasMap || !$this->getId()) {
            return;
        }

        foreach ($this->metasMap as $attr_key => $meta_key) {
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
     * @inheritDoc
     */
    public function setBillingAttr($key, $value)
    {
        return $this->set('billing', array_merge($this->get('billing', []), [$key => $value]));
    }

    /**
     * @inheritDoc
     */
    public function setShippingAttr($key, $value)
    {
        return $this->set('shipping', array_merge($this->get('shipping', []), [$key => $value]));
    }

    /**
     * @inheritDoc
     */
    public function updateStatus($new_status): bool
    {
        if (!$this->shop()->orders()->isStatus($new_status) || ($this->get('status') === $new_status)) {
            return false;
        }

        $this->set('status', $new_status);

        if (!$this->get('date_paid') && $this->hasStatus($this->shop()->orders()->getPaymentCompleteStatuses())) {
            $this->set('date_paid', $this->shop()->functions()->date()->utc('U'));
        }

        if (!$this->get('date_completed') && $this->hasStatus('completed')) {
            $this->set('date_completed', $this->shop()->functions()->date()->utc('U'));
        }

        $this->update();

        return true;
    }
}