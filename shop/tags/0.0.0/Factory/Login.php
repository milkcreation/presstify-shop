<?php
namespace tiFy\Plugins\Shop\Factory;

use tiFy\Components;

class Login extends \tiFy\Plugins\Shop\Factory
{
    /**
     * Classe de rappel de l'interface d'authentification
     * @var \tiFy\Components\Login\Factory
     */
    private static $LoginUi;

    /**
     * CONSTRUCTEUR
     *
     * @return void
     */
    public function __construct($id, $attrs = [])
    {
        parent::__construct($id, $attrs);

        // Déclaration des événements
        $this->appAddAction('tify_components_register');
    }

    /**
     * EVENEMENTS
     */
    /**
     * Déclaration de composants tiFy
     *
     * @return void
     */
    public function tify_components_register()
    {
        // Interface d'authentification
        self::$LoginUi = Components::register(
            'Login',
            [
                '_tiFyShop' => self::tFyAppConfig('login', [], 'tiFy\Plugins\Shop\Shop')
            ]
        );
    }

    /**
     * CONTROLEURS
     */
    /**
     * Récupération de la classe de rappel de l'interface d'authentification
     *
     * @return \tiFy\Components\Login\Factory
     */
    public static function get()
    {
        return self::$LoginUi;
    }
}