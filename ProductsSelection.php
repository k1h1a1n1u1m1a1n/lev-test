<?php

class ProductsSelection
{
    public static function getBestSalesProducts()
    {
        $loop = new WP_Query([
            'post_type' => 'product',
            'meta_key' => 'total_sales',
            'orderby' => 'meta_value_num',
            'posts_per_page' => 5,
        ]);
        wp_reset_query();
        return wp_list_pluck($loop->posts, 'ID');
    }

    public static function getBoughtTogetherProducts()
    {
        global $wpdb;
        $query = "SELECT order_1.product_id, order_2.product_id AS product_id_2, COUNT(*) AS purchase_frequency
                    FROM `{$wpdb->prefix}wc_order_product_lookup` order_1
                    INNER JOIN `{$wpdb->prefix}wc_order_product_lookup` order_2 ON order_1.order_id = order_2.order_id
                    AND order_1.product_id < order_2.product_id
                    GROUP BY order_1.product_id,
                             order_2.product_id
                    ORDER BY purchase_frequency DESC
                    LIMIT 3";
        $data = array_values($wpdb->get_results($query));
        $ids = [];
        foreach ($data as $id) {
            $ids[] = $id->product_id;
            $ids[] = $id->product_id_2;
        }
        return $ids;
    }

    public static function getSuggestedCartProducts()
    {
        global $wpdb;
        $cart_items = [];
        foreach (WC()->cart->get_cart() as $cart_item) {
            $cart_items[] = $cart_item['product_id'];
        }
        $cart_items = implode(', ', $cart_items);
        $query = $wpdb->prepare("
        SELECT product_id
        FROM `{$wpdb->prefix}wc_order_product_lookup`
        WHERE order_id IN
            (SELECT order_id
             FROM `{$wpdb->prefix}wc_order_product_lookup`
             WHERE product_id IN (%s))
          AND product_id NOT IN (%s)", $cart_items, $cart_items);
        $data = array_values($wpdb->get_results($query));
        $ids = [];
        foreach ($data as $id) {
            $ids[] = $id->product_id;
        }
        return $ids;
    }

    public static function drawProducts($ids, $title, $classes = '')
    {
        global $post;
        echo '<h2>' . $title . '</h2>';
        echo '<ul class="products ' . $classes . '">';
        foreach ($ids as $id) {
            $post = get_post($id, OBJECT);
            setup_postdata($post);
            wc_get_template_part('content', 'product');
        }
        echo '</ul>';
        wp_reset_postdata();
    }
}