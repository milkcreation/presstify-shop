<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop\Cart;

use Illuminate\Support\{Arr, Collection};
use tiFy\Plugins\Shop\Contracts\{Cart as CartContract, CartLine, CartSession, CartTotal, Product, Shop};
use tiFy\Plugins\Shop\ShopAwareTrait;
use tiFy\Support\{MessagesBag, Proxy\Redirect, Proxy\Request, Proxy\Router};

class Cart implements CartContract
{
    use ShopAwareTrait;

    /**
     * Instances des lignes du panier.
     * @var CartLine[]|array
     */
    protected $lines = [];

    /**
     * Liste des messages de notification.
     * @var array
     */
    protected $notices = [];

    /**
     * Instance de gestion des données du panier enregistré en session.
     * @var CartSession
     */
    protected $session;

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
            $this->session();

            $this->notices = array_merge([
                'successfully_added'   => __('L\'article a été ajouté à votre panier avec succès.', 'tify'),
                'successfully_updated' => __('Votre panier a été mis à jour avec succès.', 'tify'),
                'successfully_removed' => __('L\'article a été supprimé de votre panier avec succès.', 'tify'),
                'empty'                => __('Votre panier ne contient actuellement aucun article.', 'tify'),
            ], $this->shop()->config('cart.notices', []));
        }, 25);

        add_action('init', function () {
            $this->session()->fetchCart();
        }, 999999);
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
        $this->lines[$key] = $this->shop()->resolve('cart.line', [$this])->set($attributes)->parse();

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function all(): array
    {
        return $this->lines;
    }

    /**
     * @inheritDoc
     */
    public function addUrl($product): string
    {
        if (!$product instanceof Product) {
            $product = $this->shop()->product($product);
        }

        return ($product instanceof Product) ? Router::url('shop.cart.add', [$product->getSlug()]) : '';
    }

    /**
     * @inheritDoc
     */
    public function calculate(): CartTotal
    {
        return $this->total = $this->shop()->resolve('cart.total', [$this]);
    }

    /**
     * @inheritDoc
     */
    public function count(): int
    {
        return $this->collect()->count();
    }

    /**
     * @inheritDoc
     */
    public function collect(): Collection
    {
        return new Collection($this->all());
    }

    /**
     * @inheritDoc
     */
    public function destroy(): CartContract
    {
        $this->flush()->calculate();

        $this->session()->destroy();

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function flush(): CartContract
    {
        $this->lines = [];

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function get(string $key): ?CartLine
    {
        return $this->lines[$key] ?? null;
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
    public function handleAdd(string $product_name)
    {
        $notices = new MessagesBag();
        $redirect = Request::header('referer', get_home_url());

        if (!$product = $this->shop()->product($product_name)) {
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
            $this->session()->update();

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

        return $redirect ? Redirect::to($redirect) : null;
    }

    /**
     * @inheritDoc
     */
    public function handleUpdate()
    {
        if ($lines = Request::input('cart', [])) {
            foreach ($lines as $key => $attributes) {
                if (!$attributes['quantity'] ?? 0) {
                    $this->remove($key);
                } else {
                    $this->update($key, $attributes);
                }
            }

            $this->session()->update();

            if ($message = $this->getNotice('successfully_updated')) {
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
    public function handleRemove(string $key)
    {
        if ($this->remove($key)) {
            $this->session()->update();

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
    public function isEmpty(): bool
    {
        return empty($this->all());
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
    public function quantity(): int
    {
        return $this->collect()->sum(function (CartLine $item) {
            return is_numeric($item['quantity']) ? $item['quantity'] : 0;
        });
    }

    /**
     * @inheritDoc
     */
    public function remove(string $key)
    {
        unset($this->lines[$key]);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function session(): CartSession
    {
        return $this->shop()->resolve('cart.session', [$this]);
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
    public function update(string $key, array $attributes): CartContract
    {
        if ($line = $this->get($key)) {
            foreach ($attributes as $k => $v) {
                $line[$k] = $v;
            }

            $this->lines[$key] = $line;
        }

        return $this;
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
    public function weight(): float
    {
        return $this->collect()->sum(function (CartLine $item) {
            return (float)$item->getProduct()->getWeight() * $item->getQuantity();
        });
    }
}