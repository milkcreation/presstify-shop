<?php

/**
 * @name Date
 * @desc Controleur de gestion de dates
 * @namespace \tiFy\Plugins\Shop\Functions
 * @package presstify-plugins/shop
 * @version 1.0.2
 *
 * @author Jordy Manner <jordy@tigreblanc.fr>
 * @copyright Milkcreation
 */

namespace tiFy\Plugins\Shop\Functions;

use DateTime;
use DateTimeZone;
use tiFy\Plugins\Shop\ServiceProvider\ProvideTraits;
use tiFy\Plugins\Shop\ServiceProvider\ProvideTraitsInterface;
use tiFy\Plugins\Shop\Shop;

class Date extends DateTime implements DateInterface, ProvideTraitsInterface
{
    use ProvideTraits;

    /**
     * Classe de rappel de la boutique
     * @var Shop
     */
    protected $shop;

    /**
     * Format de date MySql
     * @var string
     */
    const SQL = 'Y-m-d H:i:s';

    /**
     * CONSTRUCTEUR
     *
     * @param string $time
     * @param bool|string|DateTimeZone $timezone
     * @param Shop $shop
     *
     * @return void
     */
    public function __construct($time = 'now', $timezone = true, Shop $shop)
    {
        // Définition de la classe de rappel de la boutique
        $this->shop = $shop;

        // Définition de la zone géographique
        if ($timezone instanceof DateTimeZone) :
        elseif ($timezone === true) :
            $timezone = new DateTimeZone(\get_option('timezone_string'));
        elseif(is_string($timezone)) :
            $timezone = new DateTimeZone($timezone);
        else :
            $timezone = null;
        endif;

        parent::__construct($time, $timezone);
    }

    /**
     * Récupére la date au format SQL.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->format(self::SQL);
    }

    /**
     * Récupération de la date.
     *
     * @param string $format Format d'affichage de la date. MySQL par default
     *
     * @return string
     */
    public function get($format = null)
    {
        return $this->format($format ? : self::SQL);
    }

    /**
     * Récupération de la date basé sur le temps universel
     *
     * @param string $format Format d'affichage de la date. MySQL par default
     *
     * @return string
     */
    public function utc($format = null)
    {
        return (new self(null, false, $this->shop))
            ->setTimestamp($this->getTimestamp())
            ->format($format ? : self::SQL);
    }
}