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

use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use tiFy\Core\Query\Controller\AbstractPostItem;
use tiFy\Plugins\Shop\Orders\OrderItem\OrderItemInterface;
use tiFy\Plugins\Shop\Products\ProductItemInterface;
use tiFy\Plugins\Shop\Orders\OrderItem\OrderItemCouponInterface;
use tiFy\Plugins\Shop\Orders\OrderItem\OrderItemFeeInterface;
use tiFy\Plugins\Shop\Orders\OrderItem\OrderItemProductInterface;
use tiFy\Plugins\Shop\Orders\OrderItem\OrderItemShippingInterface;
use tiFy\Plugins\Shop\Orders\OrderItem\OrderItemTaxInterface;
use tiFy\Plugins\Shop\ServiceProvider\ProvideTraits;
use tiFy\Plugins\Shop\ServiceProvider\ProvideTraitsInterface;
use tiFy\Plugins\Shop\Shop;
use \WP_Post;

class Order extends AbstractPostItem implements OrderInterface, ProvideTraitsInterface
{
    use ProvideTraits;

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
     * CONSTRUCTEUR.
     *
     * @param Shop $shop Classe de rappel de la boutique.
     * @param WP_Post $post Objet post Wordpress.
     *
     * @return void
     */
    public function __construct(Shop $shop, WP_Post $post)
    {
        // Définition de la classe de rappel de la boutique
        $this->shop = $shop;

        parent::__construct($post);

        $this->read();
    }

