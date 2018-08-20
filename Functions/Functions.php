<?php

/**
 * @name Functions
 * @desc Controleur de gestion des fonctions
 * @namespace \tiFy\Plugins\Shop\Functions
 * @package presstify-plugins/shop
 * @version 1.0.2
 *
 * @author Jordy Manner <jordy@tigreblanc.fr>
 * @copyright Milkcreation
 */

namespace tiFy\Plugins\Shop\Functions;

use LogicException;
use tiFy\App\Traits\App as TraitsApp;
use tiFy\Plugins\Shop\ServiceProvider\ProvideTraits;
use tiFy\Plugins\Shop\ServiceProvider\ProvideTraitsInterface;
use tiFy\Plugins\Shop\Shop;

class Functions implements FunctionsInterface, ProvideTraitsInterface
{
    use TraitsApp, ProvideTraits;

    /**
     * Instance de la classe.
     * @var Functions
     */
    private static $instance;

    /**
     * Classe de rappel de la boutique.
     * @var Shop
     */
    protected $shop;

    /**
     * Liste des fonctions disponibles.
     * @var string[]
     */
    protected $available = [];

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
     * @return Functions
     */
    public static function make(Shop $shop)
    {
        if (self::$instance) :
            return self::$instance;
        endif;

        return self::$instance = new self($shop);
    }

    /**
     * Appel d'une fonction disponible.
     *
     * @param string $function Identifiant de qualification de la fonction.
     * @param array $args Liste des variables passées en argument.
     * @param string $instanceof Intitulé de l'instance à controler.
     *
     * @return array|mixed
     */
    private function call($name, $args = [], $instanceof = null)
    {
        if (in_array($name, $this->available)) :
            return $this->provide('functions.' . $name, $args);
        endif;

        $new = $this->provide('functions.' . $name, $args);
        if ($instanceof) :
            if(! $new instanceof $instanceof) :
                throw new LogicException(
                    sprintf(
                        __('Le controleur de surcharge doit implémenter %s', 'tify'),
                        $instanceof
                    ),
                    500
                );
            endif;
        endif;

        array_push($this->available, $name);

        return $new;
    }

    /**
     * Alias de récupération du fournisseur de gestion des contextes d'affichage
     *
     * @param string $time Date à traité. now par défaut.
     * @see http://php.net/manual/fr/class.datetime.php
     *
     * @return object|DateInterface
     */
    final public function date($time = 'now', $timezone = true)
    {
        return $this->call('date', [$time, $timezone, $this->shop], DateInterface::class);
    }

    /**
     * Alias de récupération du fournisseur de gestion des contextes d'affichage.
     *
     * @return object|PageInterface
     */
    final public function page()
    {
        return $this->call('page', [$this->shop], PageInterface::class);
    }

    /**
     * Alias de récupération du fournisseur de gestion des tarifs de la boutique.
     *
     * @return object|PriceInterface
     */
    final public function price()
    {
        return $this->call('price', [$this->shop], PriceInterface::class);
    }

    /**
     * Alias de récupération du fournisseur de gestion des urls de la boutique.
     *
     * @return object|UrlInterface
     */
    final public function url()
    {
        return $this->call('url', [$this->shop], UrlInterface::class);
    }
}