<?php

namespace tiFy\Plugins\Shop\Providers;

interface PriceProviderInterface
{
    /**
     * Prix d'affichage des pages HTML
     *
     * @param float $price Montant à afficher
     * @param string $format d'affichage
     *
     * @return string
     */
    public function html($price, $format = '');
}