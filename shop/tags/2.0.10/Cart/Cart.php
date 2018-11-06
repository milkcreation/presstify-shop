<?php

/**
 * @name Cart
 * @desc Gestion du panier d'achat.
 *
 * @author Jordy Manner <jordy@tigreblanc.fr>
 * @copyright Milkcreation
 */

namespace tiFy\Plugins\Shop\Cart;

use Illuminate\Support\Arr;
use LogicException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;
use tiFy\Route\Route;
use tiFy\Plugins\Shop\AbstractShopSingleton;
use tiFy\Plugins\Shop\Contracts\CartInterface;
use tiFy\Plugins\Shop\Contracts\CartLineInterface;
use tiFy\Plugins\Shop\Contracts\CartLineListInterface;
use tiFy\Plugins\Shop\Contracts\CartSessionItemsInterface;
use tiFy\Plugins\Shop\Contracts\ProductItemInterface;

class Cart extends AbstractShopSingleton implements CartInterface
{
    /**
     * Instance de la liste des lignes du panier.
     * @var CartLineInterface[]|CartLineListInterface
     */
    protected $lines;

    /**
     * Liste des messages de notification.
     * @var array
     */
    protected $notices = [];

    /**
     * Instance de gestion des données du panier enregistré en session.
     * @var CartSessionItemsInterface
     */
    protected $sessionItems;

    /**
     * Instance de calcul des totaux.
     * @var Total
     */
    protected $totals;

    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        add_action(
            'after_setup_tify',
            function () {
                $this->sessionItems();
                $this->initNotices();
            }
        );

        add_action(
            'tify_route_register',
            function($routeController) {
                /** @var Route $routeController */

                // Ajout d'un article au panier
                $routeController->register(
                    'tify.plugins.shop.cart.add',
                    [
                        'method' => 'post',
                        'path'   => '/ajouter-au-panier/{product_name}',
                        'cb'     => function (
                            $product_name,
                            ServerRequestInterface $psrRequest,
                            ResponseInterface $psrResponse
                        ) {
                            add_action(
                                'wp_loaded',
                                function () use ($product_name, $psrRequest, $psrResponse) {
                                    call_user_func_array([$this, 'addHandler'], [$product_name, $psrRequest, $psrResponse]);
                                },
                                20
                            );
                        }
                    ]
                );

                // Mise à jour des articles du panier
                $routeController->register(
                    'tify.plugins.shop.cart.update',
                    [
                        'method' => 'post',
                        'path'   => '/mise-a-jour-du-panier',
                        'cb'     => function (ServerRequestInterface $psrRequest, ResponseInterface $psrResponse) {
                            add_action(
                                'wp_loaded',
                                function () use ($psrRequest, $psrResponse) {
                                    call_user_func_array([$this, 'updateHandler'], [$psrRequest, $psrResponse]);
                                },
                                20
                            );
                        }
                    ]
                );

                // Suppression d'un article du panier
                $routeController->register(
                    'tify.plugins.shop.cart.remove',
                    [
                        'method' => ['get', 'post'],
                        'path'   => '/supprimer-du-panier/{line_key}',
                        'cb'     => function ($line_key, ServerRequestInterface $psrRequest, ResponseInterface $psrResponse) {
                            add_action(
                                'wp_loaded',
                                function () use ($line_key, $psrRequest, $psrResponse) {
                                    call_user_func_array([$this, 'removeHandler'], [$line_key, $psrRequest, $psrResponse]);
                                },
                                20
                            );
                        }
                    ]
                );
            },
            0
        );

        add_action(
            'init',
            function() {
                $this->sessionItems()->getCart();
            },
            999999
        );

