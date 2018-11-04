<?php

/**
 * @name LineList
 * @desc Controleur de récupération des données de la liste des lignes du panier d'achat.
 *
 * @author Jordy Manner <jordy@tigreblanc.fr>
 * @copyright Milkcreation
 */

namespace tiFy\Plugins\Shop\Cart;

use Illuminate\Support\Collection;
use tiFy\Plugins\Shop\Contracts\CartLineInterface;
use tiFy\Plugins\Shop\Contracts\CartLineListInterface;

class LineList extends Collection implements CartLineListInterface
{
    /**
     * Liste des élements.
     *
     * @var CartLineInterface[]
     */
    protected $items = [];

    /**
     * Get an item at a given offset.
     *
     * @param  mixed  $key
     *
     * @return CartLineInterface
     */
    public function offsetGet($key)
    {
        return $this->items[$key];
    }
}