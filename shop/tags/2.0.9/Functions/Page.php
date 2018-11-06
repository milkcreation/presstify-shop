<?php

/**
 * @name Page
 * @desc Controleur de récupération des contextes d'affichage des pages de la boutique.
 *
 * @author Jordy Manner <jordy@tigreblanc.fr>
 * @copyright Milkcreation
 */

namespace tiFy\Plugins\Shop\Functions;

use tiFy\Plugins\Shop\Contracts\FunctionsPageInterface;
use tiFy\Plugins\Shop\Shop;
use tiFy\Plugins\Shop\ShopResolverTrait;

class Page implements FunctionsPageInterface
{
    use ShopResolverTrait;

    /**
     * CONSTRUCTEUR.
     *
     * @param Shop $shop Instance de la boutique.
     *
     * @return void
     */
    public function __construct(Shop $shop)
    {
        $this->shop = $shop;
    }

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function isCart()
    {
        return \is_single($this->settings()->cartPageId());
    }

    /**
     * {@inheritdoc}
     */
    public function isCheckout()
    {
        return \is_single($this->settings()->checkoutPageId());
    }

    /**
     * {@inheritdoc}
     */
    public function isShop()
    {
        return \is_single($this->settings()->shopPageId());
    }

    /**
     * {@inheritdoc}
     */
    public function isTerms()
    {
        return \is_single($this->settings()->termsPageId());
    }
}