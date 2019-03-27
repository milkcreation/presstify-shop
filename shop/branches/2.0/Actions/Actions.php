<?php

namespace tiFy\Plugins\Shop\Actions;

use tiFy\Contracts\Routing\Route;
use tiFy\Plugins\Shop\Contracts\Actions as ActionsContract;
use tiFy\Plugins\Shop\AbstractShopSingleton;
use Zend\Diactoros\Response\RedirectResponse;

class Actions extends AbstractShopSingleton implements ActionsContract
{
    /**
     * Liste des éléments déclarés.
     * @return Route[]
     */
    protected $items = [];

    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        // Paiement - Traitement de la commande
        $this->items['checkout.process'] = router()->post('/shop/checkout/process', [$this->checkout(), 'process']);

        // Commandes - Validation de paiement
        $this->items['order.payment_complete'] = router()->post('/shop/order/payment_complete/{order_id:number}',
            function ($order_id) {
                if (is_user_logged_in() && ($user = $this->users()->getItem())) :
                    if($user->isShopManager() && ($order = $this->orders()->getItem($order_id))) :
                        $order->paymentComplete();
                    endif;

                    $location = request()->get('_wp_http_referer')
                        ? : (request()->headers->get('referer') ? : home_url('/'));

                    return new RedirectResponse($location);
                else :
                    wp_die(
                        __('Votre utilisateur n\'est pas habilité à effectuer cette action', 'tify'),
                        __('Mise à jour de la commande impossible', 'tify'),
                        500
                    );
                    return '';
                endif;
            }
        );
    }

    /**
     * {@inheritdoc}
     */
    public function url($alias, $parameters = [], $absolute = false)
    {
        if (isset($this->items[$alias])) :
            return $this->items[$alias]->getUrl($parameters, $absolute);
        else :
            return '';
        endif;
    }
}