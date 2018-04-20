<?php

/**
 * @name OrderItemShipping
 * @desc Controleur de la livraison associée aux articles d'une commande
 * @package presstiFy
 * @namespace \tiFy\Plugins\Shop\Orders\OrderItems
 * @version 1.1
 * @since 1.1
 *
 * @author Jordy Manner <jordy@tigreblanc.fr>
 * @copyright Milkcreation
 */

namespace tiFy\Plugins\Shop\Orders\OrderItems;

final class OrderItemTypeShipping extends AbstractOrderItemType implements OrderItemTypeShippingInterface
{
    /**
     * Récupération du type d'élement associé à la commande.
     *
     * @return string shipping
     */
    public function getType()
    {
        return 'shipping';
    }
}