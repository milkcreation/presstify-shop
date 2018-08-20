<?php

namespace tiFy\Plugins\Shop\Users;

use tiFy\Plugins\Shop\Orders\OrderListInterface;
use tiFy\Plugins\Shop\Shop;

class Customer extends AbstractUser implements CustomerInterface
{
    /**
     * Vérifie si un utilisateur est considéré en tant que client
     *
     * @return bool
     */
    public function isCustomer()
    {
        return true;
    }

    /**
     * Récupération de la liste des commandes du client
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
        $query_args['post_status'] = $this->orders()->getRelPostStatuses();

        return $this->orders()->getList($query_args);
    }
}