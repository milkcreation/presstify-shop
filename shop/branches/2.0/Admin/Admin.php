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

use tiFy\Apps\AppController;
use tiFy\Plugins\Shop\ServiceProvider\ProvideTraits;
use tiFy\Plugins\Shop\ServiceProvider\ProvideTraitsInterface;
use tiFy\Plugins\Shop\Shop;

class Admin extends AppController implements AdminInterface, ProvideTraitsInterface
{
    use ProvideTraits;

    /**
     * Instance de la classe.
     * @var Admin
     */
    private static $instance;

    /**
     * Classe de rappel de la boutique.
     * @var Shop
     */
    protected $shop;

    /**
     * CONSTRUCTEUR.
     *
     * @param Shop $shop Classe de rappel de la boutique.
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
     * Court-circuitage de l'implémentation.
     *
     * @return void
     */
    private function __clone()
    {

    }

    /**
     * Court-circuitage de l'implémentation.
     *
     * @return void
     */
    private function __wakeup()
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