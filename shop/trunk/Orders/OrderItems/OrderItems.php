<?php

namespace tiFy\Plugins\Shop\Orders\OrderItems;

use tiFy\App\Traits\App as TraitsApp;
use tiFy\Plugins\Shop\Orders\OrderInterface;
use tiFy\Plugins\Shop\Shop;
use tiFy\Plugins\Shop\ServiceProvider\ProvideTraits;
use tiFy\Plugins\Shop\ServiceProvider\ProvideTraitsInterface;

class OrderItems implements ProvideTraitsInterface
{
    use TraitsApp, ProvideTraits;

    /**
     * Classe de rappel de la commande associée.
     * @var OrderInterface
     */
    protected $order;

    /**
     * Classe de rappel de la boutique.
     * @var Shop
     */
    protected $shop;

    /**
     * CONSTRUCTEUR.
     *
     * @param int $id Identifiant de qualification.
     * @param OrderInterface $order Classe de rappel de la commande associée.
     * @param Shop $shop Classe de rappel de la boutique.
     *
     * @return void
     */
    public function __construct(OrderInterface $order, Shop $shop)
    {
        // Définition de la classe de rappel de la commande associée.
        $this->order = $order;

        // Définition de la classe de rappel de la boutique.
        $this->shop = $shop;
    }

    /**
     * Récupération d'un élément.
     *
     * @param int|object $id Identifiant de qualification de l'objet|Instance de l'objet.
     *
     * @return null|object|OrderItemTypeInterface
     */
    public function get($id = null)
    {
        if ($id instanceof OrderItem) :
            $item = $id;
        elseif ($item = $this->query(
                [
                    'order_item_id' => $id,
                    'order_id' => $this->order->getId()
                ]
            )
        ) :
            $item = current($item);
        else :
            return null;
        endif;

        switch($item->getType()) :
            case 'line_item' :
                return $this->provide('orders.order_item_type_product', [$item, $this->order, $this->shop]);
            break;
        endswitch;

        return null;
    }

    /**
     * Récupération des données d'une liste d'éléments selon des critères de requête.
     *
     * @param array $query_args Liste des arguments de requête.
     *
     * @return array|object|OrderItemList|OrderItemTypeInterface[]
     */
    public function getList($query_args = [])
    {
        if($items = $this->query($query_args)) :
            $items =  array_map([$this, 'get'], $items);
        endif;

        return $this->provide('orders.order_item_list', [$items, $this->shop]);
    }

    /**
     * Requête de récupération d'une liste d'élément associés à une commande
     *
     * @param array $query_args Liste des arguments de requête.
     *
     * @return array|OrderItemInterface[]
     */
    public function query($query_args = [])
    {
        $query_args['order_id'] = $this->order->getId();

        if (!$items = $this->orders()->getDb()->select()->rows($query_args)) :
            return [];
        endif;

        return array_map(function($attributes){ return $this->provide('orders.order_item', [$attributes, $this->shop]);}, $items);
    }
}
