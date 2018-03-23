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

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;
use tiFy\App\Traits\App as TraitsApp;
use tiFy\Core\Route\Route;
use tiFy\Plugins\Shop\Shop;

class Checkout
{
    use TraitsApp;

    /**
     * Instance de la classe
     * @var Checkout
     */
    private static $instance;

    /**
     * Classe de rappel de la boutique
     * @var Shop
     */
    protected $shop;

    /**
     * CONSTRUCTEUR
     *
     * @param Shop $shop Classe de rappel de la boutique
     *
     * @return void
     */
    protected function __construct(Shop $shop)
    {
        // Définition de la classe de rappel de la boutique
        $this->shop = $shop;

        // Déclaration des événements
        $this->appAddAction('tify_route_register', null, 0);
    }

    /**
     * Court-circuitage de l'implémentation
     *
     * @return void
     */
    protected function __clone()
    {

    }

    /**
     * Court-circuitage de l'implémentation
     *
     * @return void
     */
    protected function __wakeup()
    {

    }

    /**
     * Instanciation de la classe
     *
     * @param Shop $shop
     *
     * @return Checkout
     */
    public static function make(Shop $shop)
    {
        if (self::$instance) :
            return self::$instance;
        endif;

        return self::$instance = new self($shop);
    }

    /**
     * Déclaration du chemin des routes
     *
     * @return void
     */
    final public function tify_route_register()
    {
        // Ajout d'un produit au panier
        Route::register(
            'tify.plugins.shop.checkout.process',
            [
                'method' => 'post',
                'path'   => '/commander',
                'cb'     => function (ServerRequestInterface $psrRequest, ResponseInterface $psrResponse) {
                    $this->appAddAction(
                        'wp_loaded',
                        function () use ($psrRequest, $psrResponse) {
                            call_user_func_array([$this, 'process'], [$psrRequest, $psrResponse]);
                        },
                        20
                    );
                }
            ]
        );
    }

    /**
     * Url d'action d'exécution de la commande
     * @internal Requête de type POST; l'url doit être intégrée en tant qu'attribut "action" d'une balise d'ouverture de formulaire ayant pour attribut "method" POST
     *
     * @return string
     */
    public function processUrl()
    {
        return Route::url('tify.plugins.shop.checkout.process');
    }

