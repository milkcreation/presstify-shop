<?php

namespace tiFy\Plugins\Shop\Contracts;

use Illuminate\Support\Arr;
use tiFy\PostType\PostType;
use tiFy\PostType\Metadata\Post as MetadataPost;
use tiFy\Plugins\Shop\Contracts\ShopResolverInterface;
use tiFy\Plugins\Shop\Products\ProductItem;

interface ProductObjectType extends ShopResolverInterface
{
    /**
     * Récupération de la liste des attributs de configuration.
     *
     * @return array
     */
    public function all();

    /**
     * Liste des attributs de configuration par défaut.
     *
     * @return array
     */
    public function defaults();

    /**
     * Récupération d'un attribut de configuration.
     *
     * @param string $key Clé d'index de qualification de l'attribut.
     * @param mixed $default Valeur de retoru par defaut.
     *
     * @return mixed
     */
    public function get($key, $default = null);

    /**
     * Récupération du nom de qualification.
     *
     * @return string
     */
    public function getName();

    /**
     * Vérification d'existance d'un attribut de configuration.
     *
     * @param string $key Clé d'index de qualification de l'attribut.
     *
     * @return boolean
     */
    public function has($key);

    /**
     * Récupération de la liste des types de produit
     *
     * @return array
     */
    public function getProductTypes();

    /**
     * Vérifie s'il s'agit d'une gamme de produit unique.
     *
     * @return bool
     */
    public function hasCat();

    /**
     * Traitement de arguments de configuration.
     *
     * @param array $attrs Liste des attributs de configuration.
     *
     * @return void
     */
    public function parse($attrs = []);

    /**
     * Définition d'un attribut de configuration.
     *
     * @param $key Clé d'indice de l'attribut de configuration. Syntaxe à point permise.
     * @param mixed $value Valeur de l'attribut.
     *
     * @return $this
     */
    public function set($key, $value);
}