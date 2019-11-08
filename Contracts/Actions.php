<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop\Contracts;

interface Actions extends BootableControllerInterface, ShopAwareTrait
{
    /**
     * Récupération de l'url d'une action de traitement.
     *
     * @param string $alias Alias de qualification de l'action.
     * @param array $parameters Liste des variables passées en argument dans l'url.
     * @param boolean $absolute Format de sortie de l'url. Url relative par défaut.
     *
     * @return string
     */
    public function url($alias, $parameters = [], $absolute = false): string;
}