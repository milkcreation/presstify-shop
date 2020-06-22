<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop\Products;

use tiFy\Plugins\Shop\Contracts\{Product, ProductPurchasingOption as ProductPurchasingOptionContract};
use tiFy\Plugins\Shop\ShopAwareTrait;
use tiFy\Support\{Arr, ParamsBag};

class ProductPurchasingOption extends ParamsBag implements ProductPurchasingOptionContract
{
    use ShopAwareTrait;

    /**
     * Nom de qualification.
     * @var string
     */
    protected $name = '';

    /**
     * Instance du controleur du produit associé.
     * @var Product|null
     */
    protected $product;

    /**
     * Clé d'indice de l'option d'achat selectionnée.
     * @var string|null
     */
    protected $selected = null;

    /**
     * CONSTRUCTEUR.
     *
     * @param string $name Nom de qualification.
     * @param array $attrs Liste des attributs de configuration.
     * @param Product $product Instance du produit associé.
     *
     * @return void
     */
    public function __construct(string $name, array $attrs, Product $product)
    {
        $this->name = $name;
        $this->product = $product;

        $this->setShop($this->product->shop());

        $this->set($attrs)->parse();
    }

    /**
     * @inheritDoc
     */
    public function getLabel(): string
    {
        return (string)$this->get('label', '');
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return (string)$this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function getProduct(): Product
    {
        return $this->product;
    }

    /**
     * @inheritDoc
     */
    public function getValue($default = null)
    {
        if (is_null($this->selected)) {
            return $default;
        }

        return Arr::get($this->getValueList(), $this->selected, $default);
    }

    /**
     * @inheritDoc
     */
    public function getValueList(): array
    {
        return Arr::wrap($this->get('value', []));
    }

    /**
     * @inheritDoc
     */
    public function isActive(): bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function parse(): ProductPurchasingOptionContract
    {
        parent::parse();

        $this->set([
            'field.label'      => $this->getLabel(),
            'field.args.name'  => "purchasing_options[{$this->product->getId()}][{$this->getName()}]",
            'field.args.value' => $this->getValue(),
        ]);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function renderCartLine(): string
    {
        return (string) $this->shop()->view('shop/cart/line/purchasing-option', ['option' => $this]);
    }

    /**
     * @inheritDoc
     */
    public function renderProduct(): string
    {
        return (string)$this->shop()->view('shop/product/purchasing-option', [
            'option' => $this,
            'field'  => $this->get('field'),
        ]);
    }

    /**
     * @inheritDoc
     */
    public function setSelected(string $selected): ProductPurchasingOptionContract
    {
        $this->selected = $selected;

        return $this;
    }
}