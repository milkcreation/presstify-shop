<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop\Api;

use Psr\Http\{
    Message\ResponseInterface,
    Message\ServerRequestInterface,
    Server\MiddlewareInterface,
    Server\RequestHandlerInterface
};
use tiFy\Contracts\Http\Request;
use tiFy\Http\Response;

class Middleware implements MiddlewareInterface
{
    /**
     * Vérification de l'authentification.
     *
     * @param Request $request
     *
     * @return boolean
     */
    protected function isAuth(Request $request): bool
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