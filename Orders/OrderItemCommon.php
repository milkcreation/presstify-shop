<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop\Orders;

/**
 * Tronc commun partagé à tous les types OrderItem par le biais de la methode __call.
 * @see \tiFy\Plugins\Shop\Orders\AbstractOrderItem::__call()
 */
class OrderItemCommon extends AbstractOrderItem
{
}