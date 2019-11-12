<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop\Routing;

use tiFy\Plugins\Shop\Contracts\{Routes as RoutesContract, Shop};
use tiFy\Plugins\Shop\{Api\Middleware as ApiMiddleware, Api\Strategy as ApiStrategy, ShopAwareTrait};
use tiFy\Contracts\Routing\RouteGroup;
use tiFy\Support\Proxy\Router;
use Zend\Diactoros\ResponseFactory;

class Routes implements RoutesContract
{
    use ShopAwareTrait;

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

        // API
        Router::group('shop/api', function (RouteGroup $router) {
            // Racine - Documentation
            $router->get('/', [$this->shop->resolve('api'), 'rootEndpoint']);

            // Commandes
            $router->get('/orders[/{id:number}]', [$this->shop->resolve('api.endpoint.orders'), 'endpointGet']);
            $router->post('/orders[/{id:number}]', [$this->shop->resolve('api.endpoint.orders'), 'endpointPost']);
        })
            ->setStrategy(new ApiStrategy(new ResponseFactory()))
            ->middleware(new ApiMiddleware());

        // PANIER
        // Ajout d'un article au panier
        Router::post('ajouter-au-panier/{product_name}', [$this->shop->cart(), 'addHandler'])->setName('shop.cart.add');

        // Mise à jour des articles du panier
        Router::post('mise-a-jour-du-panier', [$this->shop->cart(), 'updateHandler'])->setName('shop.cart.update');

        // Suppression d'un article du panier
        Router::get('supprimer-du-panier/{line_key}', [$this->shop->cart(), 'removeHandler'])
            ->setName('shop.cart.remove');

        // PAIEMENT
        // Traitement de la commande
        Router::post('shop/checkout/process', [$this->shop->checkout(), 'process'])->setName('shop.checkout.process');

        // COMMANDE
        // Validation de paiement
        Router::post('shop/order/payment_complete/{order_id:number}', [$this->shop->orders(), 'handlePaymentComplete'])
            ->setName('shop.order.payment_complete');

        $this->boot();
    }

    /**
     * @inheritDoc
     */
    public function boot(): void { }
}