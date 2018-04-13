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
                        'content' => __('Poids (kg)', 'tify')
                    ]
                );
            ?>
            </th>
            <td>
            <?php
                echo tify_field_text(
                    [
                        'name' => '_weight',
                        'value' => get_post_meta($post->ID, '_weight', true)
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
                        'content' => __('Dimensions (cm)', 'tify')
                    ]
                );
            ?>
            </th>
            <td>
            <?php
                echo tify_field_text(
                    [
                        'name' => '_length',
                        'value' => get_post_meta($post->ID, '_length', true)
                    ]
                );
            ?>
            <?php
                echo tify_field_text(
                    [
                        'name' => '_width',
                        'value' => get_post_meta($post->ID, '_width', true)
                    ],
                    true
                );
            ?>
            <?php
                echo tify_field_text(
                    [
                        'name' => '_height',
                        'value' => get_post_meta($post->ID, '_height', true)
                    ]
                );
            ?>
            </td>
        </tr>
    </tbody>
</table>
