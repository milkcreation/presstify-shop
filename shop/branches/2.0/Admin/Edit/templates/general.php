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
                        'content' => __('Mettre en avant', 'theme')
                    ]
                );
                ?>
            </th>
            <td>
                <?php
                echo tify_field_toggle_switch(
                    [
                        'name'  => '_featured',
                        'value' => $product->isFeatured() ? 'on' : 'off'
                    ]
                );
                ?>
            </td>
        </tr>
        <tr>
            <th>
            <?php
                echo tify_field_label(
                    [
                        'content' => __('Tarif régulier (€)', 'tify')
                    ]
                );
            ?>
            </th>
            <td>
            <?php
                echo tify_field_text(
                    [
                        'name' => '_regular_price',
                        'value' => get_post_meta($post->ID, '_regular_price', true)
                    ]
                );
            ?>
            </td>
        </tr>
    </tbody>
</table>
