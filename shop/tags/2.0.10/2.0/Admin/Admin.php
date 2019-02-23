<?php

namespace tiFy\Plugins\Shop\Admin;

use tiFy\Plugins\Shop\AbstractShopSingleton;
use tiFy\Plugins\Shop\Contracts\AdminInterface;

/**
 * Class Admin
 *
 * @desc Controleur de gestion des interfaces d'administration (produits, commandes)
 */
class Admin extends AbstractShopSingleton implements AdminInterface
{
    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        add_action('init', function() {
            if ($object_types = $this->shop->products()->getObjectTypeList()) :
                foreach ($object_types as $id => $object_type) :
                    new ListTable\ListTable($object_type, $this->shop);
                    new Edit\Edit($object_type, $this->shop);
                endforeach;
            endif;
        }, 0);
    }
}