<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop\Contracts;

interface Functions extends ShopAwareTrait
{
    /**
     * Récupération d'une instance du controleur de date.
     *
     * @param string $time Date à traité. now par défaut.
     * @param boolean $timezone
     *
     * @return DateFunctions
     * @see http://php.net/manual/fr/class.datetime.php
     */
    public function date($time = 'now', $timezone = true): DateFunctions;

    /**
     * Récupération d'une instance du controleur de page.
     *
     * @return PageFunctions
     */
    public function page(): PageFunctions;

    /**
     * Récupération d'une instance du controleur des tarifs.
     *
     * @return PriceFunctions
     */
    public function price(): PriceFunctions;
}