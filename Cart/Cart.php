<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop\Cart;

use Illuminate\Support\Arr;
use tiFy\Plugins\Shop\Contracts\{
    CartInterface as CartContract,
    CartLineInterface as CartLineContract,
    CartLineListInterface as CartLineListContract,
    CartSessionItemsInterface as CartSessionItemsContract,
    ProductItemInterface as ProductItemContract,
    ShopInterface as Shop
};
use tiFy\Plugins\Shop\ShopAwareTrait;
use tiFy\Support\{MessagesBag, Proxy\Redirect, Proxy\Request, Proxy\Router};

class Cart implements CartContract
{
    use ShopAwareTrait;

    /**
     * Instance de la liste des lignes du panier.
     * @var CartLineContract[]|CartLineListContract
     */
    protected $lines;

    /**
     * Liste des messages de notification.
     * @var array
     */
    protected $notices = [];

    /**
     * Instance de gestion des données du panier enregistré en session.
     * @var CartSessionItemsContract
     */
    protected $sessionItems;

    /**
     * Instance de calcul des totaux.
     * @var Total
     */
    protected $totals;

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

        add_action('after_setup_theme', function () {
            $this->sessionItems();
            $this->initNotices();
        }, 25);

        add_action('init', function () {
            $this->sessionItems()->fetchCart();
        }, 999999);

        add_action('get_header', function () {
            if (
                $this->shop()->functions()->page()->isCart() &&
                !$this->getList() &&
                ($message = $this->getNotice('is_empty'))
            ) {
                $this->shop()->notices()->add($message, 'info');
            }
        });
    }

    /**
     * @inheritDoc
     */
    public function boot(): void { }

    /**
     * @inheritDoc
     */
    public function add($key, $attributes)
    {
        $this->lines()->put($key, $this->shop()->resolve('cart.line')->set($attributes)->parse());
    }

    /**
     * @inheritDoc
     */
    public function addHandler($product_name)
    {
        $notices = new MessagesBag();
        $redirect = Request::header('referer', get_home_url());

        if (!$product = $this->shop()->products()->getItem($product_name)) {
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
                $this->shop()->session()->notices()->add($notice['message'], $notices::getLevelName($notice['level']));
            }
        }

        if ($redirect) {
            return Redirect::to($redirect);
        } else {
            return null;
        }
    }

    /**
     * @inheritDoc
     */
    public function addUrl($product)
    {
        if (!$product instanceof ProductItemContract) {
            $product = $this->shop()->products()->getItem($product);
        }

        return ($product instanceof ProductItemContract) ? Router::url('shop.cart.add', [$product->getSlug()]) : '';
    }

    /**
     * @inheritDoc
     */
    public function calculate()
    {
        return $this->totals = $this->shop()->resolve('cart.total');
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
        return $this->lines()->sum(function (CartLineContract $item) {
            return is_numeric($item['quantity']) ? $item['quantity'] :0;
        });
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
        $this->lines = $this->shop()->resolve('cart.line-list')->flush();
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
        return $this->lines()->sum(function (CartLineContract $item) {
            return (float)$item->getProduct()->getWeight() * $item->getQuantity();
        });
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
        $this->notices = array_merge([
            'successfully_added'   => __('L\'article a été ajouté à votre panier avec succès.', 'tify'),
            'successfully_updated' => __('Votre panier a été mis à jour avec succès.', 'tify'),
            'successfully_removed' => __('L\'article a été supprimé de votre panier avec succès.', 'tify'),
            'is_empty'             => __('Votre panier ne contient actuellement aucun article.', 'tify'),
        ], $this->shop()->config('cart.notices', []));
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
            $this->lines = $this->shop()->resolve('cart.line-list')->flush();
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
        if ($this->remove($key)) {
            $this->sessionItems()->update();

            if ($message = $this->getNotice('successfully_removed')) {
                $this->shop()->notices()->add($message);
            }
        }

        if ($redirect = Request::input('_wp_http_referer', '')) {
        } elseif ($redirect = $this->shop()->functions()->url()->cartPage()) {
        } else {
            $redirect = wp_get_referer();
        }

        wp_redirect(($redirect ?: get_home_url()));
        exit;
    }

    /**
     * @inheritDoc
     */
    public function removeUrl($key)
    {
        return Router::url('shop.cart.remove', [$key]);
    }

    /**
     * @inheritDoc
     */
    public function sessionItems()
    {
        return $this->shop()->resolve('cart.session-items', [$this, $this->shop]);
    }

    /**
     * @inheritDoc
     */
    public function update($key, $attributes)
    {
        if ($line = $this->get($key)) {
            foreach ($attributes as $key => $value) {
                $line[$key] = $value;
            }

            $this->lines()->merge([$key => $line]);
        }

        return $this->lines();
    }

    /**
     * @inheritDoc
     */
    public function updateUrl()
    {
        return Router::url('shop.cart.update');
    }

    /**
     * @inheritDoc
     */
    public function updateHandler()
    {
        $request = request();

        if ($lines = $request->request->get('cart')) {
            foreach ($lines as $key => $attributes) {
                if (!$attributes['quantity'] ?? 0) {
                    $this->remove($key);
                } else {
                    $this->update($key, $attributes);
                }
            }

            $this->sessionItems()->update();

            if ($message = $this->getNotice('successfully_updated')) {
                $this->shop()->notices()->add($message);
            }
        }

        // Définition de l'url de redirection
        if ($redirect = $request->request->get('_wp_http_referer', '')) {
        } elseif ($redirect = $this->shop()->functions()->url()->cartPage()) {
        } else {
            $redirect = wp_get_referer();
        }

        wp_redirect(($redirect ?: get_home_url()));
        exit;
    }
}