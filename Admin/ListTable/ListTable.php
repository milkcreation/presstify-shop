<?php

namespace tiFy\Plugins\Shop\Admin\ListTable;

use tiFy\Contracts\Column\Column;
use tiFy\PostType\Column\PostThumbnail\PostThumbnail;
use tiFy\Plugins\Shop\Contracts\ProductObjectType;
use tiFy\Plugins\Shop\Products\ObjectType\Categorized;
use tiFy\Plugins\Shop\Products\ObjectType\Uncategorized;
use tiFy\Plugins\Shop\Shop;
use tiFy\Plugins\Shop\ShopResolverTrait;

class ListTable
{
    use ShopResolverTrait;

    /**
     * Nom de qualification du type de post associé.
     * @var string
     */
    private $objectName = '';

    /**
     * Instance du type de produit.
     * @var Categorized|Uncategorized
     */
    private $objectType;

    /**
     * CONSTRUCTEUR.
     *
     * @param ProductObjectType $object_type Instance du type de produit.
     * @param Shop $shop Attributs de configuration.
     *
     * @return void
     */
    public function __construct(ProductObjectType $object_type, Shop $shop)
    {
        $this->shop = $shop;
        $this->objectType = $object_type;
        $this->objectName = $this->objectType->getName();

        /** @var Column $column */
        $column = app('column');
        $column
            ->add(
                "{$this->objectName}@post_type",
                'thumb',
                [
                    'position' => 1,
                    'content'  => PostThumbnail::class
                ]
            )
            ->add(
                "{$this->objectName}@post_type",
                'sku',
                [
                    'title'    => __('UGS', 'tify'),
                    'position' => 3,
                    'content'  => [$this, 'columnSku']
                ]
            )
            ->add(
                "{$this->objectName}@post_type",
                'price',
                [
                    'title'    => __('Prix', 'tify'),
                    'position' => 4,
                    'content'  => [$this, 'columnPrice']
                ]
            )
            ->add(
                "{$this->objectName}@post_type",
                'featured',
                [
                    'title'    => "<span class=\"dashicons dashicons-star-half\"></span>",
                    'position' => 5,
                    'content'  => [$this, 'columnFeatured']
                ]
            )
            ->add(
                "{$this->objectName}@post_type",
                'product_type',
                [
                    'title'    => __('Type', 'tify'),
                    'position' => 6,
                    'content'  => [$this, 'columnProductType']
                ]
            );
    }

    /**
     * Contenu des éléments de la colonne "Unité de Gestion de Stock" (sku).
     *
     * @param string $column_name Identifiant de qualification de la colonne.
     * @param int $post_id Identifiant du contenu.
     *
     * @return void
     */
    public function columnSku($column_name, $post_id)
    {
        echo get_post_meta($post_id, '_sku', true);
    }

    /**
     * Contenu des éléments de la colonne "Prix".
     *
     * @param string $column_name Identifiant de qualification de la colonne.
     * @param int $post_id Identifiant du contenu.
     *
     * @return void
     */
    public function columnPrice($column_name, $post_id)
    {
        echo ($price = get_post_meta($post_id, '_regular_price', true))
            ? $this->functions()->price()->html($price)
            : '--';
    }

    /**
     * Contenu des éléments de la colonne "Mise en avant".
     *
     * @param string $column_name Identifiant de qualification de la colonne.
     * @param int $post_id Identifiant du contenu.
     *
     * @return void
     */
    public function columnFeatured($column_name, $post_id)
    {
        $product = $this->shop->products()->getItem($post_id);

        echo $product->isFeatured()
            ? "<span class=\"dashicons dashicons-star-filled\"></span>"
            : "<span class=\"dashicons dashicons-star-empty\"></span>";
    }

    /**
     * Contenu des éléments de la colonne "Type de produit".
     *
     * @param string $column_name Identifiant de qualification de la colonne.
     * @param int $post_id Identifiant du contenu.
     *
     * @return void
     */
    public function columnProductType($column_name, $post_id)
    {
        $product = $this->shop->products()->getItem($post_id);

        echo $this->shop->products()->getProductTypeIcon($product->getProductType());
    }
}