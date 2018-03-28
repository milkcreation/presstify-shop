<?php

/**
 * @name OrderItemShipping
 * @desc Controleur de la livraison associée aux articles d'une commande
 * @package presstiFy
 * @namespace \tiFy\Plugins\Shop\Orders\OrderItem
 * @version 1.1
 * @since 1.1
 *
 * @author Jordy Manner <jordy@tigreblanc.fr>
 * @copyright Milkcreation
 */

namespace tiFy\Plugins\Shop\Orders\OrderItem;

final class OrderItemShipping extends AbstractOrderItem implements OrderItemShippingInterface
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