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

use Illuminate\Support\Arr;
use LogicException;
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
     * Liste des services
     * @var array
     */
    protected $providers;

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
     * Alias de récupération du fournisseur de gestion des contextes d'affichage
     *
     * @return object|PageProviderInterface
     */
    final public function page()
    {
        if ($page = Arr::get($this->providers, 'page', null)) :
            return $page;
        endif;

        $page = $this->shop->provide('providers.page');
        if(! $page instanceof PageProviderInterface) :
            throw new LogicException(
                sprintf(
                    __('Le controleur de surcharge doit implémenter %s', 'tify'),
                    PageProviderInterface::class
                ),
                500
            );
        endif;

        return $page;
    }

    /**
     * Alias de récupération du fournisseur de gestion des tarifs de la boutique
     *
     * @return object|PriceProviderInterface
     */
    final public function price()
    {
        if ($price = Arr::get($this->providers, 'price', null)) :
            return $price;
        endif;

        $price = $this->shop->provide('providers.price');
        if(! $price instanceof PriceProviderInterface) :
            throw new LogicException(
                sprintf(
                    __('Le controleur de surcharge doit implémenter %s', 'tify'),
                    PriceProviderInterface::class
                ),
                500
            );
        endif;

        return $price;
    }

    /**
     * Alias de récupération du fournisseur de gestion des urls de la boutique
     *
     * @return object|UrlProviderInterface
     */
    final public function url()
    {
        if ($url = Arr::get($this->providers, 'url', null)) :
            return $url;
        endif;

        $url = $this->shop->provide('providers.url');
        if(! $url instanceof UrlProviderInterface) :
            throw new LogicException(
                sprintf(
                    __('Le controleur de surcharge doit implémenter %s', 'tify'),
                    UrlProviderInterface::class
                ),
                500
            );
        endif;

        return $url;
    }
}