<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop\Contracts;

use tiFy\Contracts\Routing\Middleware;

interface ApiMiddleware extends Middleware, ShopAwareTrait
{
    /**
     * Vérification de l'authentification.
     *
     * @return boolean
     */
    public function isAuth(): bool;
}