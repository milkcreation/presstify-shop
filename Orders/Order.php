<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop\Orders;

use Exception;
use Illuminate\Support\Collection;
use tiFy\Contracts\PostType\PostTypeStatus;
use tiFy\Plugins\Shop\{Shop, ShopAwareTrait};
use tiFy\Plugins\Shop\Contracts\{Gateway,
    Order as OrderContract,
    OrderItem,
    OrderItemCoupon,
    OrderItemDiscount,
    OrderItemFee,
    OrderItemProduct,
    OrderItemShipping,
    OrderItemTax,
    UserCustomer
};
use tiFy\Support\DateTime;
use tiFy\Support\Proxy\{Database, PostType};
use tiFy\Wordpress\Contracts\Query\QueryPost as QueryPostContract;
use tiFy\Wordpress\Query\QueryPost;
use WP_Post;
use WP_Query;

class Order extends QueryPost implements OrderContract
{
    use ShopAwareTrait;

    /**
     * Nom de qualification du type de post ou liste de types de post associés.
     * @var string|string[]|null
     */
    protected static $postType = 'shop_order';

    /**
     * Cartographie des attributs en correspondance avec les métadonnées enregistrées en base.
     * @var array
     */
    protected $metasMap = [
        'cart_hash'            => '_cart_hash',
        'cart_tax'             => '_order_tax',
        'created_via'          => '_created_via',
        'currency'             => '_order_currency',
        'customer_id'          => '_customer_user',
        'customer_ip_address'  => '_customer_ip_address',
        'customer_user_agent'  => '_customer_user_agent',
        'date_completed'       => '_date_completed',
        'date_paid'            => '_date_paid',
        'discount_tax'         => '_cart_discount_tax',
        'discount_total'       => '_cart_discount',
        'order_key'            => '_order_key',
        'payment_method'       => '_payment_method',
        'payment_method_title' => '_payment_method_title',
        'prices_include_tax'   => '_prices_include_tax',
        'shipping_tax'         => '_order_shipping_tax',
        'shipping_total'       => '_order_shipping',
        'transaction_id'       => '_transaction_id',
        'total'                => '_order_total',
        'version'              => '_order_version',
    ];

    /**
     * Instances d'éléments associés à la commande.
     * @var OrderItem[]|array|null
     */
    protected $orderItems;

    /**
     * Identifiant de qualification de la transaction.
     * @return string|int
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

        $this->parse();
    }

    /**
     * @inheritDoc
     */
    public static function create($id = null, ...$args): ?QueryPostContract
    {
        if (is_numeric($id)) {
            return static::createFromId((int)$id);
        } elseif (is_string($id)) {
            return static::createFromOrderKey($id);
        } elseif ($id instanceof WP_Post) {
            return (new static($id));
        } elseif (is_null($id) && ($instance = static::createFromGlobal())) {
            if (($postType = static::$postType) && ($postType !== 'any')) {
                return $instance->typeIn($postType) ? $instance : null;
            } else {
                return $instance;
            }
        } else {
            return null;
        }
    }

    /**
     * @inheritDoc
     */
    public static function createFromOrderKey(string $orderKey): ?QueryPostContract
    {
        $wpQuery = new WP_Query(static::parseQueryArgs([
            'meta_key'    => '_order_key',
            'meta_value'  => $orderKey,
            'post_status' => 'any',
        ]));

        return ($wpQuery->found_posts == 1) ? new static(current($wpQuery->posts)) : null;
    }

