<?php

namespace tiFy\Plugins\Shop\Contracts;

use Illuminate\Support\Arr;
use tiFy\Contracts\Kernel\ParamsBag;
use tiFy\PostType\PostType;
use tiFy\PostType\Metadata\Post as MetadataPost;

interface ProductObjectType extends ParamsBag, ShopResolverInterface
{
    /**
     * Récupération du type de produit par défaut
     *
     * @return string
     */
    public function getDefaultProductType();

    /**
     * Récupération du nom de qualification.
     *
     * @return string
     */
    public function getName();

    /**
     * Récupération du nom de qualification du controleur d'élément.
     *
     * @return callable
     */
    public function getItemController();

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
}