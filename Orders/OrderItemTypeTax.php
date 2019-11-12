<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop\Orders;

use tiFy\Plugins\Shop\Contracts\OrderItemTypeTax as OrderItemTypeTaxContract;

class OrderItemTypeTax extends OrderItemType implements OrderItemTypeTaxContract
{
    /**
     * @inheritDoc
     */
    public function getType(): string
    {
        return 'tax';
    }
}