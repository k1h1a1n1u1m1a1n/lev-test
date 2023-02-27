<?php
require_once "ProductsSelection.php";

$bought_together = ProductsSelection::getBoughtTogetherProducts();
$suggested_cart_products = ProductsSelection::getSuggestedCartProducts();
?>
    <div class="plugin-wpr">
        <div class="products-wrp">
            <?php
            if (count($bought_together) > 0) {
                ProductsSelection::drawProducts($bought_together, 'Suggested Bought Together Products', 'bought-together');
            }
            if (count($suggested_cart_products) > 0) {
                ProductsSelection::drawProducts($suggested_cart_products, 'Suggested products by cart');
            }
            if (count($bought_together) == 0 && count($suggested_cart_products) == 0) {
                ProductsSelection::drawProducts(ProductsSelection::getBestSalesProducts(), 'Popular products');
            }
            ?>
        </div>
        <?php the_widget('WC_Widget_Cart', 'title=') ?>
    </div>
<?php