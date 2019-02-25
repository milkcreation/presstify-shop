<?php

namespace tiFy\Plugins\Shop\Contracts;

interface FunctionsInterface extends BootableControllerInterface, ShopResolverInterface
{
    /**
     * Récupération d'une instance du controleur de date.
     *
     * @param string $time Date à traité. now par défaut.
     * @see http://php.net/manual/fr/class.datetime.php
     * @param boolean $timezone
     *
     * @return FunctionsDateInterface
     */
    public function date($time = 'now', $timezone = true);

    /**
     * Récupération d'une instance du controleur de page.
     *
     * @return FunctionsPageInterface
     */
    public function page();

    /**
     * Récupération d'une instance du controleur des tarifs.
     *
     * @return FunctionsPriceInterface
     */
    public function price();

    /**
     * Récupération d'une instance du controleur des url de la boutique.
     *
     * @return FunctionsUrlInterface
     */
    public function url();
}