<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop\Contracts;

use tiFy\User\Session\SessionStore;

/**
 * @mixin SessionStore
 */
interface Session extends ShopAwareTrait
{
    /**
     * Instanciation de la classe.
     *
     * @return void
     */
    public function boot(): void;
}