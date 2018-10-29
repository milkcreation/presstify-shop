<?php

namespace tiFy\Plugins\Shop\Contracts;

use tiFy\Contracts\PostType\PostQueryInterface;
use tiFy\Plugins\Shop\Contracts\BootableControllerInterface;
use tiFy\Plugins\Shop\Contracts\ProductItemInterface;
use tiFy\Plugins\Shop\Contracts\ProductListInterface;
use tiFy\Plugins\Shop\Contracts\ShopResolverInterface;

interface ProductsInterface extends BootableControllerInterface, PostQueryInterface, ShopResolverInterface
{
    /**
     * {@inheritdoc}
     *
     * @return array|ProductListInterface|ProductItemInterface[]
     */
    public function getCollection($query_args = []);

    /**
     * {@inheritdoc}
     *
     * @return null|ProductItemInterface
     */
    public function getItem($product = null);

    /**
     * {@inheritdoc}
     *
     * @return null|ProductItemInterface
     */
    public function getItemBy($key = 'name', $value);

    /**
     * Récupération d'une classe de rappel des types d'objet
     *
     * @param string $object_type Identifiant de qualification du type d'object (custom_post_type)
     *
     * @return ObjectTypes\Categorized|ObjectTypes\Uncategorized
     */
    public function getObjectType($object_type);

    /**
     * Récupération de la liste des classe de rappel des types d'objet
     *
     * @return ObjectTypes\Categorized[]|ObjectTypes\Uncategorized[]
     */
    public function getObjectTypeList();

    /**
     * Récupération de la liste des identifiant de qualification des gamme de produits déclarés
     *
     * @return string[]
     */
    public function getObjectTypes();

    /**
     * Récupération du nom d'affichage d'un type de produit
     *
     * @param string $product_type Identifiant de qualification d'un type de produit permis. simple|grouped|external|variable.
     *
     * @return string
     */
    public function getProductTypeDisplayName($product_type);

    /**
     * Récupération de l'icône représentative d'un type de produit
     *
     * @param string $product_type Identifiant de qualification d'un type de produit permis. simple|grouped|external|variable.
     *
     * @return string
     */
    public function getProductTypeIcon($product_type);

    /**
     * Récupération de la liste des types de produit
     *
     * @return array
     */
    public function getProductTypes();

    /**
     * Enregistrement d'un post
     *
     * @param int $post_id Identifiant de qualification du post
     * @param \WP_Post $post Objet Post Wordpress
     *
     * @return array|null|\WP_Post
     */
    public function save_post($post_id, $post);
}