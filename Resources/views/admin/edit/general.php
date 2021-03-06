<?php
/**
 * Edition de produit - Options générales.
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
                'content' => __('Mettre en avant', 'tify'),
            ]); ?>
        </th>
        <td>
            <?php echo field('toggle-switch', [
                'name'  => '_featured',
                'value' => $product->isFeatured() ? 'on' : 'off',
            ]); ?>
        </td>
    </tr>
    <tr>
        <th>
            <?php echo field('label', [
                'content' => __('Tarif régulier (€)', 'tify'),
            ]); ?>
        </th>
        <td>
            <?php echo field('text', [
                'name'  => '_regular_price',
                'value' => get_post_meta($post->ID, '_regular_price', true),
            ]); ?>
        </td>
    </tr>
    </tbody>
</table>