<?php
/**
 * Edition de produit - Inventaire.
 * ---------------------------------------------------------------------------------------------------------------------
 * @var tiFy\Plugins\Shop\ShopViewController $this
 * @var \WP_Post $post
 * @var tiFy\Plugins\Shop\Products\Product $product
 */
?>
<table class="form-table">
    <tbody>
    <tr>
        <th>
            <?php echo field('label', [
                'content' => __('UGS', 'tify'),
            ]); ?>
        </th>
        <td>
            <?php echo field('text', [
                'name'  => '_sku',
                'value' => get_post_meta($post->ID, '_sku', true),
            ]); ?>
        </td>
    </tr>
    </tbody>
</table>
