<?php

/**
 * @name OrderItemProduct
 * @desc Controleur d'une ligne d'article associée à une commande
 * @package presstiFy
 * @namespace \tiFy\Plugins\Shop\Orders\OrderItems
 * @version 1.1
 * @since 1.1
 *
 * @author Jordy Manner <jordy@tigreblanc.fr>
 * @copyright Milkcreation
 */

namespace tiFy\Plugins\Shop\Orders\OrderItems;

final class OrderItemFee extends AbstractOrderItem implements OrderItemFeeInterface
{
    /**
     * Récupération du type d'élement associé à la commande.
     *
     * @return string fee
     */
    public function getType()
    {
        return 'fee';
    }
}