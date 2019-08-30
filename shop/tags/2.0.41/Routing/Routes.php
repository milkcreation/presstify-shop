<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop\Routing;

use tiFy\Plugins\Shop\{
    Api\Middleware as ApiMiddleware,
    Api\Strategy as ApiStrategy,
    Concerns\ShopAwareTrait,
    Contracts\Routes as RoutesContract
};
use tiFy\Contracts\Routing\RouteGroup;
use tiFy\Support\Proxy\Router;
use Zend\Diactoros\ResponseFactory;

class Routes implements RoutesContract
{
    use ShopAwareTrait;

    /**
     * @inheritDoc
     */
    public function boot(): void
    {
        $prefix = '/';
        if (is_multisite()) {
            $prefix = get_blog_details()->path !== '/'
                ? rtrim(preg_replace('#^' . url()->rewriteBase() . '#', '', get_blog_details()->path), '/') . '/'
                : '/';
        }

        // API
        Router::group("{$prefix}shop/api", function (RouteGroup $router) {
            // Racine - Documentation
            $router->get('/', [$this->shop->resolve('api'), 'rootEndpoint']);

            // Commandes
            $router->get('/orders[/{id:number}]', [$this->shop->resolve('api.orders'), 'endpointGet']);
            $router->post('/orders[/{id:number}]', [$this->shop->resolve('api.orders'), 'endpointPost']);
        })
            ->setStrategy(new ApiStrategy(new ResponseFactory()))
            ->middleware(new ApiMiddleware());

        // PANIER
        // Ajout d'un article au panier
        Router::post("{$prefix}ajouter-au-panier/{product_name}", [$this->shop->cart(), 'addHandler'])
            ->setName('shop.cart.add');

        // Mise à jour des articles du panier
        Router::post("{$prefix}mise-a-jour-du-panier", [$this->shop->cart(), 'updateHandler'])
            ->setName('shop.cart.update');

        // Suppression d'un article du panier
        Router::get("{$prefix}supprimer-du-panier/{line_key}", [$this->shop->cart(), 'removeHandler'])
            ->setName('shop.cart.remove');

        // PAIEMENT
        // Traitement de la commande
        Router::post("{$prefix}shop/checkout/process", [$this->shop->checkout(), 'process'])
            ->setName('shop.checkout.process');

        // COMMANDE
        // Validation de paiement
        Router::post(
            "{$prefix}shop/order/payment_complete/{order_id:number}",
            [$this->shop->orders(), 'handlePaymentComplete']
        )->setName('shop.order.payment_complete');
    }
}