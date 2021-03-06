<?php
/**
 * Edition de produit - Livraison.
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
                'content' => __('Poids (kg)', 'tify'),
            ]); ?>
        </th>
        <td>
            <?php echo field('text', [
                'name'  => '_weight',
                'value' => get_post_meta($post->ID, '_weight', true),
            ]); ?>
        </td>
    </tr>
    <tr>
        <th>
            <?php echo field('label', [
                'content' => __('Dimensions (cm)', 'tify'),
            ]); ?>
        </th>
        <td>
            <?php echo field('text', [
                'name'  => '_length',
                'value' => get_post_meta($post->ID, '_length', true),
            ]); ?>
            <?php echo field('text', [
                'name'  => '_width',
                'value' => get_post_meta($post->ID, '_width', true),
            ]); ?>
            <?php echo field('text', [
                'name'  => '_height',
                'value' => get_post_meta($post->ID, '_height', true),
            ]); ?>
        </td>
    </tr>
    </tbody>
</table>