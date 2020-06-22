<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop\Session;

use BadMethodCallException;
use Exception;
use tiFy\Contracts\Session\Store;
use tiFy\Plugins\Shop\Contracts\Session as SessionContract;
use tiFy\Plugins\Shop\ShopAwareTrait;
use tiFy\Support\Proxy\Session as ProxySession;

class Session implements SessionContract
{
    use ShopAwareTrait;

    /**
     * Instance du traitement de la session.
     * @var Store
     */
    public $store;

    /**
     * CONSTRUCTEUR.
     *
     * @return void
     */
    public function __construct()
    {
        $this->store = ProxySession::registerStore('tify_shop');
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
    public function __call(string $name, array $arguments)
    {
        try {
            return $this->store->$name(...$arguments);
        } catch (Exception $e) {
            throw new BadMethodCallException(sprintf(__('La méthode %s n\'est pas disponible.', 'tify'), $name));
        }
    }
}