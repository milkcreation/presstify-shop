<?php

/**
 * @name Page
 * @desc Controleur de récupération des contextes d'affichage des pages de la boutique
 * @namespace \tiFy\Plugins\Shop\Functions
 * @package presstify-plugins/shop
 * @version 1.0.2
 *
 * @author Jordy Manner <jordy@tigreblanc.fr>
 * @copyright Milkcreation
 */

namespace tiFy\Plugins\Shop\Functions;

use tiFy\Apps\AppController;
use tiFy\Plugins\Shop\ServiceProvider\ProvideTraits;
use tiFy\Plugins\Shop\ServiceProvider\ProvideTraitsInterface;
use tiFy\Plugins\Shop\Shop;

class Page extends AppController implements PageInterface, ProvideTraitsInterface
{
    use ProvideTraits;

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
     * Vérifie si la page d'affichage courante correspond à un contexte défini
     *
     * @param string $name Nom de la page. shop|cart|checkout|terms.
     *
     * @return bool
     */
    public function is($name)
    {
        $method = "is" .ucfirst($name);
        if (method_exists($this, $method)) :
            return call_user_func([$this, $method]);
        endif;

        return '';
    }

    /**
     * Vérifie si la page d'affichage courante est la page d'accueil de la boutique
     *
     * @return bool
     */
    public function isShop()
    {
        return \is_single($this->settings()->shopPageId());
    }

    /**
     * Vérifie si la page d'affichage courante est la page du panier
     *
     * @return bool
     */
    public function isCart()
    {
        return \is_single($this->settings()->cartPageId());
    }

    /**
     * Vérifie si la page d'affichage courante est la page de commande
     *
     * @return bool
     */
    public function isCheckout()
    {
        return \is_single($this->settings()->checkoutPageId());
    }

    /**
     * Vérifie si la page d'affichage courante est la page des conditions générales de vente
     *
     * @return bool
     */
    public function isTerms()
    {
        return \is_single($this->settings()->termsPageId());
    }
}