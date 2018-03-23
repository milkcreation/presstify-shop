<?php

namespace tiFy\Plugins\Shop\Functions;

interface PriceInterface
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