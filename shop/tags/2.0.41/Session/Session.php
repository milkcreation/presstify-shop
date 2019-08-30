<?php

namespace tiFy\Plugins\Shop\Session;

use BadMethodCallException;
use Exception;
use tiFy\Contracts\User\SessionStore;
use tiFy\Plugins\Shop\AbstractShopSingleton;
use tiFy\Plugins\Shop\Contracts\SessionInterface;

/**
 * Class Session
 *
 * @desc Gestion des données portées par la session.
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
        $this->store = user()->session()->register('tify_shop')->get('tify_shop');
    }

    /**
     * Délégation d'appel des méthodes du controleur de données de session associé.
     *
     * @param string $name Nom de la méthode à appeler.
     * @param array $arguments Liste des variables passées en argument.
     *
     * @return mixed
     *
     * @throws BadMethodCallException
     */
    public function __call($name, $arguments)
    {
        try {
            return $this->store->$name(...$arguments);
        } catch (Exception $e) {
            throw new BadMethodCallException(sprintf(__('La méthode %s n\'est pas disponible.', 'tify'), $name));
        }
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