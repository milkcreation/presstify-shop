<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop\Contracts;

interface WordpressAdapter extends ShopAwareTrait
{
    /**
     * Initialisation.
     *
     * @return static
     */
    public function boot(): WordpressAdapter;
}
