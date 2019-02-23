<?php

namespace tiFy\Plugins\Shop\Products;

use Illuminate\Support\Arr;
use tiFy\Kernel\Params\ParamsBag;
use tiFy\Plugins\Shop\Contracts\ProductItemInterface;
use tiFy\Plugins\Shop\Contracts\ProductPurchasingOption as ProductPurchasingOptionContract;
use tiFy\Plugins\Shop\Shop;
use tiFy\Plugins\Shop\ShopResolverTrait;

/**
 * Class ProductPurchasingOption
 *
 * @desc Controleur de gestion d'une option d'achat.
 */
class ProductPurchasingOption extends ParamsBag implements ProductPurchasingOptionContract
{
    use ShopResolverTrait;

    /**
     * Nom de qualification.
     * @var string
     */
    protected $name = '';

    /**
     * Instance du controleur du produit associé.
     * @var ProductItemInterface
     */
    protected $product;

    /**
     * Clé d'indice de l'option d'achat selectionnée.
     * @var string
     */
    protected $selected = null;

    /**
     * CONSTRUCTEUR.
     *
     * @param string $name Nom de qualification.
     * @param array $attrs Liste des attributs de configuration.
     * @param int|ProductItemInterface $product Identifiant de qualification ou instance du produit associé.
     * @param Shop $shop Instance de la boutique.
     *
     * @return void
     */
    public function __construct($name, $attrs = [], $product, Shop $shop)
    {
        $this->name = $name;
        $this->shop = $shop;

        $this->product = $product instanceof ProductItemInterface
            ? $product
            : $this->products()->getItem($product);

        parent::__construct($attrs);
    }

    /**
     * {@inheritdoc}
     */
    public function getLabel()
    {
        return (string)$this->get('label', '');
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return (string)$this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * {@inheritdoc}
     */
    public function getValue($default = null)
    {
        if (is_null($this->selected)) :
            return $default;
        endif;

        return Arr::get($this->getValueList(), $this->selected, $default);
    }

    /**
     * {@inheritdoc}
     */
    public function getValueList()
    {
        return Arr::wrap($this->get('value', []));
    }

    /**
     * {@inheritdoc}
     */
    public function isActive()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function parse($attrs = [])
    {
        parent::parse($attrs);

        $this->set('field.label', $this->getLabel());
        $this->set('field.args.name', "purchasing_options[{$this->product->getId()}][{$this->getName()}]");
        $this->set('field.args.value', $this->getValue());
    }

    /**
     * {@inheritdoc}
     */
    public function renderCartLine()
    {
        return $this->viewer('shop/cart/line/purchasing-option', ['option' => $this]);
    }

    /**
     * {@inheritdoc}
     */
    public function renderProduct()
    {
        return $this->viewer(
            'shop/product/purchasing-option',
            [
                'option' => $this,
                'field' => $this->get('field')
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function setSelected($selected)
    {
        $this->selected = $selected;
    }
}