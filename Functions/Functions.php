<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop\Functions;

use tiFy\Plugins\Shop\Contracts\{
    Functions as FunctionsContract,
    DateFunctions,
    PageFunctions,
    PriceFunctions
};
use tiFy\Plugins\Shop\ShopAwareTrait;

class Functions implements FunctionsContract
{
    use ShopAwareTrait;

    /**
     * @inheritDoc
     */
    public function date($time = 'now', $timezone = true): DateFunctions
    {
        /** @var DateFunctions $date */
        $date = $this->shop()->resolve('functions.date');

        return $date;
    }

    /**
     * @inheritDoc
     */
    public function page(): PageFunctions
    {
        return $this->shop()->resolve('functions.page');
    }

    /**
     * @inheritDoc
     */
    public function price(): PriceFunctions
    {
        return $this->shop()->resolve('functions.price');
    }
}