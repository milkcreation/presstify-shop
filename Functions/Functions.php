<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop\Functions;

use tiFy\Plugins\Shop\Contracts\{
    Functions as FunctionsContract,
    FunctionsDate,
    FunctionsPage,
    FunctionsPrice,
    FunctionsUrl,
    Shop
};
use tiFy\Plugins\Shop\ShopAwareTrait;

class Functions implements FunctionsContract
{
    use ShopAwareTrait;

    /**
     * CONSTRUCTEUR.
     *
     * @param Shop $shop
     *
     * @return void
     */
    public function __construct(Shop $shop)
    {
        $this->setShop($shop);

        $this->boot();
    }

    /**
     * @inheritDoc
     */
    public function boot(): void { }

    /**
     * @inheritDoc
     */
    public function date($time = 'now', $timezone = true): FunctionsDate
    {
        /** @var Date $date */
        $date = $this->shop()->resolve('functions.date');

        return $date;
    }

    /**
     * @inheritDoc
     */
    public function page(): FunctionsPage
    {
        return $this->shop()->resolve('functions.page');
    }

    /**
     * @inheritDoc
     */
    public function price(): FunctionsPrice
    {
        return $this->shop()->resolve('functions.price');
    }

    /**
     * @inheritDoc
     */
    public function url(): FunctionsUrl
    {
        return $this->shop()->resolve('functions.url');
    }
}