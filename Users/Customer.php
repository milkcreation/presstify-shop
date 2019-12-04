<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop\Users;

use tiFy\Plugins\Shop\Contracts\{UserCustomer as CustomerContract};

class Customer extends User implements CustomerContract
{
    /**
     * @inheritDoc
     */
    public function getOrders(array $args = []): array
    {
        $args = array_merge(['order' => 'ASC'], $args);

        $args['meta_query'] = [
            [
                'key'   => '_customer_user',
                'value' => $this->getId()
            ]
        ];

        return $this->shop()->orders($args);
    }

    /**
     * @inheritDoc
     */
    public function isCustomer(): bool
    {
        return true;
    }
}