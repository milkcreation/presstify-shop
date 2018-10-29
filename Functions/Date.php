<?php

/**
 * @name Date
 * @desc Controleur de gestion de dates
 * @namespace \tiFy\Plugins\Shop\Functions
 *
 * @author Jordy Manner <jordy@tigreblanc.fr>
 * @copyright Milkcreation
 */

namespace tiFy\Plugins\Shop\Functions;

use DateTime;
use DateTimeZone;
use tiFy\Plugins\Shop\Contracts\FunctionsDateInterface;
use tiFy\Plugins\Shop\Shop;
use tiFy\Plugins\Shop\ShopResolverTrait;

class Date extends DateTime implements FunctionsDateInterface
{
    use ShopResolverTrait;

    /**
     * Format de date MySql
     * @var string
     */
    const SQL = 'Y-m-d H:i:s';

    /**
     * CONSTRUCTEUR.
     *
     * @param string $time
     * @param bool|string|DateTimeZone $timezone
     * @param Shop $shop Instance de la boutique.
     *
     * @return void
     */
    public function __construct($time = 'now', $timezone = true, Shop $shop)
    {
        $this->shop = $shop;

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
     * {@inheritdoc}
     */
    public function get($format = null)
    {
        return $this->format($format ? : self::SQL);
    }

    /**
     * {@inheritdoc}
     */
    public function utc($format = null)
    {
        return (new self(null, false, $this->shop))
            ->setTimestamp($this->getTimestamp())
            ->format($format ? : self::SQL);
    }
}