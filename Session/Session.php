<?php

/**
 * @name \tiFy\Plugins\Shop\Session\Session
 * @desc Gestion des données portées par la session.
 *
 * @author Jordy Manner <jordy@tigreblanc.fr>
 * @copyright Milkcreation
 */

namespace tiFy\Plugins\Shop\Session;

use tiFy\Contracts\User\SessionManager;
use tiFy\Contracts\User\SessionStore;
use tiFy\Plugins\Shop\AbstractShopSingleton;
use tiFy\Plugins\Shop\Contracts\SessionInterface;

/**
 * Class Session
 * @package tiFy\Plugins\Shop\Session
 *
 * @mixin SessionStore
 */
class Session extends AbstractShopSingleton implements SessionInterface
{
    /**
     * Instance du traitement de la session.
     * @var SessionStore
     */
    public $store;

    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        events()->listen(
            'user.session.register',
            function (SessionManager $session) {
                $this->store = $session->register('tify_shop');
            }
        );
    }

    /**
     * Appel des dynamique des méthodes.
     *
     * @param string $name
     * @param array $args
     *
     * @return mixed
     */
    public function __call($name, $args)
    {
        if (method_exists($this->store, $name)) :
            return call_user_func_array([$this->store, $name], $args);
        endif;
    }

    /**
     * Appel de la classe responsable du traitement à l'invocation de la classe.
     *
     * @return SessionStore
     */
    public function __invoke()
    {
        return $this->store;
    }
}