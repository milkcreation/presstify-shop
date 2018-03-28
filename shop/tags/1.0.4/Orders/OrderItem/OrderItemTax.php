<?php

/**
 * @name OrderItemTax
 * @desc Controleur de la taxe associée à une commande
 * @package presstiFy
 * @namespace \tiFy\Plugins\Shop\Orders\OrderItem
 * @version 1.1
 * @since 1.1
 *
 * @author Jordy Manner <jordy@tigreblanc.fr>
 * @copyright Milkcreation
 */

namespace tiFy\Plugins\Shop\Orders\OrderItem;

final class OrderItemTax extends AbstractOrderItem implements OrderItemTaxInterface
{
    /**
     * Récupération du type d'élement associé à la commande.
     *
     * @return string tax
     */
    public function getType()
    {
        return 'tax';
    }
}