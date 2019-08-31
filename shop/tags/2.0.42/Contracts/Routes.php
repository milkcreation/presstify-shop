<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop\Contracts;

interface Routes extends ShopAwareTrait
{
    /**
     * @inheritDoc
     */
    public function boot(): void;
}