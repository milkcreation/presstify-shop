<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop\Checkout;

use tiFy\Plugins\Shop\Contracts\{Checkout as CheckoutContract, Order, Shop};
use tiFy\Plugins\Shop\ShopAwareTrait;
use tiFy\Support\Proxy\{Redirect, Request};

class Checkout implements CheckoutContract
{
    use ShopAwareTrait;

    /**
     * CONSTRUCTEUR.
     *
     * @param Shop $shop
     *
     * @return void
     */
    public function __construct(Shop $shop)
    {
        $this->setShop($shop);

        $this->boot();
    }

    /**
     * @inheritDoc
     */
    public function boot(): void { }

    /**
     * @inheritDoc
     */
    public function createOrderItemsCoupon(Order $order): void { }

    /**
     * @inheritDoc
     */
    public function createOrderItemsFee(Order $order): void { }

    /**
     * @inheritDoc
     */
    public function createOrderItemsProduct(Order $order): void
    {
        if ($lines = $this->shop()->cart()->all()) {
            foreach ($lines as $line) {
                $product = $line->getProduct();
                $item = $order->createItemProduct();
                $item->set([
                    'name'         => $product->getTitle(),
                    'product'      => $product->all(),
                    'product_id'   => $product->getId(),
                    'product_sku'  => $product->getSku(),
                    'quantity'     => $line->getQuantity(),
                    'subtotal'     => $line->getSubtotal(),
                    'subtotal_tax' => $line->getSubtotalTax(),
                    'total'        => $line->getTotal(),
                    'total_tax'    => $line->getTax(),
                    'tax_class'    => '',
                    'taxes'        => [],
                    'variation'    => '',
                    'variation_id' => 0,
                ]);

                $purchasing_options = [];
                foreach ($line->get('purchasing_options', []) as $product_id => $opts) {
                    if ($prod = $this->shop()->product($product_id)) {
                        $purchasing_options[$product_id] = [];
                        foreach ($opts as $name => $opt) {
                            if ($po = $prod->getPurchasingOption($name)) {
                                $po->setSelected($opt);
                                $purchasing_options[$product_id][$po->getName()] = [
                                    'selected' => $opt,
                                    'render'   => trim((string)$po->renderCartLine()),
                                    'sku'      => $prod->getSku(),
                                ];
                            }
                        }
                    }
                }
                $item->set('purchasing_options', $purchasing_options);

                $order->addOrderItem($item);
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function createOrderItemsShipping(Order $order): void { }

    /**
     * @inheritDoc
     */
    public function createOrderItemsTax(Order $order): void { }

    /**
     * @inheritDoc
     */
    public function handleProcess()
    {
        // Définition de l'url de redirection
        if (!$redirect = Request::input('_wp_http_referer', '') ?: $this->shop()->functions()->url()->checkoutPage()) {
            $redirect = Request::header('referer', get_home_url());
        }

        if (!wp_verify_nonce(Request::input('_wpnonce', ''), 'tify_shop-process_checkout')) {
            // Vérification de la validité de la requête.
            $this->shop()->notices()->add(
                __('Impossible de procéder à votre commande, merci de réessayer.', 'tify'), 'error'
            );

            return Redirect::to($redirect);
        } elseif ($this->shop()->cart()->isEmpty()) {
            // Vérification du contenu du panier.
            $this->shop()->notices()->add(__('Désolé, il semblerait que votre session ait expirée.', 'tify'), 'error');

            return Redirect::to($redirect);
        }

        // Récupération des données de formulaire
        $data = [
            'terms'                              => Request::input('terms', 1),
            'createaccount'                      => (int)!empty($_POST['createaccount']),
            'payment_method'                     => Request::input('payment_method', ''),
            'shipping_method'                    => Request::input('shipping_method', ''),
            'ship_to_different_address'          => Request::input('ship_to_different_address', false),
            'woocommerce_checkout_update_totals' => Request::input('checkout_update_totals', false),
        ];

        // Données de champ
        $fieldsets = [
            'billing'  => $this->shop()->addresses()->billing()->fields(),
            'shipping' => $this->shop()->addresses()->shipping()->fields(),
            'order'    => [
                'comments' => [
                    'type'        => 'textarea',
                    'label'       => 'note de commande',
                    'placeholder' => __('Commentaires concernant votre commande, ex.: consignes de livraison', 'tify'),
                ],
            ],
        ];

        // Données de champs ignorés
        $skipped_fieldsets = [];
        if (!$data['ship_to_different_address']) {
            array_push($skipped_fieldsets, 'shipping');
        }

        // Hydratation des données de champs déclarés (hors ignorés)
        foreach ($fieldsets as $key => $fields) {
            if (in_array($key, $skipped_fieldsets)) {
                continue;
            }
            foreach ($fields as $slug => $attrs) {
                $data["{$key}_{$slug}"] = Request::input(
                    "{$key}_{$slug}",
                    $this->shop()->session()->get("{$key}.{$slug}", '')
                );
            }
        }

        // Hydratation des données de champs ignorés
        if (in_array('shipping', $skipped_fieldsets)) {
            foreach ($fieldsets['shipping'] as $slug => $attrs) {
                $data["shipping_{$slug}"] = isset($data["billing_{$slug}"]) ? $data["billing_{$slug}"] : '';
            }
        }

        // Mise à jour des données de session
        // @todo EVOLUTION : Enregistrer les données de session + utilisateur billing & shipping

        // Livraison
        $chosen_shipping_methods = $this->shop()->session()->get('chosen_shipping_methods', []);
        if (is_array($data['shipping_method'])) {
            foreach ($data['shipping_method'] as $i => $value) {
                $chosen_shipping_methods[$i] = $value;
            }
        }
        $this->shop()->session()->put('chosen_shipping_methods', $chosen_shipping_methods);

        // Méthode de paiement
        $this->shop()->session()->put('chosen_payment_method', $data['payment_method']);

        // DEBUG - données de session
        // var_dump($this->session()->all());

        // Vérification de l'intégrité des données soumises par le formulaire de paiement
        // Données de facturation
        $fieldset_errors = [];
        foreach ($fieldsets as $fieldset_key => $fieldset) {
            foreach ($fieldset as $slug => $field) {
                if (!isset($field['required'])) {
                    continue;
                } elseif ($field['required'] !== true) {
                    continue;
                }

                $field_label = isset($field['label']) ? strtolower($field['label']) : $slug;
                switch ($fieldset_key) {
                    case 'billing' :
                        $field_label = sprintf(__('Adresse de facturation : le champ %s', 'tify'), $field_label);
                        break;
                    case 'shipping' :
                        $field_label = sprintf(__('Adresse de livraison : le champ %s', 'tify'), $field_label);
                        break;
                }

                if ($field['required'] && '' === $data[$fieldset_key . '_' . $slug]) {
                    $fieldset_errors[] = sprintf(
                        __('%1$s est requis pour pouvoir procèder à la commande.', 'woocommerce'),
                        esc_html($field_label)
                    );
                }
            }
        }

        if ($fieldset_errors) {
            foreach ($fieldset_errors as $error) {
                $this->shop()->notices()->add($error, 'error');
            }

            return Redirect::to($redirect);
        }

        // @todo EVOLUTION : Vérifier les données de panier : status du produit | disponibilité en stock

        // Conditions générales validées
        if (empty($data['terms'])) {
            $this->shop()->notices()->add(
                __('Veuillez prendre connaissance et accepter les conditions générales de vente.', 'tify'), 'error'
            );

            return Redirect::to($redirect);
        }

        // Adresse de livraison
        if ($this->shop()->cart()->needShipping()) {
            $this->shop()->notices()->add(__('Aucune méthode de livraison n\a été choisie.', 'tify'), 'error');

            return Redirect::to($redirect);
        }

        if ($this->shop()->cart()->needPayment()) {
            if (empty($data['payment_method'])) {
                $this->shop()->notices()->add(
                    __('Merci de bien vouloir sélectionner votre mode de paiement.', 'tify'), 'error'
                );

                return Redirect::to($redirect);
            } elseif (!$gateway = $this->shop()->gateways()->get($data['payment_method'])) {
                $this->shop()->notices()->add(
                    __('Désolé, le mode de paiement choisi n\'est pas valide dans cette boutique.', 'tify'), 'error'
                );

                return Redirect::to($redirect);
            }
        } else {
            $gateway = null;
        }

        $order = ($order_id = $this->shop()->session()->get('order_awaiting_payment', 0))
            ? $this->shop()->order($order_id)
            : $this->shop()->orders()->create();

        if (!$this->shop()->orders()->is($order)) {
            $this->shop()->notices()->add(
                __('Désolé, impossible de procéder à votre commande, veuillez réessayer.', 'tify'), 'error'
            );

            return Redirect::to($redirect);
        }

        if ($order->getId() === $this->shop()->session()->get('order_awaiting_payment', 0)) {
            $order->set('status', $this->shop()->orders()->getDefaultStatus());
        }

        if ($order->has('cart_hash') && $order->hasStatus(['order-pending', 'order-failed'])) {
            $order->removeOrderItems();
        }

        $created_via = 'checkout';
        $cart_hash = md5(json_encode($this->shop()->cart()->all()) . $this->shop()->cart()->total());
        $customer_id = $this->shop()->users()->get()->getId();
        $currency = $this->shop()->settings()->currency();
        $prices_include_tax = $this->shop()->settings()->isPricesIncludeTax();
        $customer_ip_address = Request::ip();
        $customer_user_agent = Request::header('User-Agent');
        $customer_note = isset($data['order_comments']) ? $data['order_comments'] : '';
        $payment_method = $data['payment_method'];
        $payment_method_title = $gateway ? $gateway->getTitle() : '';
        $shipping_total = $this->shop()->cart()->total()->getShippingTotal();
        $shipping_tax = $this->shop()->cart()->total()->getShippingTax();
        $discount_total = $this->shop()->cart()->total()->getDiscountTotal();
        $discount_tax = $this->shop()->cart()->total()->getDiscountTax();
        $cart_tax = $this->shop()->cart()->total()->getGlobalTax() + $this->shop()->cart()->total()->getFeeTax();
        $total = $this->shop()->cart()->total()->getGlobal();

        $this->createOrderItemsProduct($order);
        $this->createOrderItemsFee($order);
        $this->createOrderItemsShipping($order);
        $this->createOrderItemsTax($order);
        $this->createOrderItemsCoupon($order);

        $order_datas = compact(
            'created_via', 'cart_hash', 'customer_id', 'currency', 'prices_include_tax', 'customer_ip_address',
            'customer_user_agent', 'customer_note', 'payment_method', 'payment_method_title', 'shipping_total',
            'shipping_tax',
            'discount_total', 'discount_tax', 'cart_tax', 'total'
        );

        foreach ($data as $key => $value) {
            if (preg_match('#^billing_(.*)#', $key, $match)) {
                $order->setBilling($match[1], $value);
            } elseif (preg_match('#^shipping_(.*)#', $key, $match)) {
                $order->setShipping($match[1], $value);
            } else {
                $order->set($key, $value);
            }
        }

        foreach ($order_datas as $key => $value) {
            $order->set($key, $value);
        }

        events()->trigger('shop.checkout.create_order', [&$this, $order]);

        $order->update();

        if ($this->shop()->cart()->needPayment()) {
            $this->shop()->session()->put('order_awaiting_payment', $order->getId())->save();

            $result = $gateway->processPayment($order);
            $redirect = $result['redirect'] ?? $redirect;
        }

        events()->trigger('shop.checkout.proceeded', [&$this, $order]);

        return Redirect::to($redirect);
    }

    /**
     * @inheritDoc
     */
    public function processUrl(): string
    {
        return $this->shop()->action('checkout.process');
    }
}