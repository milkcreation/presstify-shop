<?php

namespace tiFy\Plugins\Shop\Api;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use tiFy\Contracts\Http\Request;
use tiFy\Http\Response;

class RouteMiddleware implements MiddlewareInterface
{
    /**
     * Vérification de l'authentification.
     *
     * @param Request $request
     *
     * @return bool
     */
    protected function isAuth(Request $request)
    {
        return in_array($request->get('authtoken'), config('shop.api.authtoken', []));
    }

    /**
     * @inheritdoc
     */
    public function process(ServerRequestInterface $psrRequest, RequestHandlerInterface $handler) : ResponseInterface
    {
        $request = request();

        if (!$this->isAuth($request)) {
            $response = Response::convertToPsr();
            $response->getBody()->write(json_encode(['error' => 'Accès restreint, clé d\'autorisation invalide']));
            $response->withStatus(401);
        } else {
            $response = $handler->handle($psrRequest);
        }
        return $response;
    }
}