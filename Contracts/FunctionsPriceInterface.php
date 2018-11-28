<?php

namespace tiFy\Plugins\Shop\Contracts;

interface FunctionsPriceInterface
{
    /**
     * Récupération du symbole de la devise.
     * @todo https://github.com/xsolla/currency-format
     *
     * @param string $currency Identifiant de qualification de la devise.
     *
     * @return string
     */
    public function currencySymbol($currency = '');

    /**
     * Prix d'affichage des pages HTML.
     *
     * @param float $price Montant à afficher.
     * @param string $format d'affichage.
     *
     * @return string
     */
    public function html($price, $format = '');
}