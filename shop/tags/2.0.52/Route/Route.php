<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop\Route;

use tiFy\Contracts\Routing\RouteGroup;
use tiFy\Plugins\Shop\{
    Contracts\Route as RouteContract,
    ShopAwareTrait
};
use tiFy\Support\Proxy\Router;

class Route implements RouteContract
{
    use ShopAwareTrait;

    /**
     * Indicateur d'initialisation.
     * @var bool
     */
    protected $booted = false;

    /**
     * @inheritDoc
     */
    public function boot(): RouteContract
    {
        if (!$this->booted) {
            /** Déclaration des middlewares */
            Router::registerMiddleware('shop.api', $this->shop()->resolve('middleware.api'));
            /**/

            /** Déclaration des controleurs */
            Router::setControllerStack([
                'shop.api'      => $this->shop()->resolve('controller.api'),
                'shop.cart'     => $this->shop()->resolve('controller.cart'),
                'shop.checkout' => $this->shop()->resolve('controller.checkout'),
            ]);
            /**/

            /** Routes API */
            Router::group('shop/api', function (RouteGroup $router) {
                $router->get('/', ['shop.api', 'index']);

                $router->get('/orders[/{id:number}]', ['shop.api', 'order']);
                $router->post('/orders[/{id:number}]', ['shop.api', 'order']);
            })->strategy('api')->middleware('shop.api');
            /**/

            /** Routage */
            // Panier : Ajout d'un article.
            Router::post('ajouter-au-panier/{product_name}', ['shop.cart', 'add'])->setName('shop.cart.add');

            // Panier : Suppression d'un article.
            Router::get('supprimer-du-panier/{line_key}', ['shop.cart', 'delete'])->setName('shop.cart.delete');

            // Panier : Mise à jour des articles.
            Router::post('mise-a-jour-du-panier', ['shop.cart', 'update'])->setName('shop.cart.update');

            // Paiement : Traitement de la commande.
            // Traitement de la commande
            Router::post('shop/checkout/handle', ['shop.checkout', 'handle'])->setName('shop.checkout.handle');

            // COMMANDE
            // Validation de paiement
            /*Router::post('shop/order/payment_complete/{order_id:number}', [$shop->orders(), 'handlePaymentComplete'])
                ->setName('shop.order.payment_complete');*/

            $this->booted = true;
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function cartAddUrl(string $product_name): string
    {
        return Router::url('shop.cart.add', [$product_name]);
    }

    /**
     * @inheritDoc
     */
    public function cartDeleteUrl(string $line_key): string
    {
        return Router::url('shop.cart.delete', [$line_key]);
    }

    /**
     * @inheritDoc
     */
    public function cartUpdateUrl(): string
    {
        return Router::url('shop.cart.update');
    }

    /**
     * @inheritDoc
     */
    public function checkoutHandleUrl(): string
    {
        return Router::url('shop.checkout.handle');
    }
}