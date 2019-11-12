<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop\Cart;

use Illuminate\Support\Arr;
use tiFy\Plugins\Shop\Contracts\{Cart as CartContract,
    CartLine,
    CartLinesCollection,
    CartSessionItems,
    CartTotal,
    Product,
    Shop
};
use tiFy\Plugins\Shop\ShopAwareTrait;
use tiFy\Support\{MessagesBag, Proxy\Redirect, Proxy\Request, Proxy\Router};

class Cart implements CartContract
{
    use ShopAwareTrait;

    /**
     * Instance de la liste des lignes du panier.
     * @var CartLinesCollection|CartLine[]
     */
    protected $lines;

    /**
     * Liste des messages de notification.
     * @var array
     */
    protected $notices = [];

    /**
     * Instance de gestion des données du panier enregistré en session.
     * @var CartSessionItems
     */
    protected $sessionItems;

    /**
     * Instance de calcul des totaux.
     * @var Total
     */
    protected $total;

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
                !$this->lines()->count() &&
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
    public function add(string $key, array $attributes): CartContract
    {
        $this->lines()->put($key, $this->shop()->resolve('cart.line')->set($attributes)->parse());

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function addHandler($product_name)
    {
        $notices = new MessagesBag();
        $redirect = Request::header('referer', get_home_url());

        if (!$product = $this->shop()->products()->get($product_name)) {
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
            if ($exists = $this->line($key)) {
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
                $this->shop()->notices()->add($notice['message'], $notices::getLevelName($notice['level']));
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
    public function addUrl($product): string
    {
        if (!$product instanceof Product) {
            $product = $this->shop()->products()->getItem($product);
        }

        return ($product instanceof Product) ? Router::url('shop.cart.add', [$product->getSlug()]) : '';
    }

    /**
     * @inheritDoc
     */
    public function calculate(): CartTotal
    {
        return $this->total = $this->shop()->resolve('cart.total');
    }

    /**
     * @inheritDoc
     */
    public function count(): int
    {
        return $this->lines()->count();
    }

    /**
     * @inheritDoc
     */
    public function countQuantity(): int
    {
        return $this->lines()->sum(function (CartLine $item) {
            return is_numeric($item['quantity']) ? $item['quantity'] : 0;
        });
    }

    /**
     * @inheritDoc
     */
    public function destroy(): void
    {
        $this->flush();
        $this->calculate();
        $this->sessionItems()->destroy();
    }

    /**
     * @inheritDoc
     */
    public function flush(): void
    {
        $this->lines = $this->shop()->resolve('cart.lines.collection')->flush();
    }

    /**
     * @inheritDoc
     */
    public function getProductsWeight(): float
    {
        return $this->lines()->sum(function (CartLine $item) {
            return (float)$item->getProduct()->getWeight() * $item->getQuantity();
        });
    }

    /**
     * @inheritDoc
     */
    public function getNotice(string $name, string $default = ''): string
    {
        return Arr::get($this->notices, $name, $default);
    }

    /**
     * @inheritDoc
     */
    public function initNotices(): void
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
    public function isEmpty(): bool
    {
        return $this->lines()->isEmpty();
    }

    /**
     * @inheritDoc
     */
    public function line($key): ?CartLine
    {
        return $this->lines()->get($key);
    }

    /**
     * @inheritDoc
     */
    public function lines(): CartLinesCollection
    {
        if (is_null($this->lines)) {
            $this->lines = $this->shop()->resolve('cart.lines.collection')->flush();
        }

        return $this->lines;
    }

    /**
     * @inheritDoc
     */
    public function needPayment(): bool
    {
        return $this->total()->getGlobal() > 0;
    }

    /**
     * @inheritDoc
     */
    public function needShipping(): bool
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function remove(string $key)
    {
        return $this->lines()->pull($key);
    }

    /**
     * @inheritDoc
     */
    public function removeHandler(string $key)
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
    public function removeUrl(string $key): string
    {
        return Router::url('shop.cart.remove', [$key]);
    }

    /**
     * @inheritDoc
     */
    public function sessionItems(): CartSessionItems
    {
        return $this->shop()->resolve('cart.session-items', [$this, $this->shop]);
    }

    /**
     * @inheritDoc
     */
    public function total(): ?CartTotal
    {
        return $this->total;
    }

    /**
     * @inheritDoc
     */
    public function update(string $key, array $attributes): CartLinesCollection
    {
        if ($line = $this->line($key)) {
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
    public function updateUrl(): string
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