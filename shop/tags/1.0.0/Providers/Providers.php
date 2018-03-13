<?php

/**
 * @name Providers
 * @desc Controleur de gestion des fournisseurs de service
 * @package presstiFy
 * @namespace \tiFy\Plugins\Shop\Providers
 * @version 1.1
 * @since 1.2.596
 *
 * @author Jordy Manner <jordy@tigreblanc.fr>
 * @copyright Milkcreation
 */

namespace tiFy\Plugins\Shop\Providers;

use tiFy\App\Traits\App as TraitsApp;
use tiFy\Plugins\Shop\Shop;

class Providers
{
    use TraitsApp;

    /**
     * Instance de la classe
     * @var Providers
     */
    private static $instance;

    /**
     * Classe de rappel de la boutique
     * @var Shop
     */
    protected $shop;

    /**
     * Liste des fournisseurs de service par défaut
     * @var array
     */
    private $defaults = [
        'page'  => 'tiFy\Plugins\Shop\Providers\PageProvider',
        'price' => 'tiFy\Plugins\Shop\Providers\PriceProvider',
        'url'   => 'tiFy\Plugins\Shop\Providers\UrlProvider'
    ];

    /**
     * Liste des fournisseurs de service à utiliser
     * @var array
     */
    private $providers = [];

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

        // Définition des fournisseurs de service
        $this->providers = $this->shop->appConfig('providers', $this->defaults);
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
     * @return Providers
     */
    public static function make(Shop $shop)
    {
        if (self::$instance) :
            return self::$instance;
        endif;

        return self::$instance = new self($shop);
    }

    /**
     * Récupération du fournisseur de service
     *
     * @param string $name Nom du fournisseur de service déclaré. price|url.
     *
     * @return object|PageProviderInterface|PriceProviderInterface|UrlProviderInterface
     *
     * @throws
     */
    private function get($name)
    {
        $alias = "tiFy\\Plugins\\Shop\\Providers\\" . $this->appUpperName($name) . "ProviderInterface";

        if (!$this->appHasContainer($alias)) :
            $providerClass = isset($this->providers[$name]) ? $this->providers[$name] : $this->defaults[$name];

            if (!in_array($alias, class_implements($providerClass))) :
                throw new \InvalidArgumentException(
                    sprintf(
                        __('Le fournisseur de service doit implémenter l\'interface %s', 'tify'),
                        $alias
                    ),
                    500
                );
            endif;

            $this->appAddContainer($alias, $providerClass)
                ->withArgument($this->shop);
        endif;

        return $this->appGetContainer($alias);
    }

    /**
     * Alias de récupération du fournisseur de gestion des contextes d'affichage
     *
     * @return PageProviderInterface
     */
    public function page()
    {
        return $this->get('page');
    }

    /**
     * Alias de récupération du fournisseur de gestion des tarifs de la boutique
     *
     * @return PriceProviderInterface
     */
    public function price()
    {
        return $this->get('price');
    }

    /**
     * Alias de récupération du fournisseur de gestion des urls de la boutique
     *
     * @return UrlProviderInterface
     */
    public function url()
    {
        return $this->get('url');
    }
}