<?php
/**
 * Edition de produit - Produits associés.
 * ---------------------------------------------------------------------------------------------------------------------
 * @var tiFy\Contracts\View\ViewController $this
 * @var \WP_Post $post
 * @var tiFy\Plugins\Shop\Products\ProductItem $product
 */
?>
<?php if ($product->isProductType('grouped')) : ?>
    <table class="form-table">
        <tbody>
        <tr>
            <th>
                <?php echo field('label', [
                    'content' => __('Produits groupés', 'tify'),
                ]); ?>
            </th>
            <td>
                <?php echo field('select-js', [
                    'name'         => '_grouped_products',
                    'value'        => get_post_meta($post->ID, '_grouped_products', true),
                    'multiple'     => true,
                    'autocomplete' => true,
                    'source'       => [
                        'query_args' => [
                            'post_type'    => (string)$product->getProductObjectType(),
                            'post__not_in' => [$product->getId()],
                        ],
                    ],
                ]); ?>
            </td>
        </tr>
        </tbody>
    </table>

    <hr>
<?php elseif ($product->isProductType('composed')) : ?>
    <table class="form-table">
        <tbody>
        <tr>
            <th>
                <?php echo field('label', [
                    'content' => __('Composition de produits', 'tify'),
                ]); ?>
            </th>
            <td>
                <?php echo field('select-js', [
                    'name'         => '_composition_products',
                    'value'        => [],//get_post_meta($post->ID, '_composition_products', true),
                    'multiple'     => true,
                    'autocomplete' => true,
                    'source'       => [
                        'query_args' => [
                            'post_type'    => (string)$product->getProductObjectType(),
                            'post__not_in' => [$product->getId()],
                        ],
                    ],
                ]); ?>
            </td>
        </tr>
        </tbody>
    </table>
    <hr>
<?php endif; ?>

<table class="form-table">
    <tbody>
    <tr>
        <th>
            <?php echo field('label', [
                'content' => __('Montée en gamme', 'tify'),
            ]); ?>
        </th>
        <td>
            <?php echo field('select-js', [
                'name'         => '_upsell_ids',
                'value'        => get_post_meta($post->ID, '_upsell_ids', true),
                'multiple'     => true,
                'autocomplete' => true,
                'source'       => [
                    'query_args' => [
                        'post_type'    => (string)$product->getProductObjectType(),
                        'post__not_in' => [$product->getId()],
                    ],
                ],
            ]); ?>
        </td>
    </tr>
    </tbody>
</table>
<hr>
<table class="form-table">
    <tbody>
    <tr>
        <th>
            <?php echo field('label', [
                'content' => __('Ventes croisées', 'tify'),
            ]); ?>
        </th>
        <td>
            <?php echo field('select-js', [
                'name'         => '_crosssell_ids',
                'value'        => get_post_meta($post->ID, '_crosssell_ids', true),
                'multiple'     => true,
                'autocomplete' => true,
                'source'       => [
                    'query_args' => [
                        'post_type'    => (string)$product->getProductObjectType(),
                        'post__not_in' => [$product->getId()],
                    ],
                ],
            ]); ?>
        </td>
    </tr>
    </tbody>
</table>
<hr>
<table class="form-table">
    <tbody>
    <tr>
        <th>
            <?php  echo field('label', [
                'content' => __('Produits en relation', 'tify'),
            ]); ?>
        </th>
        <td>
            <?php echo field('select-js', [
                'name'         => '_related_ids',
                'value'        => get_post_meta($post->ID, '_related_ids', true),
                'multiple'     => true,
                'autocomplete' => true,
                'source'       => [
                    'query_args' => [
                        'post_type'    => (string)$product->getProductObjectType(),
                        'post__not_in' => [$product->getId()],
                    ],
                ],
            ]); ?>
        </td>
    </tr>
    </tbody>
</table>