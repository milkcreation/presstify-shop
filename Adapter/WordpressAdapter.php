<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop\Adapter;

use tiFy\Plugins\Shop\Contracts\WordpressAdapter as WordpressAdapterContract;
use tiFy\Plugins\Shop\ShopAwareTrait;

class WordpressAdapter implements WordpressAdapterContract
{
    use ShopAwareTrait;

    /**
     * Indicateur d'initialisation.
     * @var bool
     */
    protected $booted = false;

    /**
     * @inheritDoc
     */
    public function boot(): WordpressAdapterContract
    {
        if (!$this->booted) {
            $this->booted = true;
        }

        return $this;
    }
}
