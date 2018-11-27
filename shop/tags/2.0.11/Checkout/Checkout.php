<?php

/**
 * @name Checkout
 * @desc Controleur de gestion des réglages de la boutique
 * @package presstiFy
 * @namespace \tiFy\Plugins\Shop\Checkout
 * @version 1.1
 * @since 1.2.600
 *
 * @author Jordy Manner <jordy@tigreblanc.fr>
 * @copyright Milkcreation
 */

namespace tiFy\Plugins\Shop\Checkout;

use Illuminate\Support\Arr;
use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;
use tiFy\Plugins\Shop\AbstractShopSingleton;
use tiFy\Plugins\Shop\Contracts\CheckoutInterface;
use tiFy\Plugins\Shop\Contracts\OrderInterface;

class Checkout extends AbstractShopSingleton implements CheckoutInterface
{
    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        add_action(
            'after_setup_tify',
            function() {
                // Ajout d'un produit au panier
                router(
                    'shop.checkout.process',
                    [
                        'method' => 'POST',
                        'path'   => '/commander',
                        'cb'     => [$this, 'process']
                    ]
                );
            }
        );
    }

    /**
     * {@inheritdoc}
     */
    public function createOrderItemsCoupon(OrderInterface $order)
    {

    }

    /**
     * {@inheritdoc}
     */
    public function createOrderItemsFee(OrderInterface $order)
    {

    }

    /**
     * {@inheritdoc}
     */
    public function createOrderItemsProduct(OrderInterface $order)
    {
        if ($lines = $this->cart()->lines()) :
            foreach($lines as $line) :
                $product = $line->getProduct();
                $item = $order->createItemProduct();
                $item
                    ->set('name', $product->getTitle())
                    ->set('quantity', $line->getQuantity())
                    ->set('variation', '')
                    ->set('subtotal', $line->getSubtotal())
                    ->set('subtotal_tax', $line->getSubtotalTax())
                    ->set('total', $line->getTotal())
                    ->set('total_tax', $line->getTax())
                    ->set('taxes', [])
                    ->set('tax_class', '')
                    ->set('product_id', $product->getId())
                    ->set('product', $product->all())
                    ->set('variation_id', 0);

                $order->addItem($item);
            endforeach;
        endif;
    }

    /**
     * {@inheritdoc}
     */
    public function createOrderItemsShipping(OrderInterface $order)
    {

    }

    /**
     * {@inheritdoc}
     */
    public function createOrderItemsTax(OrderInterface $order)
    {

    }

    /**
     * {@inheritdoc}
     */
    public function process()
    {
        $request = request();

        // Définition de l'url de redirection
        if ($redirect = $request->request->get('_wp_http_referer', '')) :
        elseif ($redirect = $this->functions()->url()->checkoutPage()) :
        elseif (!$redirect = wp_get_referer()) :
            $redirect = get_home_url();
        endif;

        // Vérification de la validité de la requête
        if (!wp_verify_nonce($request->request->get('_wpnonce', ''), 'tify_shop-process_checkout')) :
            $this->notices()->add(__('Impossible de procéder à votre commande, merci de réessayer.', 'tify'),
                'error');

            wp_redirect($redirect);
            exit;
        endif;

        // Vérification du contenu du panier
        if ($this->cart()->isEmpty()) :
            $this->notices()->add(__('Désolé, il semblerait votre session ait expirée.', 'tify'), 'error');

            wp_redirect($redirect);
            exit;
        endif;

        // Récupération des données de formulaire
        $data = [
            'terms'                              => $request->request->getInt('terms', 1),
            'createaccount'                      => (int)!empty($_POST['createaccount']),
            'payment_method'                     => $request->request->get('payment_method', ''),
            'shipping_method'                    => $request->request->get('shipping_method', ''),
            'ship_to_different_address'          => $request->request->getBoolean('ship_to_different_address', false),
            'woocommerce_checkout_update_totals' => $request->request->getBoolean('checkout_update_totals', false)
        ];

        // Données de champ
        $fieldsets = [
            'billing'  => $this->addresses()->billing()->fields(),
            'shipping' => $this->addresses()->shipping()->fields(),
            'order'    => [
                'comments' => [
                    'type'        => 'textarea',
                    'label'       => 'note de commande',
                    'placeholder' => __('Commentaires concernant votre commande, ex.: consignes de livraison', 'tify')
                ]
            ]
        ];

        // Données de champs ignorés
        $skipped_fieldsets = [];
        if (! $data['ship_to_different_address']) :
            array_push($skipped_fieldsets, 'shipping');
        endif;

        // Hydratation des données de champs déclarés (hors ignorés)
        foreach ($fieldsets as $key => $fields) :
            if (in_array($key, $skipped_fieldsets)) :
                continue;
            endif;

            foreach ($fields as $slug => $attrs) :
                $data["{$key}_{$slug}"] = $request->request->get(
                    "{$key}_{$slug}",
                    $this->session()->get("{$key}.{$slug}", '')
                );
            endforeach;
        endforeach;

        // Hydratation des données de champs ignorés
        if (in_array('shipping', $skipped_fieldsets)) :
            foreach($fieldsets['shipping'] as $slug => $attrs) :
                $data["shipping_{$slug}"] = isset($data["billing_{$slug}"]) ? $data["billing_{$slug}"] : '';
            endforeach;
        endif;

        // Mise à jour des données de session
        // @todo enregistrer les données de session + utilisateur billing & shipping

        // Livraison
        $chosen_shipping_methods = $this->session()->get('chosen_shipping_methods', []);
        if (is_array($data['shipping_method'])) :
            foreach ($data['shipping_method'] as $i => $value) :
                $chosen_shipping_methods[$i] = $value;
            endforeach;
        endif;
        $this->session()->put('chosen_shipping_methods', $chosen_shipping_methods);

        // Méthode de paiement
        $this->session()->put('chosen_payment_method', $data['payment_method']);

        // DEBUG - données de session
        // var_dump($this->session()->all());

        // Vérification de l'intégrité des données soumises par le formulaire de paiement
        // Données de facturation
        $fieldset_errors = [];
        foreach ($fieldsets as $fieldset_key => $fieldset) :
            foreach ($fieldset as $slug => $field) :
                if (!isset($field['required'])) :
                    continue;
                endif;
                if ($field['required'] !== true) :
                    continue;
                endif;

                $field_label = isset($field['label']) ? strtolower($field['label']) : $slug;
                switch ($fieldset_key) :
                    case 'billing' :
                        $field_label = sprintf(__('Adresse de facturation : le champ %s', 'tify'), $field_label);
                        break;
                    case 'shipping' :
                        $field_label = sprintf(__('Adresse de livraison : le champ %s', 'tify'), $field_label);
                        break;
                endswitch;

                if ($field['required'] && '' === $data[$fieldset_key . '_'. $slug]) :
                    $fieldset_errors[] = sprintf(
                        __('%1$s est requis pour pouvoir procèder à la commande.', 'woocommerce'),
                        esc_html($field_label)
                    );
                endif;
            endforeach;
        endforeach;

        if ($fieldset_errors) :
            foreach($fieldset_errors as $error) :
                $this->notices()->add($error, 'error');
            endforeach;

            wp_redirect($redirect);
            exit;
        endif;

        // @todo vérifier les données de panier : status du produit | disponibilité en stock

        // Conditions générales validées
        if (empty($data['terms'])) :
            $this->notices()->add(__('Veuillez prendre connaissance et accepter les conditions générales de vente.',
                'tify'), 'error');

            wp_redirect($redirect);
            exit;
        endif;

        // Adresse de livraison
        if ($this->cart()->needShipping()) :
            $this->notices()->add(__('Aucune méthode de livraison n\a été choisie.', 'tify'), 'error');

            wp_redirect($redirect);
            exit;
        endif;

        if ($this->cart()->needPayment()) :
            if (empty($data['payment_method'])) :
                $this->notices()->add(__('Merci de bien vouloir sélectionner votre mode de paiement.',
                    'tify'), 'error');

                wp_redirect($redirect);
                exit;
            elseif (!$gateway = $this->gateways()->get($data['payment_method'])) :
                $this->notices()->add(__('Désolé, le mode de paiement choisie n\'est pas valide dans cette boutique.',
                    'tify'), 'error');

                wp_redirect($redirect);
                exit;
            endif;
        endif;

        /** @var OrderInterface $order */
        $order = ($order_id = $this->session()->get('order_awaiting_payment', 0))
            ? $this->orders()->getItem($order_id)
            : $this->orders()->create();

        if (!$this->orders()->is($order)) :
            $this->notices()->add(__('Désolé, impossible de procéder à votre commande, veuillez réessayer.',
                'tify'), 'error');

            wp_redirect($redirect);
            exit;
        endif;

        $created_via = 'checkout';
        $cart_hash = md5(json_encode($this->cart()->getList()) . $this->cart()->getTotals());
        $customer_id = $this->users()->getItem()->getId();
        $currency = $this->settings()->currency();
        $prices_include_tax = $this->settings()->isPricesIncludeTax();
        $customer_ip_address = $request->getClientIp();
        $customer_user_agent = $request->headers->get('User-Agent');
        $customer_note = isset($data['order_comments']) ? $data['order_comments'] : '';
        $payment_method_title = $gateway->getTitle();
        $shipping_total = $this->cart()->getTotals()->getShippingTotal();
        $shipping_tax = $this->cart()->getTotals()->getShippingTax();
        $discount_total = $this->cart()->getTotals()->getDiscountTotal();
        $discount_tax = $this->cart()->getTotals()->getDiscountTax();
        $cart_tax = $this->cart()->getTotals()->getGlobalTax() + $this->cart()->getTotals()->getFeeTax();
        $total = $this->cart()->getTotals()->getGlobal();

        // Liste des articles du panier associés à la commande
        $this->createOrderItemsProduct($order);

        // Liste des promotions associées à la commande
        $this->createOrderItemsFee($order);

        // Liste des livraisons associées à la commande
        $this->createOrderItemsShipping($order);

        // Liste des taxes associées à la commandes
        $this->createOrderItemsTax($order);

        // Liste des coupons de réduction associé à la commande
        $this->createOrderItemsCoupon($order);

        $order_datas = compact(
            'created_via', 'cart_hash', 'customer_id', 'currency', 'prices_include_tax', 'customer_ip_address',
            'customer_user_agent', 'customer_note', 'payment_method', 'payment_method_title', 'shipping_total',
            'shipping_tax',
            'discount_total', 'discount_tax', 'cart_tax', 'total'
        );
        foreach ($data as $key => $value) :
            if (preg_match('#^billing_(.*)#', $key, $match)) :
                $order->setBillingAttr($match[1], $value);
            elseif (preg_match('#^shipping_(.*)#', $key, $match)) :
                $order->setShippingAttr($match[1], $value);
            else :
                $order->set($key, $value);
            endif;
        endforeach;

        foreach ($order_datas as $key => $value) :
            $order->set($key, $value);
        endforeach;

        events()->trigger('tify.plugins.shop.checkout.create_order', [&$this]);

        $order->create();

        $order->save();

        if ($this->cart()->needPayment()) :
            $this->session()
                ->put('order_awaiting_payment', $order->getId())
                ->save();

            $result = $gateway->processPayment($order);
        endif;

        wp_redirect(Arr::get($result, 'redirect', $redirect));
        exit;
    }

    /**
     * {@inheritdoc}
     */
    public function processUrl()
    {
        return route('shop.checkout.process');
    }
}