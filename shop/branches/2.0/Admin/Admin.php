<?php

/**
 * @name Admin
 * @desc Controleur de gestion des interfaces d'administration (produits, commandes)
 * @package presstiFy
 * @namespace \tiFy\Plugins\Shop\Admin
 * @version 1.1
 * @since 1.0.0
 *
 * @author Jordy Manner <jordy@tigreblanc.fr>
 * @copyright Milkcreation
 */

namespace tiFy\Plugins\Shop\Admin;

use tiFy\Plugins\Shop\AbstractShopSingleton;
use tiFy\Plugins\Shop\Contracts\AdminInterface;

class Admin extends AbstractShopSingleton implements AdminInterface
{
    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        $this->app()->appAddAction(
            'init',
            function(){
                if ($object_types = $this->shop->products()->getObjectTypeList()) :
                    foreach ($object_types as $id => $object_type) :
                        new ListTable\ListTable($this->shop, $object_type);
                        new Edit\Edit($this->shop, $object_type);
                    endforeach;
                endif;
            }
        );
    }
}