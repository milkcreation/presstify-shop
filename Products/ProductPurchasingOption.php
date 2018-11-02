<?php

/**
 * @name ProductPurchasingOption
 * @desc Controleur de gestion d'une option d'achat.
 *
 * @author Jordy Manner <jordy@tigreblanc.fr>
 * @copyright Milkcreation
 */

namespace tiFy\Plugins\Shop\Products;

use Illuminate\Support\Arr;
use tiFy\Kernel\Params\ParamsBag;
use tiFy\Plugins\Shop\Contracts\ProductItemInterface;
use tiFy\Plugins\Shop\Contracts\ProductPurchasingOptionInterface;
use tiFy\Plugins\Shop\Shop;
use tiFy\Plugins\Shop\ShopResolverTrait;

class ProductPurchasingOption extends ParamsBag implements ProductPurchasingOptionInterface
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
     * @param string $name Identifiant de qualification de l'option d'achat.
     * @param int|ProductItemInterface $product Identifiant de qualification du produit|Objet produit.
     * @param Shop $shop Classe de rappel de la boutique.
     *
     * @return void
     */
    public function __construct($name, $product = null, Shop $shop)
    {
        $this->name = $name;
        $this->shop = $shop;

        $this->product = $product instanceof ProductItemInterface
            ? $product
            : $this->products()->getItem($product);

        $attrs = $this->product instanceof ProductItemInterface
            ? Arr::get($this->product->getMetaSingle('_purchasing_options', []), $this->name, [])
            : [];

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
        return (array)$this->get('value', []);
    }

    /**
     * {@inheritdoc}
     */
    public function isActive()
    {
        return !empty($this->attributes);
    }

    /**
     * {@inheritdoc}
     */
    public function setSelected($selected)
    {
        $this->selected = $selected;
    }
}