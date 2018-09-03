<?php

/**
 * @name Functions
 * @desc Controleur de gestion des fonctions utiles.
 *
 * @author Jordy Manner <jordy@tigreblanc.fr>
 * @copyright Milkcreation
 */

namespace tiFy\Plugins\Shop\Functions;

use LogicException;
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
        return $this->app('shop.functions.date', [$time, $timezone, $this->shop]);
    }

    /**
     * {@inheritdoc}
     */
    public function page()
    {
        return $this->app('shop.functions.page', [$this->shop]);
    }

    /**
     * {@inheritdoc}
     */
    public function price()
    {
        return $this->app('shop.functions.price', [$this->shop]);
    }

    /**
     * {@inheritdoc}
     */
    public function url()
    {
        return $this->app('shop.functions.url', [$this->shop]);
    }
}