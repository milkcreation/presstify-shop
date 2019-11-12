<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop\Contracts;

interface CustomTypes extends ShopAwareTrait
{
    /**
     * Instanciation de la classe.
     *
     * @return void
     */
    public function boot(): void;
}