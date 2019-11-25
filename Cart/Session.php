<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop\Cart;

use tiFy\Plugins\Shop\Contracts\{Cart, CartSession as CartSessionContract};
use tiFy\Plugins\Shop\ShopAwareTrait;
use tiFy\Support\{Arr, ParamsBag};

class Session extends ParamsBag implements CartSessionContract
{
    use ShopAwareTrait;

    /**
     * Instance du panier de commande associé.
     * @var Cart
     */
    protected $cart;

    /**
     * CONSTRUCTEUR.
     *
     * @param Cart $cart
     *
     * @return void
     */
    public function __construct(Cart $cart)
    {
        $this->cart = $cart;

        $this->setShop($this->cart->shop());
    }

    /**
     * @inheritDoc
     */
    public function cart(): Cart
    {
        return $this->cart;
    }

    /**
     * @inheritDoc
     */
    public function defaults(): array
    {
        return [
            'cart'                       => [],
            'cart_totals'                => [],
            'applied_coupons'            => [],
            'coupon_discount_totals'     => [],
            'coupon_discount_tax_totals' => [],
            'removed_cart_contents'      => [],
        ];
    }

    /**
     * @inheritDoc
     */
    public function destroy($persistent = true): CartSessionContract
    {
        foreach ($this->all() as $key => $default) {
            $this->shop()->session()->put($key, $default);
        }
        $this->shop()->session()->forget('order_awaiting_payment');
        $this->shop()->session()->save();

        if ($persistent) {
            delete_user_option($this->shop()->user()->getId(), '_tify_shop_cart');
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function fetchCart(): CartSessionContract
    {
        /**
         * @var array $cart
         * @var array $cart_totals
         * @var array $applied_coupons
         * @var array $coupon_discount_totals
         * @var array $coupon_discount_tax_totals
         * @var array $removed_cart_contents
         */
        foreach ($this->all() as $key => $default) {
            ${$key} = $this->shop()->session()->get($key, $default);
        }

        if ($stored_cart = get_user_option('_tify_shop_cart') ?: ['cart' => []]) {
            $cart = array_merge($cart, Arr::get($stored_cart, 'cart', []));
        }
        if (!empty($cart)) {
            foreach ($cart as $key => $line) {
                $product = $this->shop()->product($line['product_id']);
                $quantity = $line['quantity'];

                if (!$product || ($quantity < 0)) {
                    continue;
                } else {
                    if (!$product->isPurchasable()) {
                        // do_action( 'woocommerce_remove_cart_item_from_session', $key, $values );
                    } else {
                        $line['product'] = $product;
                        $this->cart()->add($key, $line);
                    }
                }
            }
        }

        $this->cart()->calculate();

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function update(): CartSessionContract
    {
        $cart_totals = $this->cart()->calculate()->all();

        $cart = [];
        $lines = $this->cart()->all();

        foreach ($lines as $key => $line) {
            unset($line['product']);
            $cart[$key] = $line->all();
        }

        // Mise à jour des données de session
        $attributes = array_merge($this->all(), [
            'cart'        => $cart,
            'cart_totals' => $cart_totals,
        ]);

        foreach ($attributes as $key => $value) {
            $this->shop()->session()->put($key, $value);
        }

        if ($user_id = get_current_user_id()) {
            update_user_option($user_id, '_tify_shop_cart', ['cart' => $cart]);
        }

        return $this;
    }
}