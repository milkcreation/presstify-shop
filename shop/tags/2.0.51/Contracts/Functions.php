<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop\Contracts;

interface Functions extends ShopAwareTrait
{
    /**
     * Instanciation de la classe.
     *
     * @return void
     */
    public function boot(): void;

    /**
     * Récupération d'une instance du controleur de date.
     *
     * @param string $time Date à traité. now par défaut.
     * @param boolean $timezone
     *
     * @return FunctionsDate
     * @see http://php.net/manual/fr/class.datetime.php
     */
    public function date($time = 'now', $timezone = true): FunctionsDate;

    /**
     * Récupération d'une instance du controleur de page.
     *
     * @return FunctionsPage
     */
    public function page(): FunctionsPage;

    /**
     * Récupération d'une instance du controleur des tarifs.
     *
     * @return FunctionsPrice
     */
    public function price(): FunctionsPrice;

    /**
     * Récupération d'une instance du controleur des url de la boutique.
     *
     * @return FunctionsUrl
     */
    public function url(): FunctionsUrl;
}