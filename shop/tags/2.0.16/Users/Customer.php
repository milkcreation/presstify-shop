<?php

namespace tiFy\Plugins\Shop\Users;

use tiFy\Plugins\Shop\Contracts\UserCustomerInterface;
use tiFy\Plugins\Shop\Contracts\OrderListInterface;

class Customer extends UserItem implements UserCustomerInterface
{
    /**
     * {@inheritdoc}
     */
    public function isCustomer()
    {
        return true;
    }

    /**
     * Récupération de la liste des commandes du client
     *
     * @param array $query_args Liste des arguments de requête personnalisée.
     * 
     * @return OrderListInterface
     */
    public function getOrderList($query_args = [])
    {
        $query_args = array_merge(
            [
                'order' => 'ASC'
            ],
            $query_args
        );

        $query_args['meta_query'] = [
            [
                'key'   => '_customer_user',
                'value' => $this->getId()
            ]
        ];
        
        return $this->orders()->getCollection($query_args);
    }
}