        add_action(
            'wp_loaded',
            function () {
                if ($this->functions()->page()->isCart() && ! $this->getList() && ($message = $this->getNotice('is_empty'))) :
                    $this->notices()->add(
                        $message,
                        'info'
                    );
                endif;
            }
        );
    }

    /**
     * {@inheritdoc}
     */
    public function add($key, $attributes)
    {
        $this->lines()->put(
            $key,
            app(
                'shop.cart.line',
                [$attributes, $this, $this->shop]
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function addHandler($product_name, ServerRequestInterface $psrRequest, ResponseInterface $psrResponse)
    {
        /**
         * Vérification d'existance du produit et récupération
         * @var \tiFy\Plugins\Shop\Products\ProductItem $product
         */
        if (!$product = $this->products()->getItem($product_name)) :
            return;
        endif;

        /**
         * Conversion de la requête PSR-7
         * @see https://symfony.com/doc/current/components/psr7.html
         * @var \Symfony\Component\HttpFoundation\Request $request
         */
        $request = (new HttpFoundationFactory())->createRequest($psrRequest);

        // Récupération de la quantité de produit
        if (!$quantity = $request->request->getInt('quantity', 1)) :
            return;
        endif;

        // Vérifie si un produit peut être commandé
        if (!$product->isPurchasable()) :
            return;
        endif;

        // Options d'achat
        $purchasing_options = $request->request->get('purchasing_options', []);

        // Identification de la ligne du panier (doit contenir toutes les options d'unicité).
        $key = md5(
            implode(
                '_',
                [
                    $product->getid(),
                    maybe_serialize($purchasing_options)
                ]
            )
        );
        if ($exists = $this->get($key)) :
            $quantity += $exists->getQuantity();
        endif;

        $this->add(
            $key,
            compact(
                'key',
                'quantity',
                'product',
                'purchasing_options'
            )
        );

        // Mise à jour des données de session
        $this->sessionItems()->update();

        // Message de notification
        if ($message = $this->getNotice('successfully_added')) :
            $this->notices()->add($message);
        endif;

        // Définition de l'url de redirection
        if ($redirect = $request->request->get('_wp_http_referer', '')) :
        elseif ($redirect = $product->getPermalink()) :
        else :
            $redirect = wp_get_referer();
        endif;

        wp_redirect(($redirect ?: get_home_url()));
        exit;
    }

    /**
     * {@inheritdoc}
     */
    public function addUrl($product)
    {
        if (!$product instanceof ProductItemInterface) :
            $product = $this->products()->getItem($product);
        elseif ($product instanceof ProductItemInterface) :
            /** @var Route $route */
            $route = app(Route::class);

            return $route->url('tify.plugins.shop.cart.add', [$product->getSlug()]);
        else :
            return '';
        endif;
    }

    /**
     * {@inheritdoc}
     */
    public function calculate()
    {
        return $this->totals = app('shop.cart.total', [$this, $this->shop]);
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return $this->lines()->count();
    }

    /**
     * {@inheritdoc}
     */
    public function countQuantity()
    {
        return $this->lines()->sum('quantity');
    }

    /**
     * {@inheritdoc}
     */
    public function destroy()
    {
        $this->flush();
        $this->calculate();
        $this->sessionItems()->destroy();
    }

    /**
     * {@inheritdoc}
     */
    public function flush()
    {
        $this->lines = app('shop.cart.line_list', [[]]);
    }

    /**
     * {@inheritdoc}
     */
    public function get($key)
    {
        return $this->lines()->get($key);
    }

    /**
     * {@inheritdoc}
     */
    public function getProductsWeight()
    {
        return $this->lines()->sum(
            function (CartLineInterface $item) {
                return (float)$item->getProduct()->getWeight() * $item->getQuantity();
            }
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getList()
    {
        return $this->lines()->all();
    }

    /**
     * {@inheritdoc}
     */
    public function getNotice($name, $default = '')
    {
        return Arr::get($this->notices, $name, $default);
    }

    /**
     * {@inheritdoc}
     */
    public function getTotals()
    {
        return $this->totals;
    }

    /**
     * {@inheritdoc}
     */
    public function initNotices()
    {
        $this->notices = array_merge(
            [
                'successfully_added'   => __('L\'article a été ajouté à votre panier avec succès.', 'tify'),
                'successfully_updated' => __('Votre panier a été mis à jour avec succès.', 'tify'),
                'successfully_removed' => __('L\'article a été supprimé de votre panier avec succès.', 'tify'),
                'is_empty'             => __('Votre panier ne contient actuellement aucun article.', 'tify')
            ],
            $this->config('cart.notices', [])
        );
    }

    /**
     * {@inheritdoc}
     */
    public function isEmpty()
    {
        return $this->lines()->isEmpty();
    }

    /**
     * {@inheritdoc}
     */
    public function lines()
    {
        if (is_null($this->lines)) :
            $this->lines = app('shop.cart.line_list', [[]]);
        endif;

        return $this->lines;
    }

    /**
     * {@inheritdoc}
     */
    public function needPayment()
    {
        return $this->totals->getGlobal() > 0;
    }

    /**
     * {@inheritdoc}
     */
    public function needShipping()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function remove($key)
    {
        return $this->lines()->pull($key);
    }

    /**
     * {@inheritdoc}
     */
    public function removeHandler($key, ServerRequestInterface $psrRequest, ResponseInterface $psrResponse)
    {
        /**
         * Conversion de la requête PSR-7
         * @see https://symfony.com/doc/current/components/psr7.html
         * @var \Symfony\Component\HttpFoundation\Request $request
         */
        $request = (new HttpFoundationFactory())->createRequest($psrRequest);

        if ($this->remove($key)) :
            // Mise à jour des données de session
            $this->sessionItems()->update();

            // Message de notification
            if ($message = $this->getNotice('successfully_removed')) :
                $this->notices()->add($message);
            endif;
        endif;

        // Définition de l'url de redirection
        if ($redirect = $request->get('_wp_http_referer', '')) :
        elseif ($redirect = $this->functions()->url()->cartPage()) :
        else :
            $redirect = wp_get_referer();
        endif;

        wp_redirect(($redirect ?: get_home_url()));
        exit;
    }

    /**
     * {@inheritdoc}
     */
    public function removeUrl($key)
    {
        /** @var Route $route */
        $route = app(Route::class);

        return $route->url('tify.plugins.shop.cart.remove', [$key]);
    }

    /**
     * {@inheritdoc}
     */
    public function sessionItems()
    {
        return app('shop.cart.session_items', [$this, $this->shop]);
    }

    /**
     * {@inheritdoc}
     */
    public function update($key, $attributes)
    {
        if ($line = $this->get($key)) :
            foreach ($attributes as $key => $value) :
                $line[$key] = $value;
            endforeach;

            $this->lines()->merge([$key => $line]);
        endif;

        return $this->lines();
    }

    /**
     * {@inheritdoc}
     */
    public function updateUrl()
    {
        /** @var Route $route */
        $route = app(Route::class);

        return $route->url('tify.plugins.shop.cart.update');
    }

    /**
     * {@inheritdoc}
     */
    public function updateHandler(ServerRequestInterface $psrRequest, ResponseInterface $psrResponse)
    {
        /**
         * Conversion de la requête PSR-7
         * @see https://symfony.com/doc/current/components/psr7.html
         * @var \Symfony\Component\HttpFoundation\Request $request
         */
        $request = (new HttpFoundationFactory())->createRequest($psrRequest);

        if ($lines = $request->request->get('cart')) :
            foreach ($lines as $key => $attributes) :
                $this->update($key, $attributes);
            endforeach;

            // Mise à jour des données de session
            $this->sessionItems()->update();

            // Message de notification
            if ($message = $this->getNotice('successfully_updated')) :
                $this->notices()->add($message);
            endif;
        endif;

        // Définition de l'url de redirection
        if ($redirect = $request->request->get('_wp_http_referer', '')) :
        elseif ($redirect = $this->functions()->url()->cartPage()) :
        else :
            $redirect = wp_get_referer();
        endif;

        wp_redirect(($redirect ?: get_home_url()));
        exit;
    }
}