    /**
     * Traitement de la commande
     *
     * @param ServerRequestInterface $psrRequest Requête HTTP Psr-7
     * @param ResponseInterface $psrResponse Requête HTTP Psr-7
     *
     * @return void
     */
    final public function process(ServerRequestInterface $psrRequest, ResponseInterface $psrResponse)
    {
        /**
         * Conversion de la requête PSR-7
         * @see https://symfony.com/doc/current/components/psr7.html
         * @var \Symfony\Component\HttpFoundation\Request $request
         */
        $request = (new HttpFoundationFactory())->createRequest($psrRequest);

        // Définition de l'url de redirection
        if ($redirect = $request->request->get('_wp_http_referer', '')) :
        elseif ($redirect = $this->shop->functions()->url()->checkoutPage()) :
        elseif (!$redirect = \wp_get_referer()) :
            $redirect = get_home_url();
        endif;

        // Vérification de la validité de la requête
        if (!\wp_verify_nonce($request->request->get('_wpnonce', ''), 'tify_shop-process_checkout')) :
            $this->shop->notices()->add(__('Impossible de procéder à votre commande, merci de réessayer.', 'tify'),
                'error');

            \wp_redirect($redirect);
            exit;
        endif;

        // Vérification du contenu du panier
        if ($this->shop->cart()->isEmpty()) :
            $this->shop->notices()->add(__('Désolé, il semblerait votre session ait expirée.', 'tify'), 'error');

            \wp_redirect($redirect);
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
            'billing'  => $this->shop->addresses()->billing()->fields(),
            'shipping' => $this->shop->addresses()->shipping()->fields(),
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
                    $this->shop->session()->get("{$key}.{$slug}", '')
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
        $chosen_shipping_methods = $this->shop->session()->get('chosen_shipping_methods', []);
        if (is_array($data['shipping_method'])) :
            foreach ($data['shipping_method'] as $i => $value) :
                $chosen_shipping_methods[$i] = $value;
            endforeach;
        endif;
        $this->shop->session()->put('chosen_shipping_methods', $chosen_shipping_methods);

        // Méthode de paiement
        $this->shop->session()->put('chosen_payment_method', $data['payment_method']);

        // DEBUG - données de session
        // var_dump($this->shop->session()->all());

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
                $this->shop->notices()->add($error, 'error');
            endforeach;

            \wp_redirect($redirect);
            exit;
        endif;

        // @todo vérifier les données de panier : status du produit | disponibilité en stock

        // Conditions générales validées
        if (empty($data['terms'])) :
            $this->shop->notices()->add(__('Veuillez prendre connaissance et accepter les conditions générales de vente.',
                'tify'), 'error');

            \wp_redirect($redirect);
            exit;
        endif;

        // Adresse de livraison
        if ($this->shop->cart()->needShipping()) :
            $this->shop->notices()->add(__('Aucune méthode de livraison n\a été choisie.', 'tify'), 'error');

            \wp_redirect($redirect);
            exit;
        endif;

        if ($this->shop->cart()->needPayment()) :
            if (empty($data['payment_method'])) :
                $this->shop->notices()->add(__('Merci de bien vouloir sélectionner votre mode de paiement.',
                    'tify'), 'error');

                \wp_redirect($redirect);
                exit;
            elseif (!$gateway = $this->shop->gateways()->get($data['payment_method'])) :
                $this->shop->notices()->add(__('Désolé, le mode de paiement choisie n\'est pas valide dans cette boutique.',
                    'tify'), 'error');

                \wp_redirect($redirect);
                exit;
            endif;
        endif;

        /** @var  $order */
        $order = ($order_id = $this->shop->session()->get('order_awaiting_payment', 0))
            ? $this->shop->orders()->get($order_id)
            : $this->shop->orders()->create();

        if (!$this->shop->orders()->is($order)) :
            $this->shop->notices()->add(__('Désolé, impossible de procéder à votre commande, veuillez réessayer.',
                'tify'), 'error');

            \wp_redirect($redirect);
            exit;
        endif;

        $created_via = 'checkout';
        $cart_hash = md5(json_encode($this->shop->cart()->getList()) . $this->shop->cart()->getTotals());
        $customer_id = $this->shop->users()->get()->getId();
        $currency = $this->shop->settings()->currency();
        $prices_include_tax = $this->shop->settings()->isPricesIncludeTax();
        $customer_ip_address = $request->getClientIp();
        $customer_user_agent = $request->headers->get('User-Agent');
        $customer_note = isset($data['order_comments']) ? $data['order_comments'] : '';
        $payment_method_title = $gateway->getTitle();
        $shipping_total = $this->shop->cart()->getTotals()->getShippingTotal();
        $shipping_tax = $this->shop->cart()->getTotals()->getShippingTax();
        $discount_total = $this->shop->cart()->getTotals()->getDiscountTotal();
        $discount_tax = $this->shop->cart()->getTotals()->getDiscountTax();
        $cart_tax = $this->shop->cart()->getTotals()->getGlobalTax() + $this->shop->cart()->getTotals()->getFeeTax();
        $total = $this->shop->cart()->getTotals()->getGlobal();

        // Liste des articles du panier associés à la commande
        if ($lines = $this->shop->cart()->lines()) :
            foreach($lines as $line) :
                $product = $line->getProduct();
                $item = $order->createItemProduct($product);
                $item
                    ->set('quantity', $line->getQuantity())
                    ->set('variation', '')
                    ->set('subtotal', $line->getSubtotal())
                    ->set('subtotal_tax', $line->getSubtotalTax())
                    ->set('total_tax', $line->getTax())
                    ->set('taxes', [])
                    ->set('name', $product->getTitle())
                    ->set('tax_class', '')
                    ->set('product_id', $product->getId())
                    ->set('variation_id', 0);

                $order->addItem($item);
            endforeach;
        endif;

        // Liste des promotions associées à la commande
        // @todo

        // Liste des livraisons associées à la commande
        // @todo

        // Liste des taxes associées à la commandes
        // @todo

        // Liste des coupons de réduction associé à la commande
        // @todo

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
        $order->create();

        $order->save();
        var_dump($this->shop->cart()->needPayment());
        exit;

        \wp_redirect($redirect);
        exit;

        /*
        $user = $this->shop->users()->get();
        if (!$user->isCustomer()) :
            return;
        endif;

        var_dump($this->shop->users()->get()->isCustomer());
        */
    }
}