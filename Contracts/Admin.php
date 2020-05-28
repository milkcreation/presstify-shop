<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop\Contracts;

interface Admin extends ShopAwareTrait
{
    /**
     * Initialisation de la classe.
     *
     * @return void
     */
    public function boot(): void;
}