<?php

namespace tiFy\Plugins\Shop\Users;

use tiFy\Plugins\Shop\Contracts\OrderListInterface;
use tiFy\Plugins\Shop\Contracts\UserCustomerInterface;

class Customer extends UserItem implements UserCustomerInterface
{
    /**
     * Récupération de la liste des commandes du client
     *
     * @param array $query_args Liste des arguments de requête personnalisée.
     *
     * @return OrderListInterface
     */
    public function getOrderList($query_args = [])
    {
        $query_args = array_merge([
            'order' => 'ASC'
        ], $query_args);

        $query_args['meta_query'] = [
            [
                'key'   => '_customer_user',
                'value' => $this->getId()
            ]
        ];

        return $this->orders()->getCollection($query_args);
    }

    /**
     * {@inheritdoc}
     */
    public function isCustomer()
    {
        return true;
    }
}