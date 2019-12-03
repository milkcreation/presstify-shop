<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop\Api;

use Psr\Http\{
    Message\ResponseInterface,
    Message\ServerRequestInterface as ServerRequest,
    Server\MiddlewareInterface,
    Server\RequestHandlerInterface as RequestHandler
};
use tiFy\Support\Proxy\{Response, Request};


class Middleware implements MiddlewareInterface
{
    /**
     * Vérification de l'authentification.
     *
     * @return boolean
     */
    protected function isAuth(): bool
    {
        return in_array(Request::input('authtoken'), config('shop.api.authtoken', []));
    }

    /**
     * @inheritDoc
     */
    public function process(ServerRequest $request, RequestHandler $handler) : ResponseInterface
    {
        if (!$this->isAuth()) {
            $response = Response::psr();
            $response->getBody()->write(json_encode(['error' => 'Accès restreint, clé d\'autorisation invalide']));
            $response->withStatus(401);
        } else {
            $response = $handler->handle($request);
        }

        return $response;
    }
}