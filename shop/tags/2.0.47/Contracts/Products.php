<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop\Contracts;

use WP_Post;

interface Products extends ShopAwareTrait
{
    /**
     * Récupération d'une collection de produits.
     *
     * @param Product[]|array $products
     *
     * @return ProductsCollection
     */
    public function collect(array $products = []): ProductsCollection;

    /**
     * Récupération d'un produit.
     *
     * @param int|string|Wp_Post|null
     *
     * @return Product|null
     */
    public function get($id = null): ?Product;

    /**
     * Récupération d'une classe de rappel des types d'objet
     *
     * @param string $object_type Identifiant de qualification du type d'object (custom_post_type)
     *
     * @return ProductObjectType
     */
    public function getObjectType(string $object_type): ?ProductObjectType;

    /**
     * Récupération de la liste des classe de rappel des types d'objet
     *
     * @return ProductObjectType[]
     */
    public function getObjectTypeList(): array;

    /**
     * Récupération de la liste des identifiant de qualification des gamme de produits déclarés
     *
     * @return string[]
     */
    public function getObjectTypes(): array;

    /**
     * Récupération du nom d'affichage d'un type de produit
     *
     * @param string $product_type Identifiant de qualification d'un type de produit permis.
     * simple|grouped|external|variable.
     *
     * @return string
     */
    public function getProductTypeDisplayName(string $product_type): string;

    /**
     * Récupération de l'icône représentative d'un type de produit
     *
     * @param string $product_type Identifiant de qualification d'un type de produit permis.
     * simple|grouped|external|variable.
     *
     * @return string
     */
    public function getProductTypeIcon(string $product_type): string;

    /**
     * Récupération de la liste des types de produit
     *
     * @return array
     */
    public function getProductTypes(): array;

    /**
     * Récupération d'une liste d'instance de produits selon un liste de critères de récupération.
     *
     * @param array $args Liste des critères de récupération de la liste des instances de produits.
     *
     * @return array
     */
    public function query(array $args = []): array;

    /**
     * Enregistrement d'un post
     *
     * @param int $post_id Identifiant de qualification du post
     * @param WP_Post $post Objet Post Wordpress
     *
     * @return WP_Post|array|null
     */
    public function saveWpPost(int $post_id, WP_Post $post);
}