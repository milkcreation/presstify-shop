<?php

namespace tiFy\Plugins\Shop\Admin;

use tiFy\App\Traits\App as TraitsApp;
use tiFy\Plugins\Shop\Shop;

class Admin
{
    use TraitsApp;

    /**
     * Instance de la classe
     * @var Admin
     */
    private static $instance;

    /**
     * Classe de rappel de la boutique
     * @var Shop
     */
    protected $shop;

    /**
     * CONSTRUCTEUR
     *
     * @param Shop $shop Classe de rappel de la boutique
     *
     * @return void
     */
    protected function __construct(Shop $shop)
    {
        // Définition de la classe de rappel de la boutique
        $this->shop = $shop;

        // Déclaration des événements de déclenchement
        $this->appAddAction('init');
    }

    /**
     * Court-circuitage de l'implémentation
     *
     * @return void
     */
    protected function __clone()
    {

    }

    /**
     * Court-circuitage de l'implémentation
     *
     * @return void
     */
    protected function __wakeup()
    {

    }

    /**
     * Instanciation de la classe
     *
     * @param Shop $shop
     *
     * @return Admin
     */
    public static function make(Shop $shop)
    {
        if (self::$instance) :
            return self::$instance;
        endif;

        return self::$instance = new self($shop);
    }

    /**
     * Initialisation globale
     *
     * @return void
     */
    final public function init()
    {
        // Initialisation des interface d'administration
        if ($object_types = $this->shop->products()->getObjectTypeList()) :
            foreach ($object_types as $id => $object_type) :
                new ListTable\ListTable($this->shop, $object_type);
                new Edit\Edit($this->shop, $object_type);
            endforeach;
        endif;
    }
}