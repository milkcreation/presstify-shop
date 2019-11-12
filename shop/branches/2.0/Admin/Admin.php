<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop\Admin;

use tiFy\Plugins\Shop\Contracts\{Admin as AdminContract, Shop};
//use tiFy\Plugins\Shop\Admin\{Edit\Edit, ListTable\ListTable};
use tiFy\Plugins\Shop\ShopAwareTrait;

class Admin implements AdminContract
{
    use ShopAwareTrait;

    /**
     * CONSTRUCTEUR.
     *
     * @param Shop $shop
     *
     * @return void
     */
    public function __construct(Shop $shop)
    {
        $this->setShop($shop);

        $this->boot();

        add_action('init', function() {
            /** @todo COMPATIBILITE tiFY 2.0
             if ($object_types = $this->shop->products()->getObjectTypeList()) {
                foreach ($object_types as $id => $object_type) {
                    new ListTable($object_type, $this->shop);
                    //new Edit($object_type, $this->shop);
                }
            } */
        }, 0);
    }

    /**
     * @inheritDoc
     */
    public function boot(): void {}
}