<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop\Contracts;

use tiFy\Contracts\Support\DateTime;

interface FunctionsDate extends DateTime, ShopAwareTrait
{
    /**
     * Récupération de la date pour un format donné.
     *
     * @param string $format Format d'affichage de la date. MySQL par défaut.
     *
     * @return string
     */
    public function get($format = null);

    /**
     * Récupération de la date basée sur le temps universel pour un format donné.
     *
     * @param string $format Format d'affichage de la date. MySQL par défaut.
     *
     * @return string
     */
    public function utc($format = null);
}