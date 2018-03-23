<?php

/**
 * @name GatewayList
 * @desc Gestion des plateformes de paiement déclarées
 * @package presstiFy
 * @namespace \tiFy\Plugins\Shop\Gateways
 * @version 1.1
 * @since 1.3.1
 *
 * @author Jordy Manner <jordy@tigreblanc.fr>
 * @copyright Milkcreation
 */

namespace tiFy\Plugins\Shop\Gateways;

use Illuminate\Support\Collection;
use tiFy\Plugins\Shop\Shop;

class GatewayList implements GatewayListInterface
{
    /**
     * Classe de rappel de la boutique.
     * @var Shop
     */
    protected $shop;

    /**
     * Classe de rappel de gestion de la liste des plateformes.
     * @var \Illuminate\Support\Collection
     */
    protected $collect;

    /**
     * CONSTRUCTEUR
     *
     * @param Shop $shop Classe de rappel de la boutique
     * @param GatewayInterface[] $items
     *
     * @return void
     */
    public function __construct(Shop $shop, $items = [])
    {
        // Définitation de la classe de rappel de gestion de la liste des plateformes
        $this->collect = new Collection($items);

        // Définition de la classe de rappel de la boutique
        $this->shop = $shop;
    }

    /**
     * Récupération de la liste complète des plateformes de paiement déclarées.
     *
     * @return GatewayInterface[]
     */
    public function all()
    {
        return $this->collect->all();
    }

    /**
     * Récupération de la liste des plateformes de paiement disponibles
     *
     * @return GatewayInterface[]
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
     * Récupération d'une plateforme de paiement déclarée.
     *
     * @param string $id Identifiant de qualification de la plateforme
     *
     * @return null|GatewayInterface
     */
    public function get($id)
    {
        return $this->collect->get($id, null);
    }
}