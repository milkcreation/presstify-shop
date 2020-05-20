<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop;

use tiFy\View\Factory\PlatesFactory;

class ShopViewController extends PlatesFactory
{
    /**
     * Liste des méthodes héritées.
     * @var array
     */
    protected $mixins = [];

    /**
     * Délégation d'appel des méthodes de de l'instance de la boutique.
     *
     * @param string $name Nom de la méthode à appeler.
     * @param array $arguments Liste des variables passées en argument.
     *
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        if (in_array($name, $this->mixins)) {
            return call_user_func_array([$this->engine->params('shop'), $name], $arguments);
        }

        return null;
    }
}