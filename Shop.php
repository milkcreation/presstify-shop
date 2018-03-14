<?php
/*
 Plugin Name: Shop
 Plugin URI: http://presstify.com/plugins/shop
 Description: boutique
 Version: 1.528
 Author: Milkcreation
 Author URI: http://milkcreation.fr
 Text Domain: tify
*/
namespace tiFy\Plugins\Shop;

class Shop extends \tiFy\App\Plugin
{
    /**
     * Liste des identifiants de qualification des classes de rappel des contrôleurs
     * @var string[]
     */
    private static $FactoryIds  = [
        'login',
        'products',
        'roles',
        'session',
        'users'
    ];

    /**
     * Liste des classes de rappel des contrôleurs
     * @var \tiFy\Plugins\Shop\Factory\Factory[]
     */
    private static $Factory = [];

    /**
     * CONSTRUCTEUR
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        // Chargement des contrôleurs
        if ($factory_ids = self::$FactoryIds) :
            foreach ($factory_ids as $id) :
                if (!self::tFyAppConfig($id)) :
                    continue;
                endif;

                $ShortName = join('', array_map('ucfirst', preg_split('#_#', $id)));

                $Factory = self::tFyAppNamespace() . "\\Factory\\" . $ShortName;
                if (class_exists($Factory)) :
                    self::$Factory[$id] = new $Factory($id, []);
                endif;
            endforeach;
        endif;
    }

    /**
     * CONTROLEURS
     */
    /**
     * Récupération d'une classe de rappel de controleur
     *
     * @param string $id Identifiant de qualification d'un controleur
     *
     * @return \tiFy\Plugins\Shop\Factory\Factory
     */
    public static function get($id)
    {
        if(!in_array($id, self::$FactoryIds)) :
            return;
        endif;

        if (!isset(self::$Factory[$id])) :
            return;
        endif;

        return self::$Factory[$id];
    }

    /**
     * Alias de récupération de la classe de rappel du controleur d'interface d'authentification
     *
     * @return \tiFy\Plugins\Shop\Factory\Login::get()
     */
    final public static function login()
    {
        $login = self::$Factory['login'];
	    return $login::get();
    }

    /**
     * Alias de récupération de la classe de rappel du controleur de session
     *
     * @return \tiFy\Plugins\Shop\Factory\Session
     */
    final public static function session()
    {
        return self::$Factory['session'];
    }

    /**
     * Alias de récupération de la classe de rappel du controleur de session
     *
     * @return \tiFy\Plugins\Shop\Factory\Users
     */
    final public static function user()
    {
        return self::$Factory['users'];
    }
}
