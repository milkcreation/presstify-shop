<?php

/**
 * @name Functions
 * @desc Controleur de gestion des fonctions utiles.
 *
 * @author Jordy Manner <jordy@tigreblanc.fr>
 * @copyright Milkcreation
 */

namespace tiFy\Plugins\Shop\Functions;

use tiFy\Plugins\Shop\AbstractShopSingleton;
use tiFy\Plugins\Shop\Contracts\FunctionsInterface;

class Functions extends AbstractShopSingleton implements FunctionsInterface
{
    /**
     * Liste des fonctions disponibles.
     * @var string[]
     */
    protected $available = [];

    /**
     * {@inheritdoc}
     */
    public function boot()
    {

    }

    /**
     * {@inheritdoc}
     */
    public function date($time = 'now', $timezone = true)
    {
        return app('shop.functions.date', [$time, $timezone, $this->shop]);
    }

    /**
     * {@inheritdoc}
     */
    public function page()
    {
        return app('shop.functions.page', [$this->shop]);
    }

    /**
     * {@inheritdoc}
     */
    public function price()
    {
        return app('shop.functions.price', [$this->shop]);
    }

    /**
     * {@inheritdoc}
     */
    public function url()
    {
        return app('shop.functions.url', [$this->shop]);
    }
}