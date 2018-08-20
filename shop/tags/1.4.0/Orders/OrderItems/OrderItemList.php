<?php

/**
 * @name OrderItemList
 * @desc Controleur des éléments de commande en base de données.
 * @package presstiFy
 * @namespace \tiFy\Plugins\Shop\Orders\OrderItems
 * @version 1.1
 * @since 1.1
 *
 * @author Jordy Manner <jordy@tigreblanc.fr>
 * @copyright Milkcreation
 */

namespace tiFy\Plugins\Shop\Orders\OrderItems;

use illuminate\Support\Collection;
use tiFy\App\Traits\App as TraitsApp;
use tiFy\Plugins\Shop\Shop;
use tiFy\Plugins\Shop\ServiceProvider\ProvideTraits;
use tiFy\Plugins\Shop\ServiceProvider\ProvideTraitsInterface;

class OrderItemList extends Collection implements ProvideTraitsInterface, OrderItemListInterface
{
    use TraitsApp, ProvideTraits;

    /**
     * Classe de rappel de la boutique.
     * @var Shop
     */
    protected $shop;

    /**
     * CONSTRUCTEUR.
     *
     * @param OrderItem[] $items Liste des éléments.
     * @param Shop $shop Classe de rappel de la boutique.
     *
     * @return void
     */
    public function __construct($items = [], Shop $shop)
    {
        // Définition de la classe de rappel de la boutique.
        $this->shop = $shop;

        parent::__construct($items);
    }
}