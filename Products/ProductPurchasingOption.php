<?php


/**
 * @name ProductPurchasingOption
 * @desc Controleur de gestion d'une option d'achat
 * @package presstiFy
 * @namespace \tiFy\Plugins\Shop\Products
 * @version 1.1
 * @since 1.2.535
 *
 * @author Jordy Manner <jordy@tigreblanc.fr>
 * @copyright Milkcreation
 */

namespace tiFy\Plugins\Shop\Products;

use Illuminate\Support\Arr;
use tiFy\Plugins\Shop\ServiceProvider\ProvideTraits;
use tiFy\Plugins\Shop\ServiceProvider\ProvideTraitsInterface;
use tiFy\Plugins\Shop\Shop;

class ProductPurchasingOption implements ProvideTraitsInterface
{
    use ProvideTraits;

    /**
     * Classe de rappel de la boutique.
     * @var Shop
     */
    protected $shop;

    /**
     * Identifiant de qualification.
     * @var string
     */
    protected $name = '';

    /**
     * Classe de rappel du produit associé.
     * @var ProductItemInterface
     */
    protected $product;

    /**
     * Liste des attributs de configuration
     * @var array|mixed
     */
    protected $attributes = [];

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
        // Définition de la classe de rappel de la boutique
        $this->shop = $shop;

        // Définition de l'identification
        $this->name = $name;

        // Définition du produit associé
        if ($product instanceof ProductItemInterface) :
            $this->product = $product;
        else :
            $this->product = $this->products()->get($product);
        endif;

        // Définition des attributs de configuration
        $this->attributes = $this->product instanceof ProductItemInterface
            ? Arr::get($this->product->getMeta('_purchasing_options', true, []), $this->name, [])
            : [];
    }

    /**
     * Vérification d'existance.
     *
     * @return bool
     */
    public function exists()
    {
        return !empty($this->attributes);
    }

    /**
     * Récupération d'attribut.
     *
     * @param string $key Clé d'index de l'attribut de configuration. Syntaxe à point permise.
     * @param mixed $default Valeur de retour par defaut.
     *
     * @return mixed
     */
    public function get($key, $default = null)
    {
        return Arr::get($this->attributes, $key, $default);
    }

    /**
     * Identifiant de qualification.
     *
     * @return string
     */
    public function getName()
    {
        return (string)$this->name;
    }

    /**
     * Classe de rappel du produit associé.
     *
     * @return null|ProductItemInterface
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * Intitulé de qualification.
     *
     * @return string
     */
    public function getLabel()
    {
        return (string)$this->get('label', '');
    }

    /**
     * Intitulé de qualification.
     *
     * @return array
     */
    public function getValueList()
    {
        return (array)$this->get('value', []);
    }

    /**
     * Définition de la valeur de selection.
     *
     * @return void
     */
    public function setSelected($selected)
    {
        $this->selected = $selected;
    }

    /**
     * Récupération de la valeur de selection.
     *
     * @param mixed $default Valeur de retour par défaut
     *
     * @return void
     */
    public function getValue($default = null)
    {
        if (is_null($this->selected)) :
            return $default;
        endif;

        return Arr::get($this->getValueList(), $this->selected, $default);
    }
}