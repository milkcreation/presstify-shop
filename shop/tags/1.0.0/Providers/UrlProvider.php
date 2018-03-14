<?php

/**
 * @name UrlProvider
 * @desc Controleur de récupération des url de la boutique
 * @package presstiFy
 * @namespace \tiFy\Plugins\Shop\Providers
 * @version 1.1
 * @since 1.2.596
 *
 * @author Jordy Manner <jordy@tigreblanc.fr>
 * @copyright Milkcreation
 */

namespace tiFy\Plugins\Shop\Providers;

use tiFy\Plugins\Shop\Shop;

class UrlProvider implements UrlProviderInterface
{
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
    public function __construct(Shop $shop)
    {
        // Définition de la classe de rappel de la boutique
        $this->shop = $shop;
    }

    /**
     * Url vers une page de la boutique
     *
     * @param string $name Nom de la page. shop|cart|checkout|terms.
     *
     * @return string
     */
    public function page($name)
    {
        $method = strtolower($name) . "Page";
        if (method_exists($this, $method)) :
            return call_user_func([$this, $method]);
        endif;

        return '';
    }

    /**
     * Url vers la page d'accueil de la boutique
     *
     * @return string
     */
    public function shopPage()
    {
        return ($page_id = $this->shop->settings()->shopPageId()) ? \get_permalink($page_id) : \get_home_url();
    }

    /**
     * Url vers la page d'affichage du panier
     *
     * @return string
     */
    public function cartPage()
    {
        return ($page_id = $this->shop->settings()->cartPageId()) ? \get_permalink($page_id) : \get_home_url();
    }

    /**
     * Url vers la page de commande
     *
     * @return string
     */
    public function checkoutPage()
    {
        return ($page_id = $this->shop->settings()->checkoutPageId()) ? \get_permalink($page_id) : \get_home_url();
    }

    /**
     * Url vers la page des conditions générales de vente
     *
     * @return string
     */
    public function termsPage()
    {
        return ($page_id = $this->shop->settings()->termsPageId()) ? \get_permalink($page_id) : \get_home_url();
    }
}