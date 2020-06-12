<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop\Middleware;

use Psr\Http\{
    Message\ResponseInterface as PsrResponse,
    Message\ServerRequestInterface as PsrRequest,
    Server\RequestHandlerInterface as RequestHandler
};
use tiFy\Http\Response;
use tiFy\Plugins\Shop\Contracts\ApiMiddleware as ApiMiddlewareContract;
use tiFy\Plugins\Shop\ShopAwareTrait;
use tiFy\Routing\BaseMiddleware;
use tiFy\Support\Proxy\Request;

class ApiMiddleware extends BaseMiddleware implements ApiMiddlewareContract
{
    use ShopAwareTrait;

    /**
     * @inheritDoc
     */
    public function isAuth(): bool
    {
        return in_array(Request::input('authtoken'), $this->shop()->config('api.authtoken', []));
    }

    /**
     * @inheritDoc
     */
    public function process(PsrRequest $psrRequest, RequestHandler $handler): PsrResponse
    {
        if ($this->isAuth()) {
            return $handler->handle($psrRequest);

        } else {
            return (new Response(json_encode([
                'error' => __('Accès restreint, clé d\'autorisation invalide.', 'tify')
            ]), 401))->psr();
        }
    }
}