<?php

/**
 * @name Session
 * @desc Gestion des données portées par la session
 * @package presstiFy
 * @namespace \tiFy\Plugins\Shop\Session
 * @version 1.1
 * @since 1.2.600
 *
 * @author Jordy Manner <jordy@tigreblanc.fr>
 * @copyright Milkcreation
 */

namespace tiFy\Plugins\Shop\Session;

use tiFy\App\Traits\App as TraitsApp;
use tiFy\Core\User\Session\Session as tFySession;
use tiFy\Core\User\Session\Store;
use tiFy\Core\User\Session\StoreInterface;
use tiFy\Plugins\Shop\ServiceProvider\ProvideTraits;
use tiFy\Plugins\Shop\ServiceProvider\ProvideTraitsInterface;
use tiFy\Plugins\Shop\Shop;

class Session implements SessionInterface, ProvideTraitsInterface
{
    use TraitsApp, ProvideTraits;

    /**
     * Instance de la classe.
     * @var Session
     */
    private static $instance;

    /**
     * Classe de rappel de la boutique.
     * @var Shop
     */
    protected $shop;

    /**
     * Récupération de la classe responsable du traitement des sessions.
     * @var StoreInterface
     */
    private $handler;

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

        // Déclaration des événements
        $this->appAddAction('tify_user_session_register', null, 0);
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
     * Instanciation de la classe.
     *
     * @param Shop $shop
     *
     * @return Session
     */
    public static function make(Shop $shop)
    {
        if (self::$instance) :
            return self::$instance;
        endif;

        return self::$instance = new self($shop);
    }

    /**
     * Déclaration de la session.
     *
     * @return void
     */
    public function tify_user_session_register()
    {
        $this->handler = tFySession::register('tify_shop');
    }

    /**
     * Appel de la classe responsable du traitement à l'invocation de la classe.
     *
     * @return StoreInterface
     */
    public function __invoke()
    {
        return $this->handler;
    }
}