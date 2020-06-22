<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop\Controller;

use tiFy\Routing\BaseController;
use tiFy\Plugins\Shop\{
    Contracts\CartController as CartControllerContract,
    ShopAwareTrait
};
use tiFy\Contracts\Http\Response;
use tiFy\Support\{Arr, MessagesBag};
use tiFy\Support\Proxy\{Redirect, Request, Url};

class CartController extends BaseController implements CartControllerContract
{
    use ShopAwareTrait;

    /**
     * @inheritDoc
     */
    public function add(string $product_name): Response
    {
        $notices = new MessagesBag();
        $redirect = Request::header('referer', Url::root()->render());

        if (!$product = $this->shop->product($product_name)) {
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
            $key = md5(implode('_', [$product->getId(), Arr::serialize($purchasing_options)]));
            if ($exists = $this->shop->cart()->get($key)) {
                $quantity += $exists->getQuantity();
            }

            $this->shop->cart()->add($key, compact('key', 'quantity', 'product', 'purchasing_options'));

            // Mise à jour des données de session
            $this->shop->cart()->session()->update();

            $notices->success(
                $this->shop->cart()->getNotice('successfully_added')
                    ?: __('Le produit a été ajouté au panier avec succès', 'tify')
            );

            // Définition de l'url de redirection
            if (!$redirect = Request::input('_wp_http_referer', '') ?: $product->getPermalink()) {
                $redirect = Request::header('referer', Url::root()->render());
            }
        }

        if ($notices->exists()) {
            foreach ($notices->fetch() as $notice) {
                $this->shop->notices()->add($notice['message'], $notices::getLevelName($notice['level']));
            }
        }

        return Redirect::to($redirect);
    }

    /**
     * @inheritDoc
     */
    public function delete(string $line_key): Response
    {
        if ($this->shop->cart()->remove($line_key)) {
            $this->shop->cart()->session()->update();

            if ($message = $this->shop->cart()->getNotice('successfully_removed')) {
                $this->shop->notices()->add($message);
            }
        }

        if (!$redirect = Request::input('_wp_http_referer', '') ?: $this->shop->functions()->page()->cartPageUrl()) {
            $redirect = Request::header('referer', Url::root()->render());
        }

        return Redirect::to($redirect);
    }

    /**
     * @inheritDoc
     */
    public function update(): Response
    {
        if ($lines = Request::input('cart', [])) {
            foreach ($lines as $key => $attributes) {
                if (!$attributes['quantity'] ?? 0) {
                    $this->shop->cart()->remove($key);
                } else {
                    $this->shop->cart()->update($key, $attributes);
                }
            }

            $this->shop->cart()->session()->update();

            if ($message = $this->shop->cart()->getNotice('successfully_updated')) {
                $this->shop->notices()->add($message);
            }
        }

        if (!$redirect = Request::input('_wp_http_referer', '') ?: $this->shop->functions()->page()->cartPageUrl()) {
            $redirect = Request::header('referer', Url::root()->render());
        }

        return Redirect::to($redirect);
    }
}