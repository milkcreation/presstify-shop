<?php

namespace tiFy\Plugins\Shop\Gateways;

use Illuminate\Support\Collection;
use tiFy\Plugins\Shop\Contracts\GatewayInterface;
use tiFy\Plugins\Shop\Contracts\GatewayListInterface;
use tiFy\Plugins\Shop\Shop;
use tiFy\Plugins\Shop\ShopResolverTrait;

/**
 * Class GatewayList
 *
 * @desc Gestion de la liste des plateformes de paiement déclarées.
 */
class GatewayList implements GatewayListInterface
{
    use ShopResolverTrait;

    /**
     * Classe de rappel de gestion de la liste des plateformes.
     * @var Collection
     */
    protected $collect;

    /**
     * CONSTRUCTEUR.
     *
     * @param GatewayInterface[] $items
     * @param Shop $shop Instance de la boutique.
     *
     * @return void
     */
    public function __construct($items = [], Shop $shop)
    {
        $this->shop = $shop;
        $this->collect = new Collection($items);

    }

    /**
     * {@inheritdoc}
     */
    public function all()
    {
        return $this->collect->all();
    }

    /**
     * {@inheritdoc}
     */
    public function available()
    {
        $filtered = $this->collect->filter(function($item, $key){
            /** @var GatewayInterface $item */
            return $item->isAvailable();
        });

        return $filtered->all();
    }

    /**
     * {@inheritdoc}
     */
    public function get($id)
    {
        return $this->collect->get($id, null);
    }
}