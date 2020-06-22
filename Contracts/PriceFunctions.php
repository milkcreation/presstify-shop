<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop\Contracts;

interface PriceFunctions extends ShopAwareTrait
{
    /**
     * Récupération du symbole de la devise.
     * @todo EVOLUTION : Utiliser la librairie suivante >>
     * @see https://github.com/xsolla/currency-format
     *
     * @param string|null $currency Identifiant de qualification de la devise.
     *
     * @return string|array
     */
    public function currencySymbol(?string $currency = null);

    /**
     * Prix d'affichage des pages HTML.
     *
     * @param float $price Montant à afficher.
     * @param string $format d'affichage.
     *
     * @return string
     */
    public function html(float $price, ?string $format = null): string;
}