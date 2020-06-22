<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop\Gateways;

use tiFy\Plugins\Shop\Contracts\{Gateway as GatewayContract, Gateways as GatewaysContract};
use tiFy\Plugins\Shop\ShopAwareTrait;
use tiFy\Support\Collection;

class Gateways extends Collection implements GatewaysContract
{
    use ShopAwareTrait;

    /**
     * Indicateur d'initialisation.
     * @var bool
     */
    protected $booted = false;

    /**
     * {@inheritDoc}
     *
     * @return GatewayContract[]
     */
    public function all(): array
    {
        return parent::all();
    }

    /**
     * {@inheritDoc}
     *
     * @return GatewayContract[]
     */
    public function available(): array
    {
        $filtered = $this->collect()->filter(function (GatewayContract $item) {
            return $item->isAvailable();
        });

        return $filtered->all();
    }

    /**
     * @inheritDoc
     */
    public function boot(): GatewaysContract
    {
        if (!$this->booted) {
            events()->trigger('shop.gateways.register', [$this]);

            $this->set($this->shop->config('gateways', []));

            $this->booted = true;
        }

        return $this;
    }

    /**
     * {@inheritDoc}
     *
     * @param string $id
     *
     * @return GatewayContract
     */
    public function get($id): ?GatewayContract
    {
        return is_string($id) ? parent::get($id) : null;
    }

    /**
     * {@inheritDoc}
     *
     * @param GatewayContract|array|string $gateway
     * @param string|null $alias
     *
     * @return GatewayContract
     */
    public function walk($gateway, $alias = null): ?GatewayContract
    {
        $attrs = [];
        $enabled = true;

        if (is_numeric($alias)) {
            $alias = $gateway;
            $attrs = [];
            $enabled = true;
        } elseif (is_bool($gateway)) {
            $attrs = [];
            $enabled = $gateway;
            $gateway = $alias;
        } elseif (is_array($gateway)) {
            $attrs = $gateway;
            $enabled = true;
            $gateway = $alias;
        }

        if (is_string($gateway)) {
            $gateway = $this->shop->resolve("gateway.{$gateway}");
        }

        if ($gateway instanceof GatewayContract) {
            $gateway->setShop($this->shop)
                ->set($attrs)->parse()
                ->setEnabled($enabled);

            return $this->items[$alias] = $gateway;
        } else {
            return null;
        }
    }
}