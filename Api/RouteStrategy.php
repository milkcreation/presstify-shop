<?php

namespace tiFy\Plugins\Shop\Api;

use League\Route\Strategy\JsonStrategy;
use League\Route\Route;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use tiFy\Contracts\Routing\Route as RouteContract;

class RouteStrategy extends JsonStrategy
{
    /**
     * {@inheritdoc}
     */
    public function invokeRouteCallable(Route $route, ServerRequestInterface $request) : ResponseInterface
    {
        /** @var RouteContract $route */
        $route->setCurrent();

	    $controller = $route->getCallable($this->getContainer());

	    $resolved = call_user_func_array($controller, $route->getVars());

	    if ($this->isJsonEncodable($resolved)) :
		    $body = json_encode($resolved['body']);

	        $response = $this->responseFactory->createResponse(200);

		    foreach($resolved['headers'] as $name => $value) :
                $this->addDefaultResponseHeader("x-{$name}", $value);
		    endforeach;

		    $response->getBody()->write($body);
	    endif;

	    $response = $this->applyDefaultResponseHeaders($response);

	    return $response;
    }
}