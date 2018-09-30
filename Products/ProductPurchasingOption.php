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
use tiFy\Plugins\Shop\Contracts\ProductItemInterface;
use tiFy\Plugins\Shop\Contracts\ProductPurchasingOptionInterface;
use tiFy\Plugins\Shop\Shop;
use tiFy\Plugins\Shop\ShopResolverTrait;

class ProductPurchasingOption implements ProductPurchasingOptionInterface
{
    use ShopResolverTrait;

    /**
     * Liste des attributs de configuration
     * @var array|mixed
     */
    protected $attributes = [];

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

        if ($product instanceof ProductItemInterface) :
            $this->product = $product;
        else :
            $this->product = $this->products()->getItem($product);
        endif;

        $this->attributes = $this->product instanceof ProductItemInterface
            ? Arr::get($this->product->getMeta('_purchasing_options', true, []), $this->name, [])
            : [];
    }

    /**
     * {@inheritdoc}
     */
    public function exists()
    {
        return !empty($this->attributes);
    }

    /**
     * {@inheritdoc}
     */
    public function get($key, $default = null)
    {
        return Arr::get($this->attributes, $key, $default);
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
    public function setSelected($selected)
    {
        $this->selected = $selected;
    }
}