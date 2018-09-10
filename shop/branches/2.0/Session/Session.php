<?php

/**
 * @name Session
 * @desc Gestion des données portées par la session.
 *
 * @author Jordy Manner <jordy@tigreblanc.fr>
 * @copyright Milkcreation
 */

namespace tiFy\Plugins\Shop\Session;

use tiFy\User\Session\Session as tFyUserSession;
use tiFy\User\Session\Store;
use tiFy\User\Session\StoreInterface;
use tiFy\Plugins\Shop\AbstractShopSingleton;
use tiFy\Plugins\Shop\Contracts\SessionInterface;

/**
 * Class Session
 * @package tiFy\Plugins\Shop\Session
 *
 * @mixin StoreInterface
 */
class Session extends AbstractShopSingleton implements SessionInterface
{
    /**
     * Récupération de la classe responsable du traitement des sessions.
     * @var StoreInterface
     */
    private $handler;

    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        add_action(
            'tify_user_session_register',
            function ($sessionController) {
                /** @var tFyUserSession $sessionController */
                $this->handler = $sessionController->register('tify_shop');
            },
            0
        );
    }

    /**
     *
     * @return mixed
     */
    public function __call($name, $args)
    {
        if (method_exists($this->handler, $name)) :
            return call_user_func_array([$this->handler, $name], $args);
        endif;
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