<?php

/**
 * @name Url
 * @desc Controleur de récupération des url de la boutique
 * @namespace \tiFy\Plugins\Shop\Functions
 * @package presstify-plugins/shop
 * @version 1.0.2
 *
 * @author Jordy Manner <jordy@tigreblanc.fr>
 * @copyright Milkcreation
 */

namespace tiFy\Plugins\Shop\Functions;

use League\Uri;
use tiFy\Apps\AppController;
use tiFy\Plugins\Shop\ServiceProvider\ProvideTraits;
use tiFy\Plugins\Shop\ServiceProvider\ProvideTraitsInterface;
use tiFy\Plugins\Shop\Shop;

class Url extends AppController implements UrlInterface, ProvideTraitsInterface
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
     * Url vers la page d'accueil de la boutique.
     *
     * @return string
     */
    public function shopPage()
    {
        return ($page_id = $this->settings()->shopPageId()) ? \get_permalink($page_id) : \get_home_url();
    }

    /**
     * Url vers la page d'affichage du panier.
     *
     * @return string
     */
    public function cartPage()
    {
        return ($page_id = $this->settings()->cartPageId()) ? \get_permalink($page_id) : \get_home_url();
    }

    /**
     * Url vers la page de commande.
     *
     * @return string
     */
    public function checkoutPage()
    {
        return ($page_id = $this->settings()->checkoutPageId()) ? \get_permalink($page_id) : \get_home_url();
    }

    /**
     * Url vers la page de demande de paiement de la commande.
     *
     * @param array $args Liste des arguments de requête.
     *
     * @return string
     */
    public function checkoutOrderPayPage($args = [])
    {
        $base_uri = Uri\create($this->checkoutPage());

        return (string)Uri\append_query($base_uri, http_build_query($args));
    }

    /**
     * Url vers la page de commande reçue.
     *
     * @param array $args Liste des arguments de requête.
     *
     * @return string
     */
    public function checkoutOrderReceivedPage($args = [])
    {
        $base_uri = Uri\create($this->checkoutPage());

        return (string)Uri\append_query($base_uri, http_build_query($args));
    }

    /**
     * Url vers la page d'ajout de moyen de paiement.
     * @todo
     *
     * @return string
     */
    public function checkoutAddPaymentMethodPage()
    {
        return '';
    }

    /**
     * Url vers la page de suppression de moyen de paiement.
     * @todo
     *
     * @return string
     */
    public function checkoutDeletePaymentMethodPage()
    {
        return '';
    }

    /**
     * Url vers la page de définition du moyen de paiement par défaut.
     * @todo
     *
     * @return string
     */
    public function checkoutSetDefaultPaymentMethodPage()
    {
        return '';
    }

    /**
     * Url vers la page des conditions générales de vente.
     *
     * @return string
     */
    public function termsPage()
    {
        return ($page_id = $this->settings()->termsPageId()) ? \get_permalink($page_id) : \get_home_url();
    }
}