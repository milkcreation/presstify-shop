<?php

/**
 * @name ProductList
 * @desc Controleur de récupération des données d'un produit.
 *
 * @author Jordy Manner <jordy@tigreblanc.fr>
 * @copyright Milkcreation
 */

namespace tiFy\Plugins\Shop\Products;

use tiFy\PostType\Query\PostQueryCollection;
use tiFy\Plugins\Shop\Contracts\ProductListInterface;

class ProductList extends PostQueryCollection implements ProductListInterface
{

}