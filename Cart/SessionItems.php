<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop\Cart;

use tiFy\Plugins\Shop\Contracts\{CartInterface as CartContract,
    CartSessionItemsInterface as CartSessionItemsContract,
    ShopInterface as Shop};
use tiFy\Plugins\Shop\ShopAwareTrait;
use tiFy\Support\{Arr, ParamsBag};

class SessionItems extends ParamsBag implements CartSessionItemsContract
{
    use ShopAwareTrait;

    /**
     * CONSTRUCTEUR.
     *
     * @param Shop $shop Instance de la boutique.
     *
     * @return void
     */
    public function __construct(Shop $shop)
    {
        $this->shop = $shop;
    }

    /**
     * @inheritDoc
     */
    public function cart(): CartContract
    {
        return $this->shop()->resolve('cart');
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
    public function destroy($persistent = true): void
    {
        foreach ($this->all() as $key => $default) {
            $this->shop()->session()->put($key, $default);
        }
        $this->shop()->session()->put('order_awaiting_payment', 0);
        $this->shop()->session()->save();

        if ($persistent) {
            delete_user_option($this->shop()->users()->getItem()->getId(), '_tify_shop_cart');
        }
    }

    /**
     * @inheritDoc
     */
    public function fetchCart(): void
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
                $product = $this->shop()->products()->getItem($line['product_id']);
                $quantity = $line['quantity'];

                if (!$product || ($quantity < 0)) {
                    continue;
                } else {
                    if (!$product->isPurchasable()) {
                        // do_action( 'woocommerce_remove_cart_item_from_session', $key, $values );
                    } else {
                        $this->cart()->add($key, compact('key', 'quantity', 'product'));
                    }
                }
            }
        }

        $this->cart()->calculate();
    }

    /**
     * @inheritDoc
     */
    public function update(): void
    {
        $cart_totals = $this->cart()->calculate()->all();

        $cart = [];
        $lines = $this->cart()->lines();

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

        $this->shop()->session()->save();

        if ($user_id = get_current_user_id()) {
            update_user_option($user_id, '_tify_shop_cart', ['cart' => $cart]);
        }
    }
}