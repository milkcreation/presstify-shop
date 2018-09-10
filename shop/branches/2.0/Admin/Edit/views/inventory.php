<?php
/**
 * @var \WP_Post $post
 * @var \tiFy\Plugins\Shop\Products\ProductItemInterface $product
 */
?>

<table class="form-table">
    <tbody>
        <tr>
            <th>
            <?php
                echo tify_field_label(
                    [
                        'content' => __('UGS', 'tify')
                    ]
                );
            ?>
            </th>
            <td>
            <?php
                echo tify_field_text(
                    [
                        'name' => '_sku',
                        'value' => get_post_meta($post->ID, '_sku', true)
                    ]
                );
            ?>
            </td>
        </tr>
    </tbody>
</table>
