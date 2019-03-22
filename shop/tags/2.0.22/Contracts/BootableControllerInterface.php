<?php

namespace tiFy\Plugins\Shop\Contracts;

interface BootableControllerInterface
{
    /**
     * Initialisation du controleur.
     *
     * @return void
     */
    public function boot();
}