    /**
     * Récupération de la liste des attributs.
     *
     * @return void
     */
    public function read()
    {
        $this->attributes = array_merge($this->defaults, $this->attributes);

        if (! $id = $this->getId()) :
            return;
        endif;

        foreach($this->metas_map as $attr_key => $meta_key) :
            $this->set($attr_key, \get_post_meta($id, $meta_key, true) ? : $this->get($attr_key, Arr::get($this->defaults, $attr_key)));
        endforeach;

        foreach(['billing', 'shipping'] as $address_type) :
            if (!$address_data = $this->get($address_type, [])) :
                continue;
            endif;
            foreach($address_data as $key => $value) :
                $this->set("{$address_type}.{$key}", \get_post_meta($id, "_{$address_type}_{$key}", true));
            endforeach;
        endforeach;

        $this->set('parent_id', $this->getParentId());
        $this->set('date_created', $this->getDate(true));
        $this->set('date_modified', $this->getModified(true));
        $this->set('status', $this->orders()->isStatus($this->post_status) ? $this->post_status : $this->orders()->getDefaultStatus());
        $this->set('customer_note', $this->getExcerpt(true));
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
     * @return $this
     */
    public function set($key, $value)
    {
        Arr::set($this->attributes, $key, $value);

        return $this;
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
        return (string)$this->get('status', $this->orders()->getDefaultStatus());
    }

    /**
     * Récupération de la clé d'identification de la commande.
     *
     * @return string
     */
    public function getOrderKey()
    {
        return (string)$this->get('order_key', '');
    }

    /**
     * Récupération de l'identifiant de qualification du client associé à la commande.
     *
     * @return int
     */
    public function getCustomerId()
    {
        return (int)$this->get('customer_id', 0);
    }

    /**
     * Récupération d'un attribut pour un type d'adresse.
     *
     * @param string $key Clé d'identification de l'attribut à retourner.
     * @param string $type Type d'adresse. billing|shipping.
     * @param mixed $default Valeur de retour par défaut.
     *
     * @return mixed
     */
    public function getAddressAttr($key, $type = 'billing', $default = '')
    {
        return $this->get("{$type}.{$key}", $default);
    }

    /**
     * Récupération du montant total de la commande.
     *
     * @return float
     */
    public function getTotal()
    {
        return (float)$this->get('total', 0);
    }

    /**
     * Récupération de la méthode de paiement.
     *
     * @return string
     */
    public function getPaymentMethod()
    {
        return (string)$this->get('payment_method', '');
    }

    /**
     * Ajout d'une note à la commande.
     *
     * @param string $note Message de la note.
     * @param bool $is_customer Définie si la note est à destination du client.
     * @param bool $by_user Lorsque la note provient de l'utilisateur.
     *
     * @return int
     */
    public function addNote($note, $is_customer = false, $by_user = false)
    {
        if (! $this->getId()) :
            return 0;
        endif;

        if (($user = $this->users()->get()) && $user->can('edit_shop_order', $this->getId()) && $by_user) :
            $comment_author       = $user->getDisplayName();
            $comment_author_email = $user->getEmail();
        else :
            $comment_author       = __('tiFyShop', 'tify');
            $comment_author_email = strtolower(__('tiFyShop', 'tify')) . '@';
            $comment_author_email .= 'noreply.com';
            $comment_author_email = sanitize_email( $comment_author_email );
        endif;

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

        $comment_id = \wp_insert_comment($commentdata);

        if ($is_customer) :
            \add_comment_meta($comment_id, 'is_customer_note', 1);
        endif;

        return $comment_id;
    }

    /**
     * Vérification de correspondance du statut de la commande.
     *
     * @param string|array $status Statut unique ou liste de statuts de contrôle de correspondance.
     *
     * @return bool
     */
    public function hasStatus($status)
    {
        return in_array($this->getStatus(), (array)$status);
    }

    /**
     * Mise à jour du statut et enregistrement immédiat.
     *
     * @return bool
     */
    public function updateStatus($new_status)
    {
        if (! $this->orders()->isStatus($new_status) || ($this->get('status') === $new_status)) :
            return false;
        endif;

        $this->set('status', $new_status);
        // @todo status_transition

        if (! $this->get('date_paid') && $this->hasStatus($this->orders()->getPaymentCompleteStatuses())) :
            $this->set('date_paid', $this->functions()->date()->utc());
        endif;

        if (! $this->get('date_completed') && $this->hasStatus('completed')) :
            $this->set('date_completed', $this->functions()->date()->utc());
        endif;

        $this->save();

        return true;
    }

    /**
     * Action appelée à l'issue du processus de paiement.
     *
     * @param string $transaction_id Optional Identifiant de qualification de la transaction.
     *
     * @return bool
     */
    public function paymentComplete($transaction_id = '')
    {
        try {
            if (! $this->getId() ) :
                return false;
            endif;

            $this->session()->pull('order_awaiting_payment', false);

            if ($this->hasStatus($this->orders()->getPaymentValidStatuses())) :
                if (! empty($transaction_id)) :
                    $this->transaction_id = $transaction_id;
                endif;
                if (! $this->get('date_paid')) :
                    $this->set('date_paid', $this->functions()->date()->utc('U'));
                endif;

                $this->set('status', $this->needProcessing() ? 'order-processing' : 'order-completed');

                $this->save();
            endif;
        } catch (Exception $e) {
            return false;
        }
        return true;
    }

    /**
     * Création d'une ligne de coupon de réduction.
     *
     * @return object|OrderItemCouponInterface
     */
    public function createItemCoupon()
    {
        // @todo
    }

    /**
     * Création d'une ligne de promotion.
     *
     * @return object|OrderItemFeeInterface
     */
    public function createItemFee()
    {
        // @todo
    }

    /**
     * Création d'une ligne de produit.
     *
     * @return object|OrderItemProductInterface
     */
    public function createItemProduct(ProductItemInterface $product)
    {
        return $this->provide('orders.item_product', [$product, $this->shop, $this]);
    }

    /**
     * Création d'une ligne de livraison.
     *
     * @return object|OrderItemShippingInterface
     */
    public function createItemShipping()
    {
        // @todo
    }

    /**
     * Création d'une ligne de taxe.
     *
     * @return object|OrderItemTaxInterface
     */
    public function createItemTax()
    {
        // @todo
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
     * @param string $type Type d'éléments à récupérer. null pour tous par défaut|coupon|fee|line_item (product)|shipping|tax.
     *
     * @return Collection
     */
    public function getItems($type = null)
    {
        $items = $type ? Arr::get($this->items, $type) : $this->items;

        return new Collection($items);
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
            'post_date'         => $this->functions()->date()->get(),
            'post_date_gmt'     => $this->functions()->date()->utc(),
            'post_status'       => $this->getStatus(),
            'post_parent'       => $this->getParentId(),
            'post_excerpt'      => $this->getExcerpt(true),
            'post_modified'     => $this->functions()->date()->get(),
            'post_modified_gmt' => $this->functions()->date()->utc(),
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
     * Sauvegarde de la liste des éléments.
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

    /**
     * Vérifie si une commande nécessite un paiement.
     *
     * @return bool
     */
    public function needPayment()
    {
        return $this->hasStatus($this->orders()->getNeedPaymentStatuses()) && ($this->getTotal() > 0);
    }

    /**
     * Vérifie si une commande nécessite une intervention avant d'être complétée.
     * @internal Seul les produits téléchargeable et dématerialisé ne nécessite aucune intervention.
     *
     * @return bool
     */
    public function needProcessing()
    {
        if (! $line_items = $this->getItems('line_item')) :
            return false;
        endif;

        $virtual_and_downloadable = $line_items->filter(function($line_item) {
            /** @var OrderItemProductInterface $item */
            return $item->getProduct()->isDownloadable() &&  $item->getProduct()->isVirtual();
        });

        return count($virtual_and_downloadable) === 0;
    }

    /**
     * Récupération de l'url vers la page d'invitation au paiement de la commande.
     *
     * @return string
     */
    public function getCheckoutPaymentUrl()
    {
        return $this->functions()->url()->checkoutOrderPayPage([
            'order-pay' => $this->getId(),
            'key'       => $this->getOrderKey()
        ]);
    }

    /**
     * Récupération de l'url vers la page de paiement reçu.
     * @internal Lorsque le paiement a été accepté.
     *
     * @return string
     */
    public function getCheckoutOrderReceivedUrl()
    {
        return $this->functions()->url()->checkoutOrderReceivedPage([
            'order-received' => $this->getId(),
            'key'            => $this->getOrderKey()
        ]);
    }
}