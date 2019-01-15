<?php

namespace tiFy\Plugins\Shop\Api;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use tiFy\Kernel\Http\Request;
use Zend\Diactoros\Response;

class RouteMiddleware implements MiddlewareInterface
{
    /**
     * Vérification de l'authentification.
     *
     * @param Request $request
     *
     * @return bool
     */
    protected function isAuth($request)
    {
        return in_array($request->get('authtoken'), config('shop.api.authtoken', []));
    }

    /**
     * {@inheritdoc}
     */
    public function process(ServerRequestInterface $psrRequest, RequestHandlerInterface $handler) : ResponseInterface
    {
        $request = request()->createFromPsr($psrRequest);

        if (!$this->isAuth($request)) :
            $response = new Response();
            $response->getBody()->write(json_encode(['error' => 'Accès restreint, clé d\'autorisation invalide']));
            $response->withStatus(401);
        else :
            $response = $handler->handle($psrRequest);
        endif;

        return $response;
    }
}