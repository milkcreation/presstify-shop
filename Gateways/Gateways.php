<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop\Gateways;

use tiFy\Plugins\Shop\{
    Concerns\ShopAwareTrait,
    Contracts\GatewayInterface,
    Contracts\GatewaysInterface,
};
use tiFy\Support\Collection;

/**
 * Gestion des plateformes de paiement.
 */
class Gateways extends Collection implements GatewaysInterface
{
    use ShopAwareTrait;

    /**
     * @inheritDoc
     */
    public function boot(): void
    {
        $this->_register();
    }

    /**
     * Définition de la liste des plateformes de paiement déclarées.
     *
     * @return void
     */
    private function _register()
    {
        events()->trigger('tify.plugins.shop.gateways.register', [&$this]);

        $this->set($this->shop->config("gateways", []));
    }

    /**
     * {@inheritDoc}
     *
     * @return GatewaysInterface[]
     */
    public function all(): array
    {
        return parent::all();
    }

    /**
     * {@inheritDoc}
     *
     * @return GatewayInterface[]
     */
    public function available(): array
    {
        $filtered = $this->collect()->filter(function(GatewayInterface $item){
            return $item->isAvailable();
        });

        return $filtered->all();
    }

    /**
     * {@inheritDoc}
     *
     * @param string $id
     *
     * @return GatewayInterface
     */
    public function get($id): ?GatewayInterface
    {
        return is_string($id) ? parent::get($id): null;
    }

    /**
     * {@inheritDoc}
     *
     * @param GatewayInterface|array|string $gateway
     * @param string|null $alias
     *
     * @return GatewayInterface
     */
    public function walk($gateway, $alias = null): ?GatewayInterface
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

        if ($gateway instanceof GatewayInterface) {
            $gateway->setShop($this->shop)
                ->set($attrs)->parse()
                ->setEnabled($enabled)
                ->boot();

            return $this->items[$alias] = $gateway;
        } else {
            return null;
        }
    }
}