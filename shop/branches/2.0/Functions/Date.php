<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop\Functions;

use tiFy\Plugins\Shop\Contracts\{FunctionsDate as FunctionsDateContract, Shop};
use tiFy\Plugins\Shop\ShopAwareTrait;
use tiFy\Support\DateTime;
use Exception;

class Date extends DateTime implements FunctionsDateContract
{
    use ShopAwareTrait;

    /**
     * Format de date MySql
     * @var string
     */
    const SQL = 'Y-m-d H:i:s';

    /**
     * CONSTRUCTEUR.
     *
     * @param Shop $shop Instance de la boutique.
     *
     * @return void
     *
     * @throws Exception
     */
    public function __construct(Shop $shop)
    {
        $this->setShop($shop);

        parent::__construct();
    }

    /**
     * @inheritDoc
     */
    public function __toString(): string
    {
        return $this->format(self::SQL);
    }

    /**
     * @inheritDoc
     */
    public function get($format = null): string
    {
        return $this->format($format ?: self::SQL);
    }

    /**
     * @inheritDoc
     */
    public function utc($format = null): string
    {
        try {
            return (new static($this->shop()))->setTimestamp($this->getTimestamp())->format($format ?: self::SQL);
        } catch (Exception $e) {
            return '';
        }
    }
}