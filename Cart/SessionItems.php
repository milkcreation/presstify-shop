<?php

/**
 * @name SessionItems
 * @desc Gestion des données des éléments du panier d'achat portées par la session
 * @package presstiFy
 * @namespace \tiFy\Plugins\Shop\Cart
 * @version 1.1
 * @since 1.2.535
 *
 * @author Jordy Manner <jordy@tigreblanc.fr>
 * @copyright Milkcreation
 */

namespace tiFy\Plugins\Shop\Cart;

use Illuminate\Support\Arr;
use Illuminate\Support\Fluent;
use tiFy\App\Traits\App as TraitsApp;
use tiFy\Plugins\Shop\ServiceProvider\ProvideTraits;
use tiFy\Plugins\Shop\ServiceProvider\ProvideTraitsInterface;
use tiFy\Plugins\Shop\Shop;

class SessionItems extends Fluent implements SessionItemsInterface, ProvideTraitsInterface
{
    use TraitsApp, ProvideTraits;

    /**
     * Classe de rappel de la boutique
     * @var Shop
     */
    protected $shop;

    /**
     * Classe de rappel de gestion des données des élements contenu dans le panier
     * @var CartInterface
     */
    protected $cart;

    /**
     * Définition des attributs par défaut du panier porté par la session
     *
     * @var array
     */
    protected $defaults = [
        'cart'                       => [],
        'cart_totals'                => [],
        'applied_coupons'            => [],
        'coupon_discount_totals'     => [],
        'coupon_discount_tax_totals' => [],
        'removed_cart_contents'      => []
    ];

    /**
     * CONSTRUCTEUR
     *
     * @return void
     */
    public function __construct(Shop $shop, CartInterface $cart)
    {
        parent::__construct($this->defaults);

        // Définition de la classe de rappel de la boutique
        $this->shop = $shop;

        // Définition du panier
        $this->cart = $cart;
    }

    /**
     * Récupération des articles du panier portés par la session.
     *
     * @return void
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
        foreach($this->defaults as $key => $default) :
            ${$key} = $this->session()->get($key, $default);
        endforeach;

        $stored_cart = get_user_option('_tify_shop_cart') ? : ['cart' => []];

        if ($stored_cart) :
            $cart = array_merge($cart, Arr::get($stored_cart, 'cart', []));
        endif;

        if ($cart) :
            foreach ($cart as $key => $line) :
                $product = $this->products()->get($line['product_id']);
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
     * Détruit les données de session associées au panier.
     *
     * @param bool $persistent Active la suppression des données de panier relatives aux options utilisateur
     *
     * @return void
     */
    public function destroy($persistent = true)
    {
        foreach($this->defaults as $key => $default) :
            $this->session()->put($key, $default);
        endforeach;
        $this->session()->save();

        if ($persistent) :
            \delete_user_option($this->users()->get()->getId(), '_tify_shop_cart');
        endif;
    }

    /**
     * Mise à jour des données des éléments du panier portées par la session.
     *
     * @return void
     */
    public function update()
    {
        // Récupération des totaux
        $cart_totals = $this->cart->calculate()->toArray();

        // Préparation de la session
        $cart = [];
        $lines = $this->cart->lines()->toArray();
        foreach($lines as $key => $line) :
            unset($line['product']);
            $cart[$key] = $line;
        endforeach;

        // Mise à jour des données de session
        $attributes = array_merge(
            $this->defaults,
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