<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop\Users;

use tiFy\Plugins\Shop\Contracts\{OrdersCollection, UserCustomer as CustomerContract};

class Customer extends User implements CustomerContract
{
    /**
     * Récupération de la liste des commandes du client
     *
     * @param array $query_args Liste des arguments de requête personnalisée.
     *
     * @return OrdersCollection
     */
    public function queryOrders($query_args = [])
    {
        $query_args = array_merge(['order' => 'ASC'], $query_args);

        $query_args['meta_query'] = [
            [
                'key'   => '_customer_user',
                'value' => $this->getId()
            ]
        ];

        return $this->shop()->orders()->query($query_args);
    }

    /**
     * @inheritDoc
     */
    public function isCustomer(): bool
    {
        return true;
    }
}