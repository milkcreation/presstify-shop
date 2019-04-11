<?php

namespace tiFy\Plugins\Shop\Api;

use League\Route\Strategy\JsonStrategy;
use League\Route\Route;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use tiFy\Contracts\Routing\Route as RouteContract;
use tiFy\Http\Response;

class RouteStrategy extends JsonStrategy
{
    /**
     * @inheritdoc
     */
    public function invokeRouteCallable(Route $route, ServerRequestInterface $request): ResponseInterface
    {
        /** @var RouteContract $route */
        $route->setCurrent();

        $controller = $route->getCallable($this->getContainer());

        $args = array_values($route->getVars());
        array_push($args, $request);
        $resolved = $controller(...$args);

        $response = Response::convertToPsr();
        if ($this->isJsonEncodable($resolved)) {
            $body = json_encode($resolved['body']);

            $response = $this->responseFactory->createResponse(200);

            foreach ($resolved['headers'] as $name => $value) {
                $this->addDefaultResponseHeader("x-{$name}", $value);
            }

            $response->getBody()->write($body);
        }
        return $this->applyDefaultResponseHeaders($response);
    }
}