    /**
     * @inheritDoc
     */
    public function addNote(string $note, bool $is_customer = false, bool $by_user = false): int
    {
        if (!$this->getId()) {
            return 0;
        }

        if (($user = $this->shop()->user()) && $user->can('edit_shop_order', $this->getId()) && $by_user) {
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
    public function addOrderItem(OrderItem $orderItem): OrderContract
    {
        $type = $orderItem->getType();
        $group = "new:{$type}";

        if (!isset($this->orderItems[$group])) {
            $this->orderItems[$group] = [];
        }

        $this->orderItems[$group][] = $orderItem;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function createItem(array $args = []): OrderItem
    {
        /** @var OrderItemCoupon $instance */
        $instance = $this->shop()->resolve('order.item.common');

        return $instance->setOrder($this)->set($args)->parse();
    }

    /**
     * @inheritDoc
     */
    public function createItemCoupon(array $args = []): OrderItemCoupon
    {
        /** @var OrderItemCoupon $instance */
        $instance = $this->shop()->resolve('order.item.coupon');

        return $instance->setOrder($this)->set(array_merge($args, ['type' => 'coupon']))->parse();
    }

    /**
     * @inheritDoc
     */
    public function createItemDiscount(array $args = []): OrderItemDiscount
    {
        /** @var OrderItemDiscount $instance */
        $instance = $this->shop()->resolve('order.item.discount');

        return $instance->setOrder($this)->set(array_merge($args, ['type' => 'discount']))->parse();
    }

    /**
     * @inheritDoc
     */
    public function createItemFee(array $args = []): OrderItemFee
    {
        /** @var OrderItemFee $instance */
        $instance = $this->shop()->resolve('order.item.fee');

        return $instance->setOrder($this)->set(array_merge($args, ['type' => 'fee']))->parse();
    }

    /**
     * @inheritDoc
     */
    public function createItemProduct(array $args = []): OrderItemProduct
    {
        /** @var OrderItemProduct $instance */
        $instance = $this->shop()->resolve('order.item.product');

        return $instance->setOrder($this)->set(array_merge($args, ['type' => 'line_item']))->parse();
    }

    /**
     * @inheritdoc
     */
    public function createItemShipping(array $args = []): OrderItemShipping
    {
        /** @var OrderItemShipping $instance */
        $instance = $this->shop()->resolve('order.item.shipping');

        return $instance->setOrder($this)->set(array_merge($args, ['type' => 'shipping']))->parse();
    }

    /**
     * @inheritDoc
     */
    public function createItemTax(array $args = []): OrderItemTax
    {
        /** @var OrderItemTax $instance */
        $instance = $this->shop()->resolve('order.item.tax');

        return $instance->setOrder($this)->set(array_merge($args, ['type' => 'tax']))->parse();
    }

    /**
     * @inheritDoc
     */
    public function defaults()
    {
        return [
            'billing'              => [
                'first_name' => '',
                'last_name'  => '',
                'company'    => '',
                'address1'   => '',
                'address2'   => '',
                'city'       => '',
                'state'      => '',
                'postcode'   => '',
                'country'    => '',
                'email'      => '',
                'phone'      => '',
            ],
            'cart_hash'            => '',
            'cart_tax'             => 0,
            'currency'             => '',
            'customer_id'          => 0,
            'customer_ip_address'  => '',
            'customer_user_agent'  => '',
            'created_via'          => '',
            'customer_note'        => '',
            'date_completed'       => null,
            'date_created'         => null,
            'date_modified'        => null,
            'date_paid'            => null,
            'discount_tax'         => 0,
            'discount_total'       => 0,
            'order_key'            => '',
            'parent_id'            => 0,
            'payment_method'       => '',
            'payment_method_title' => '',
            'prices_include_tax'   => false,
            'status'               => '',
            'shipping'             => [
                'first_name' => '',
                'last_name'  => '',
                'company'    => '',
                'address1'   => '',
                'address2'   => '',
                'city'       => '',
                'state'      => '',
                'postcode'   => '',
                'country'    => '',
            ],
            'shipping_total'       => 0,
            'shipping_tax'         => 0,
            'transaction_id'       => '',
            'total'                => 0,
            'total_tax'            => 0,
            'version'              => '',
        ];
    }

    /**
     * @inheritDoc
     */
    public function getBilling(?string $key = null, $default = null): string
    {
        return is_null($key) ? $this->get('billing', []) : $this->get("billing.{$key}", $default);
    }

    /**
     * @inheritDoc
     */
    public function getCheckoutOrderReceivedUrl(): string
    {
        return $this->shop()->functions()->page()->orderReceivedPageUrl([
            'order-received' => $this->getId(),
            'key'            => $this->getOrderKey(),
        ]);
    }

    /**
     * @inheritDoc
     */
    public function getCheckoutPaymentUrl(): string
    {
        return $this->shop()->functions()->page()->orderPayPageUrl([
            'order-pay' => $this->getId(),
            'key'       => $this->getOrderKey(),
        ]);
    }

    /**
     * @inheritDoc
     */
    public function getCustomer(): UserCustomer
    {
        return $this->shop()->user($this->getCustomerId());
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
    public function getDiscountTotal(): float
    {
        return (float)$this->get('discount_total', 0);
    }

    /**
     * Récupération des l'instance d'éléments associés à la commande.
     *
     * @param string|null $type Type d'éléments à retourner. coupon|fee|line_item|shipping|tax.
     *
     * @return OrderItem[]|array
     */
    public function getOrderItems(?string $type = null): array
    {
        if (is_null($this->orderItems)) {
            $this->orderItems = [];

            $queryItems = Database::table('tify_shop_order_items')->where('order_id', $this->getId())->get();

            if ($queryItems->count()) {
                foreach ($queryItems as $queryItem) {
                    $args = get_object_vars($queryItem);

                    /** @var OrderItem $orderItem */
                    switch ($item_type = $args['order_item_type']) {
                        default :
                            $method = 'createItem' . ucfirst($item_type);
                            $orderItem = method_exists($this, $method)
                                ? $this->{$method}($args) : $this->createItem($args);
                            break;
                        case 'line_item' :
                            $orderItem = $this->createItemProduct($args);
                            break;
                    }

                    if (!isset($this->orderItems[$orderItem->getType()])) {
                        $this->orderItems[$orderItem->getType()] = [];
                    }

                    $this->orderItems[$orderItem->getType()][] = $orderItem;
                }
            }
        }

        if (is_null($type)) {
            return $this->orderItems ?: [];
        } elseif ($this->orderItems) {
            $orderItems = [];
            foreach ($this->orderItems as $items) {
                foreach ($items as $item) {
                    $orderItems[] = $item;
                }
            }

            return (new Collection($orderItems))->filter(function (OrderItem $item) use ($type) {
                return $item->getType() === $type;
            })->all();
        } else {
            return [];
        }
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
    public function getShipping(?string $key = null, $default = null): string
    {
        return is_null($key) ? $this->get('shipping', []) : $this->get("shipping.{$key}", $default);
    }

    /**
     * @inheritDoc
     */
    public function getShippingTotal(): float
    {
        return (float)$this->get('shipping_total', 0);
    }

    /**
     * @inheritDoc
     */
    public function getSubtotal(): float
    {
        return (float)($this->getDiscountTotal() + $this->getTotal());
    }

    /**
     * @inheritDoc
     */
    public function getShortStatus(): string
    {
        $regex = '/^order\-/';

        return (string)preg_replace($regex, '', $this->get('status', $this->shop()->orders()->getDefaultStatus()));
    }

    /**
     * @inheritDoc
     */
    public function getStatus(): PostTypeStatus
    {
        return PostType::status($this->get('status', $this->shop()->orders()->getDefaultStatus()));
    }

    /**
     * @inheritDoc
     */
    public function getStatusLabel(): string
    {
        return $this->getStatus()->getLabel();
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
        return in_array($this->getStatus()->getName(), (array)$status);
    }

    /**
     * @inheritDoc
     */
    public function isCustomer(int $id): bool
    {
        return $this->getCustomerId() === $id;
    }

    /**
     * @inheritDoc
     */
    public function mapMeta($key, ?string $metaKey = null): OrderContract
    {
        $keys = is_array($key) ? $key : [$key => $metaKey];

        foreach ($keys as $key => $metaKey) {
            if ($value = $this->getMetaSingle($metaKey)) {
                $this->set($key, $value);
            }
        }

        return $this;
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
        if ($lineItems = $this->getOrderItems('line_item')) {
            return (new Collection($lineItems))->filter(function (OrderItemProduct $lineItem) {
                    return ($product = $this->shop()->product($lineItem->getProductId()))
                        ? $product->isDownloadable() && $product->isVirtual()
                        : false;
                })->count() === 0;
        } else {
            return false;
        }
    }

    /**
     * @inheritDoc
     */
    public function parse(): OrderContract
    {
        parent::parse();

        if (!$id = $this->getId()) {
            return $this;
        }

        $this->mapMeta($this->metasMap);

        foreach (['billing', 'shipping'] as $type) {
            if ($datas = $this->get($type, []) ?: []) {
                foreach (array_keys($datas) as $key) {
                    $this->mapMeta("{$type}.{$key}", "_{$type}_{$key}");
                }
            }
        }

        $this->set([
            'customer_note' => $this->getExcerpt(true),
            'date_created'  => $this->getDate(true),
            'date_modified' => $this->getModified(true),
            'order_key'     => $this->get('order_key') ?: uniqid('order_'),
            'parent_id'     => $this->getParentId(),
            'status'        => $this->shop()->orders()->hasStatus($this->get('post_status'))
                ? $this->get('post_status') : $this->shop()->orders()->getDefaultStatus(),
        ]);

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

            $this->shop()->session()->forget('order_awaiting_payment');

            if ($this->hasStatus($this->shop()->orders()->getPaymentValidStatuses())) {
                $this->set('transaction_id', $transaction_id);

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
        return (int)count($this->getOrderItems('line_item'));
    }

    /**
     * @inheritDoc
     */
    public function quantityProductCount(): int
    {
        return (int)(new Collection($this->getOrderItems('line_item')))->sum('quantity');
    }

    /**
     * @inheritDoc
     */
    public function removeOrderItems(?string $type = null): void
    {
        if (!is_null($type)) {
            if ($items = $this->getOrderItems($type)) {
                $ids = (new Collection($items))->pluck('id')->unique()->all();

                foreach ($ids as $id) {
                    $this->shop()->entity()->orderItemsTable()->where([
                        'order_item_id'   => $id,
                        'order_item_type' => $type,
                        'order_id'        => $this->getId(),
                    ])->delete();

                    $this->shop()->entity()->orderItemMetaTable()->where([
                        'order_item_id' => $id,
                    ])->delete();
                }
                unset($this->orderItems[$type]);
            }
        } elseif ($types = array_keys($this->getOrderItems())) {
            foreach ($types as $type) {
                $this->removeOrderItems($type);
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function saveItems(): void
    {
        foreach ($this->getOrderItems() as $group => $grouped) {
            foreach ($grouped as $key => $item) {
                /** @var OrderItem $item */
                if ($id = $item->save()) {
                    $item->saveMetas();
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
        } else {
            foreach ($this->metasMap as $attr_key => $meta_key) {
                $meta_value = $this->get($attr_key, '');

                switch ($attr_key) {
                    case 'date_paid' :
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
    }

    /**
     * @inheritDoc
     */
    public function setBilling(string $key, $value): OrderContract
    {
        return $this->set("billing.{$key}", $value);
    }

    /**
     * @inheritDoc
     */
    public function setShipping(string $key, $value): OrderContract
    {
        return $this->set("shipping.{$key}", $value);
    }

    /**
     * @inheritDoc
     */
    public function update(): void
    {
        $post_data = [
            'ID'                => $this->getId(),
            'post_status'       => $this->getStatus()->getName(),
            'post_parent'       => $this->getParentId(),
            'post_excerpt'      => $this->getExcerpt(true),
            'post_modified'     => $this->shop()->functions()->date()->local(),
            'post_modified_gmt' => $this->shop()->functions()->date()->utc(),
        ];

        wp_update_post($post_data);

        $this->saveMetas();

        $this->saveItems();
    }

    /**
     * @inheritDoc
     */
    public function updateStatus(string $new_status): bool
    {
        if (!$this->shop()->orders()->hasStatus($new_status) || ($this->get('status') === $new_status)) {
            return false;
        } else {
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
}