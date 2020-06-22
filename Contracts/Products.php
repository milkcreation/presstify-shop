<?php declare(strict_types=1);

namespace tiFy\Plugins\Shop\Contracts;

use WP_Post;

interface Products extends ShopAwareTrait
{
    /**
     * Initialisation.
     *
     * @return static
     */
    public function boot(): Products;

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
     * @param string $name
     *
     * @return ProductObjectType
     */
    public function getObjectType(string $name): ?ProductObjectType;

    /**
     * Récupération de la liste des noms de qualification des gammes de produits déclarées
     *
     * @return string[]
     */
    public function getObjectTypeNames(): array;

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

    /**
     * Définition d'une gamme de produit.
     *
     * @param string $alias
     * @param ProductObjectType $objectType
     *
     * @return static
     */
    public function setObjectType(string $alias, ProductObjectType $objectType): Products;
}