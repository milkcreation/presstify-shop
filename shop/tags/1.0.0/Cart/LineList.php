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

use tiFy\App\Traits\App as TraitsApp;
use Illuminate\Support\Collection;

class LineList extends Collection
{
    use TraitsApp;
}