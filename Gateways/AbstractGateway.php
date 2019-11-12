<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop\Gateways;

use Psr\Log\LoggerInterface;
use tiFy\Plugins\Shop\Contracts\{Gateway as GatewayContract, Order};
use tiFy\Plugins\Shop\ShopAwareTrait;
use tiFy\Log\Logger;
use tiFy\Support\ParamsBag;

abstract class AbstractGateway extends ParamsBag implements GatewayContract
{
    use ShopAwareTrait;

    /**
     * Activation du mode de déboguage.
     * {@internal Journalisation des processus engagés, affichage des information de deboguage, ...}
     * @var bool
     */
    protected $debug = false;

    /**
     * Status d'activation de la plateforme.
     * @var bool
     */
    protected $enabled = true;

    /**
     * Identifiant de qualification de la plateforme.
     * @var string
     */
    protected $id = '';

    /**
     * Instance du gestionnaire de journalisation.
     * @var Logger|null
     */
    protected $logger;

    /**
     * @inheritDoc
     */
    public function boot(): void {}

    /**
     * @inheritDoc
     */
    public function checkoutPaymentForm(): void {}

    /**
     * @inheritDoc
     */
    public function defaults(): array
    {
        return [
            'availability'         => '',
            'choosen'              => false,
            'countries'            => [],
            'debug'                => false,
            'description'          => '',
            'has_fields'           => false,
            'icon'                 => '',
            'logger'               => [],
            'max_amount'           => 0,
            'method_description'   => '',
            'method_title'         => '',
            'order_button_text'    => '',
            'supports'             => ['products'],
            'title'                => '',
            'tokens'               => [],
            'view_transaction_url' => ''
        ];
    }

    /**
     * @inheritDoc
     */
    public function getDescription(): string
    {
        return $this->get('description', '');
    }

    /**
     * @inheritDoc
     */
    public function getIcon(): string
    {
        return $this->get('icon', '');
    }

    /**
     * @inheritDoc
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @inheritDoc
     */
    public function getMethodDescription(): string
    {
        return $this->get('method_description', '');
    }

    /**
     * @inheritDoc
     */
    public function getMethodTitle(): string
    {
        return $this->get('method_title', '');
    }

    /**
     * @inheritDoc
     */
    public function getOrderButtonText(): string
    {
        return $this->get('order_button_text', '');
    }

    /**
     * @inheritDoc
     */
    public function getReturnUrl(?Order $order = null): string
    {
        if ($order) {
            return $order->getCheckoutOrderReceivedUrl();
        } else {
            return $this->shop->functions()->url()->checkoutOrderReceivedPage(['order-received' => '']);
        }
    }

    /**
     * @inheritDoc
     */
    public function getTitle(): string
    {
        return $this->get('title', '');
    }

    /**
     * @inheritDoc
     */
    public function hasFields(): bool
    {
        return $this->get('has_fields', false);
    }

    /**
     * @inheritDoc
     */
    public function icon(): string
    {
        return $this->getIcon()
            ? '<img src="' . $this->getIcon() . '" alt="' . esc_attr($this->getTitle()) . '" />'
            : '';
    }

    /**
     * @inheritDoc
     */
    public function isAvailable(): bool
    {
        return $this->isEnabled();
    }

    /**
     * @inheritDoc
     */
    public function isChoosen(): bool
    {
        return $this->get('choosen', true);
    }

    /**
     * @inheritDoc
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * @inheritDoc
     */
    public function logger($level = null, string $message = '', array $context = []): ?LoggerInterface
    {
        if (is_null($this->logger)) {
            if ($logger = $this->get('logger', true)) {
                if (!$logger instanceof Logger) {
                    $attrs = is_array($logger) ? $logger : [];

                    $logger = (new Logger($this->getId()))->setContainer(app())->setParams($attrs);
                }
                $this->setLogger($logger);
            } else {
                return null;
            }
        }

        if(is_null($level)) {
            return $this->logger;
        } else {
            $this->logger->log($level, $message, $context);
        }
        return null;
    }

    /**
     * @inheritDoc
     */
    public function processPayment(Order $order): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function setEnabled(bool $enabled): GatewayContract
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setLogger(LoggerInterface $logger): GatewayContract
    {
        $this->logger = $logger;

        return $this;
    }
}