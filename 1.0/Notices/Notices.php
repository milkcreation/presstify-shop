<?php

/**
 * @name Notices
 * @desc Gestion des messages de notification
 * @package presstiFy
 * @namespace \tiFy\Plugins\Shop\Notices
 * @version 1.1
 * @since 1.2.600
 *
 * @author Jordy Manner <jordy@tigreblanc.fr>
 * @copyright Milkcreation
 */

namespace tiFy\Plugins\Shop\Notices;

use tiFy\App\Traits\App as TraitsApp;
use tiFy\Core\Layout\Layout;
use Illuminate\Support\Arr;
use tiFy\Plugins\Shop\Shop;

final class Notices
{
    use TraitsApp;

    /**
     * Instance de la classe
     * @var Notices
     */
    private static $instance;

    /**
     * Classe de rappel de la boutique
     * @var Shop
     */
    protected $shop;

    /**
     * Classe de rappel de traitement de la session
     * @var \tiFy\Core\User\Session\StoreInterface
     */
    protected $session;

    /**
     * Liste des messages de notification à afficher
     * @var array
     */
    protected $notices = [];

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

        // Déclaration des événements
        $this->appAddAction('wp_loaded');
        $this->appAddAction('wp_enqueue_scripts');
    }

    /**
     * Court-circuitage de l'implémentation
     *
     * @return void
     */
    private function __clone()
    {

    }

    /**
     * Court-circuitage de l'implémentation
     *
     * @return void
     */
    private function __wakeup()
    {

    }

    /**
     * Affichage des message de notification
     */
    public function __toString()
    {
        return $this->display();
    }

    /**
     * Instanciation de la classe
     *
     * @param Shop $shop
     *
     * @return Notices
     */
    public static function make(Shop $shop)
    {
        if (self::$instance) :
            return self::$instance;
        endif;

        return self::$instance = new self($shop);
    }

    /**
     * A l'issue du chargement de Wordpress
     *
     * @return void
     */
    public function wp_loaded()
    {
        $this->notices = $this->shop->session()->get('notices', []);
    }

    /**
     * Mise en file des scripts
     *
     * @return void
     */
    public function wp_enqueue_scripts()
    {
        if (!$this->notices) :
            return;
        endif;

        Layout::Notice()->enqueue_scripts();
    }

    /**
     * Ajout d'un message de notification
     *
     * @param string $message Intitulé du message de notification
     * @param string $type Type de message de notification success (default)|warning|info|error
     *
     * @return void
     *
     * @throws \LogicException
     */
    public function add($message, $type = 'success')
    {
        if (!did_action('wp_loaded')) :
            throw new \LogicException(
                __('L\'ajout de message de notification ne devrait pas être fait à ce moment de l\'exécution de votre code', 'tify'),
                500
            );
        endif;

        if (!isset($this->notices[$type])) :
            $this->notices[$type] = [];
        endif;
        $this->notices[$type][] = $message;

        $this->shop->session()->put('notices', $this->notices);
    }

    /**
     * Suppression de la liste des messages de notification
     *
     * @return void
     */
    public function clear()
    {
        $this->notices = [];
        $this->shop->session()->put('notices', []);
    }

    /**
     * Affichage des messages de notification
     *
     * @return string
     */
    public function display()
    {
        if (!did_action('template_redirect')) :
            throw new \LogicException(
                __('L\'affichage des messages de notifications ne devrait pas être fait à ce moment de l\'execution de votre code', 'tify'),
                500
            );
        endif;

        if (!$this->notices) :
            return '';
        endif;

        $output = "";
        foreach ($this->notices as $type => $messages) :
            foreach($messages as $text) :
                $output .= (string)Layout::Notice(compact('type', 'text'));
            endforeach;
        endforeach;

        $this->clear();

        return $output;
    }
}