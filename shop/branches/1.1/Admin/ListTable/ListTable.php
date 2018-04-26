<?php

namespace tiFy\Plugins\Shop\Admin\ListTable;

use tiFy\App\Traits\App as TraitsApp;
use tiFy\Components\Columns\PostType\PostThumbnail;
use tiFy\Core\Column\Column;
use tiFy\Plugins\Shop\Shop;
use tiFy\Plugins\Shop\Products\ObjectTypes\Factory as ObjectTypesFactory;

class ListTable
{
    use TraitsApp;

    /**
     * Classe de rappel de la boutique
     * @var Shop
     */
    protected $shop;

    /**
     * Classe de rappel d'un produit
     * @var \tiFy\Plugins\Shop\Products\ObjectTypes\Categorized|\tiFy\Plugins\Shop\Products\ObjectTypes\Uncategorized
     */
    private $objectType;

    /**
     * CONSTRUCTEUR
     *
     * @param string $id Identifiant de qualification du type de post du produit
     * @param array $attrs Attributs de configuration
     *
     * @return void
     */
    public function __construct(Shop $shop, ObjectTypesFactory $ObjectType)
    {
        // Définition de la classe de rappel de la boutique
        $this->shop = $shop;
        
        $this->objectType = $ObjectType;

        // Définition des colonnes
        Column::make('post_type', (string)$this->objectType)
            ->add(
                'thumb',
                new PostThumbnail(
                    [
                        'position'  => 1
                    ]
                )
            )
            ->add(
                'sku',
                [
                    'title'     => __('UGS', 'tify'),
                    'position'  => 3,
                    'content'   => [$this, 'columnSku']
                ]
            )
            ->add(
                'price',
                [
                    'title'     => __('Prix', 'tify'),
                    'position'  => 4,
                    'content'   => [$this, 'columnPrice']
                ]
            )
            ->add(
                'featured',
                [
                    'title'     => "<span class=\"dashicons dashicons-star-half\"></span>",
                    'position'  => 5,
                    'content'   => [$this, 'columnFeatured']
                ]
            )
            ->add(
                'product_type',
                [
                    'title'     => __('Type', 'tify'),
                    'position'  => 6,
                    'content'   => [$this, 'columnProductType']
                ]
            );
    }

    /**
     * Contenu des éléments de la colonne "Unité de Gestion de Stock" (sku
     *
     * @param string $column_name Identifiant de qualification de la colonne
     * @param int $post_id Identifiant du contenu
     *
     * @return void
     */
    public function columnSku($column_name, $post_id)
    {
        echo get_post_meta($post_id, '_sku', true);
    }

    /**
     * Contenu des éléments de la colonne "Prix"
     *
     * @param string $column_name Identifiant de qualification de la colonne
     * @param int $post_id Identifiant du contenu
     *
     * @return void
     */
    public function columnPrice($column_name, $post_id)
    {
        echo get_post_meta($post_id, '_regular_price', true);
    }

    /**
     * Contenu des éléments de la colonne "Mise en avant"
     *
     * @param string $column_name Identifiant de qualification de la colonne
     * @param int $post_id Identifiant du contenu
     *
     * @return void
     */
    public function columnFeatured($column_name, $post_id)
    {
        $product = $this->shop->products()->get($post_id);

        echo $product->isFeatured()
            ? "<span class=\"dashicons dashicons-star-filled\"></span>"
            : "<span class=\"dashicons dashicons-star-empty\"></span>";
    }

    /**
     * Contenu des éléments de la colonne "Type de produit"
     *
     * @param string $column_name Identifiant de qualification de la colonne
     * @param int $post_id Identifiant du contenu
     *
     * @return void
     */
    public function columnProductType($column_name, $post_id)
    {
        $product = $this->shop->products()->get($post_id);

        echo $this->shop->products()->getProductTypeIcon($product->getProductType());
    }
}