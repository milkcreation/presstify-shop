<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop\Cart;

use Illuminate\Support\Arr;
use tiFy\Plugins\Shop\{
    AbstractShopSingleton,
    Contracts\CartInterface,
    Contracts\CartLineInterface,
    Contracts\CartLineListInterface,
    Contracts\CartSessionItemsInterface,
    Contracts\ProductItemInterface
};
use tiFy\Support\{MessagesBag, Proxy\Redirect, Proxy\Request};

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
        add_action('after_setup_theme', function () {
            $this->sessionItems();
            $this->initNotices();

            // Ajout d'un article au panier
            router('shop.cart.add', [
                'method' => 'POST',
                'path'   => '/ajouter-au-panier/{product_name}',
                'cb'     => [$this, 'addHandler'],
            ]);

            // Mise à jour des articles du panier
            router('shop.cart.update', [
                'method' => 'POST',
                'path'   => '/mise-a-jour-du-panier',
                'cb'     => [$this, 'updateHandler'],
            ]);

            // Suppression d'un article du panier
            router('shop.cart.remove', [
                'method' => 'GET',
                'path'   => '/supprimer-du-panier/{line_key}',
                'cb'     => [$this, 'removeHandler'],
            ]);
        }, 25);

        add_action('init', function () {
            $this->sessionItems()->getCart();
        }, 999999);

        add_action('get_header', function () {
            if (
                $this->functions()->page()->isCart() &&
                !$this->getList() &&
                ($message = $this->getNotice('is_empty'))
            ) {
                $this->notices()->add($message, 'info');
            }
        });
    }

    /**
     * @inheritDoc
     */
    public function add($key, $attributes)
    {
        $this->lines()->put($key, app('shop.cart.line', [$attributes, $this, $this->shop]));
    }

    /**
     * @inheritDoc
     */
    public function addHandler($product_name)
    {
        $notices = new MessagesBag();
        $redirect = Request::header('referer', get_home_url());

        if (!$product = $this->products()->getItem($product_name)) {
            // > Produit inexistant.
            $notices->error(__('Le produit n\'existe pas.', 'tify'));
        } elseif (!$quantity = Request::instance()->request->getInt('quantity', 1)) {
            // > Impossible de définir la quantité de produit.
            $notices->error(__('La quantité de produit ne peut être définie.', 'tify'));
        } elseif (!$product->isPurchasable()) {
            // > Le produit n'est pas commandable.
            $notices->error(__('Le produit ne peut être commandé.', 'tify'));
        } else {
            // Options d'achat
            $purchasing_options = Request::input('purchasing_options', []);

            // Identification de la ligne du panier (doit contenir toutes les options d'unicité).
            $key = md5(implode('_', [$product->getid(), maybe_serialize($purchasing_options)]));
            if ($exists = $this->get($key)) {
                $quantity += $exists->getQuantity();
            }

            $this->add($key, compact(
                'key',
                'quantity',
                'product',
                'purchasing_options'
            ));

            // Mise à jour des données de session
            $this->sessionItems()->update();

            $notices->success(
                $this->getNotice('successfully_added')
                    ?: __('Le produit a été ajouté au panier avec succès', 'tify')
            );

            // Définition de l'url de redirection
            if ($redirect = Request::input('_wp_http_referer', '')) {
            } elseif ($redirect = $product->getPermalink()) {
            } else {
                $redirect = Request::header('referer', get_home_url());
            }
        }

        if ($notices->exists()) {
            foreach ($notices->fetch() as $notice) {
                $this->shop->session()->notices()->add($notice['message'], $notices::getLevelName($notice['level']));
            }
        }

        if ($redirect) {
            return Redirect::to($redirect);
        } else {
            return null;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function addUrl($product)
    {
        if (!$product instanceof ProductItemInterface) :
            $product = $this->products()->getItem($product);
        endif;

        return ($product instanceof ProductItemInterface)
            ? route('shop.cart.add', [$product->getSlug()])
            : '';
    }

    /**
     * @inheritDoc
     */
    public function calculate()
    {
        return $this->totals = app('shop.cart.total', [$this, $this->shop]);
    }

    /**
     * @inheritDoc
     */
    public function count()
    {
        return $this->lines()->count();
    }

    /**
     * @inheritDoc
     */
    public function countQuantity()
    {
        return $this->lines()->sum('quantity');
    }

    /**
     * @inheritDoc
     */
    public function destroy()
    {
        $this->flush();
        $this->calculate();
        $this->sessionItems()->destroy();
    }

    /**
     * @inheritDoc
     */
    public function flush()
    {
        $this->lines = app('shop.cart.line_list', [[]]);
    }

    /**
     * @inheritDoc
     */
    public function get($key)
    {
        return $this->lines()->get($key);
    }

    /**
     * @inheritDoc
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
     * @inheritDoc
     */
    public function getList()
    {
        return $this->lines()->all();
    }

    /**
     * @inheritDoc
     */
    public function getNotice($name, $default = '')
    {
        return Arr::get($this->notices, $name, $default);
    }

    /**
     * @inheritDoc
     */
    public function getTotals()
    {
        return $this->totals;
    }

    /**
     * @inheritDoc
     */
    public function initNotices()
    {
        $this->notices = array_merge(
            [
                'successfully_added'   => __('L\'article a été ajouté à votre panier avec succès.', 'tify'),
                'successfully_updated' => __('Votre panier a été mis à jour avec succès.', 'tify'),
                'successfully_removed' => __('L\'article a été supprimé de votre panier avec succès.', 'tify'),
                'is_empty'             => __('Votre panier ne contient actuellement aucun article.', 'tify'),
            ],
            $this->config('cart.notices', [])
        );
    }

    /**
     * @inheritDoc
     */
    public function isEmpty()
    {
        return $this->lines()->isEmpty();
    }

    /**
     * @inheritDoc
     */
    public function lines()
    {
        if (is_null($this->lines)) {
            $this->lines = app('shop.cart.line_list', [[]]);
        }
        return $this->lines;
    }

    /**
     * @inheritDoc
     */
    public function needPayment()
    {
        return $this->totals->getGlobal() > 0;
    }

    /**
     * @inheritDoc
     */
    public function needShipping()
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function remove($key)
    {
        return $this->lines()->pull($key);
    }

    /**
     * @inheritDoc
     */
    public function removeHandler($key)
    {
        /**
         * Conversion de la requête PSR-7
         * @see https://symfony.com/doc/current/components/psr7.html
         * @var \Symfony\Component\HttpFoundation\Request $request
         */
        $request = request();

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
     * @inheritDoc
     */
    public function removeUrl($key)
    {
        return route('shop.cart.remove', [$key]);
    }

    /**
     * @inheritDoc
     */
    public function sessionItems()
    {
        return app('shop.cart.session_items', [$this, $this->shop]);
    }

    /**
     * @inheritDoc
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
     * @inheritDoc
     */
    public function updateUrl()
    {
        return route('shop.cart.update');
    }

    /**
     * @inheritDoc
     */
    public function updateHandler()
    {
        $request = request();

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