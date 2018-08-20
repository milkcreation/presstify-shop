<?php

/**
 * @name LineList
 * @desc Controleur de récupération des données de la liste des lignes du panier d'achat
 * @package presstiFy
 * @namespace \tiFy\Plugins\Shop\Cart
 * @version 1.1
 * @since 1.2.535
 *
 * @author Jordy Manner <jordy@tigreblanc.fr>
 * @copyright Milkcreation
 */

namespace tiFy\Plugins\Shop\Cart;

use Illuminate\Support\Collection;
use tiFy\App\Traits\App as TraitsApp;

class LineList extends Collection
{
    use TraitsApp;

    /**
     * Liste des élements.
     *
     * @var LineInterface[]
     */
    protected $items = [];

    /**
     * Get an item at a given offset.
     *
     * @param  mixed  $key
     * @return LineInterface
     */
    public function offsetGet($key)
    {
        return $this->items[$key];
    }
}