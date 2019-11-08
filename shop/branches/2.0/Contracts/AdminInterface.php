<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop\Contracts;

interface AdminInterface extends BootableControllerInterface, ShopAwareTrait
{
    /**
     * Initialisation de la classe.
     *
     * @return void
     */
    public function boot(): void;
}