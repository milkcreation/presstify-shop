<?php

/**
 * @name SessionItems
 * @desc Gestion des données des éléments du panier d'achat portées par la session.
 *
 * @author Jordy Manner <jordy@tigreblanc.fr>
 * @copyright Milkcreation
 */

namespace tiFy\Plugins\Shop\Cart;

use Illuminate\Support\Arr;
use tiFy\Kernel\Params\ParamsBag;
use tiFy\Plugins\Shop\Contracts\CartInterface;
use tiFy\Plugins\Shop\Contracts\CartSessionItemsInterface;
use tiFy\Plugins\Shop\Shop;
use tiFy\Plugins\Shop\ShopResolverTrait;

class SessionItems extends ParamsBag implements CartSessionItemsInterface
{
    use ShopResolverTrait;

    /**
     * Instance du controleur de panier.
     * @var CartInterface
     */
    protected $cart;

    /**
     * Listes des attributs du panier porté par la session.
     *
     * @var array
     */
    protected $attributes = [
        'cart'                       => [],
        'cart_totals'                => [],
        'applied_coupons'            => [],
        'coupon_discount_totals'     => [],
        'coupon_discount_tax_totals' => [],
        'removed_cart_contents'      => []
    ];

    /**
     * CONSTRUCTEUR.
     *
     * @param CartInterface $cart Instance de gestion des données des élements contenu dans le panier.
     * @param Shop $shop Instance de la boutique.
     *
     * @return void
     */
    public function __construct(CartInterface $cart, Shop $shop)
    {
        $this->shop = $shop;
        $this->cart = $cart;

        parent::__construct($this->attributes);
    }

    /**
     * {@inheritdoc}
     */
    public function destroy($persistent = true)
    {
        foreach($this->all() as $key => $default) :
            $this->session()->put($key, $default);
        endforeach;
        $this->session()->put('order_awaiting_payment', 0);
        $this->session()->save();

        if ($persistent) :
            \delete_user_option($this->users()->getItem()->getId(), '_tify_shop_cart');
        endif;
    }

    /**
     * {@inheritdoc}
     */
    public function getCart()
    {
        /**
         * @var array $cart
         * @var array $cart_totals
         * @var array $applied_coupons
         * @var array $coupon_discount_totals
         * @var array $coupon_discount_tax_totals
         * @var array $removed_cart_contents
         */
        foreach($this->toArray() as $key => $default) :
            ${$key} = $this->session()->get($key, $default);
        endforeach;

        $stored_cart = get_user_option('_tify_shop_cart') ? : ['cart' => []];

        if ($stored_cart) :
            $cart = array_merge($cart, Arr::get($stored_cart, 'cart', []));
        endif;

        if ($cart) :
            foreach ($cart as $key => $line) :
                $product = $this->products()->getItem($line['product_id']);
                $quantity = $line['quantity'];

                if (!$product || ($quantity < 0)) :
                    continue;
                endif;

                if (!$product->isPurchasable()) :
                    // do_action( 'woocommerce_remove_cart_item_from_session', $key, $values );
                else :
                    $this->cart->add($key, compact('key', 'quantity', 'product'));
                endif;
            endforeach;
        endif;

        $this->cart->calculate();
    }

    /**
     * {@inheritdoc}
     */
    public function update()
    {
        // Récupération des totaux
        $cart_totals = $this->cart->calculate()->all();

        // Préparation de la session
        $cart = [];
        $lines = $this->cart->lines()->toArray();
        foreach($lines as $key => $line) :
            unset($line['product']);
            $cart[$key] = $line;
        endforeach;

        // Mise à jour des données de session
        $attributes = array_merge(
            $this->all(),
            [
                'cart'        => $cart,
                'cart_totals' => $cart_totals
            ]
        );

        foreach($attributes as $key => $value) :
            $this->session()->put($key, $value);
        endforeach;

        $this->session()->save();

        if ($user_id = get_current_user_id()) :
            update_user_option(
                $user_id,
                '_tify_shop_cart',
                ['cart' => $cart]
            );
        endif;
    }
}