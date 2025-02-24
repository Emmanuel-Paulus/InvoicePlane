<script>
    $(function () {
        //$(".simple-select").select2();

        // Reset the form
        $('#product-reset-button').click(function () {
            var lookup_url = "<?php echo site_url('products/index'); ?>/";
            lookup_url += '/?rnd=' + Math.floor(Math.random() * 1000);
            lookup_url += "&reset_table=true";
            location.href = lookup_url;
        });

        // Filter on search button click
        $('#filter-button').click(function () {
            product_filter();
        });

        // Filter on family dropdown change
        $("#filter_family").change(function () {
            product_filter();
        });


        // Filter on family dropdown change
        $("#order").change(function () {
            product_filter();
        });
        
        // Filter products
        function product_filter() {
            var filter_family = $('#filter_family').val();
            var filter_product = $('#filter_product').val();
            var sortorder = $('#order').val();
            var lookup_url = "<?php echo site_url('products/index'); ?>/";
            lookup_url += '/?rnd=' + Math.floor(Math.random() * 1000);

            if (filter_product) {
                lookup_url += "&filter_product=" + filter_product;
            }

            if (filter_family) {
                lookup_url += "&filter_family=" + filter_family;
            }

            if (sortorder) {
                lookup_url += "&order=" + sortorder;
            }

            location.href = lookup_url;
        }

        // Bind enter to product search if search field is focused
        $(document).keypress(function(e){
            if (e.which === 13 && $('#filter_product').is(':focus')){
                $('#filter-button').click();
                return false;
            }
        });
    });
</script>
<div id="headerbar">
    <h1 class="headerbar-title"><?php _trans('products'); ?></h1>

    <div class="headerbar-item pull-right">
        <a class="btn btn-sm btn-primary" href="<?php echo site_url('products/form'); ?>">
            <i class="fa fa-plus"></i> <?php _trans('new'); ?>
        </a>
    </div>

    <div class="headerbar-item pull-right">
        <?php echo pager(site_url('products/index'), 'mdl_products'); ?>
    </div>


    <div class="headerbar-item pull-right visible-lg">
        <div class="btn-group btn-group-sm index-options">
            <div class="form-inline">
                <div class="form-group filter-form">
                    <select name="filter_family" id="filter_family" class="form-control simple-select">
                        <option value=""><?php _trans('any_family'); ?></option>
                        <?php foreach ($families as $family) { ?>
                            <option value="<?php echo $family->family_id; ?>"
                            <?php
                            if (isset($filter_family) && $family->family_id == $filter_family) {
                                echo ' selected="selected"';
                            }
                            ?>>
                            <?php _htmlsc($family->family_name); ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
                <div class="form-group">
                    <input type="text" class="form-control" name="filter_product" id="filter_product"
                           placeholder="<?php _trans('product_name'); ?>"
                           value="<?php echo $filter_product ?>">
                </div>
                <div class="form-group filter-form">
<?php
function option_for_select($order, $id, $name) {
    echo "<option value='".$id."'".((isset($order) && $id == $order) ? " selected='selected'" : "").">".$name."</option>\n";    
}
?>
                    <select name="order" id="order" class="form-control">
                        <option value=""><?php _trans('order'); ?></option>
                        <?php option_for_select($order, "product_id", trans("id")); ?>
                        <?php option_for_select($order, "product_name", trans("product_name")); ?>
                        <?php option_for_select($order, "family_name", trans("family_name")); ?>
                        <?php option_for_select($order, "product_description", trans("product_description")); ?>
                    </select>
                </div>
                <button type="button" id="filter-button"
                        class="btn btn-default"><?php _trans('search_product'); ?></button>
                <button type="button" id="product-reset-button" class="btn btn-default">
<?php _trans('reset'); ?>
                </button>
            </div>
        </div>
    </div>
</div>

<div id="content" class="table-content">

    <?php $this->layout->load_view('layout/alerts'); ?>

    <div class="table-responsive">
        <table class="table table-hover table-striped table-picture">

            <thead>
            <tr>
                <th><?php _trans('product_name'); ?></th>
                <th><?php _trans('family'); ?></th>
                <th><?php _trans('product_sku'); ?></th>
                <th><?php _trans('product_description'); ?></th>
                <th><?php _trans('product_price'); ?></th>
                <th><?php _trans('product_unit'); ?></th>
                <th><?php _trans('tax_rate'); ?></th>
                <?php if (get_setting('sumex')) : ?>
                    <th><?php _trans('product_tariff'); ?></th>
                <?php endif; ?>
                <th><?php _trans('options'); ?></th>
            </tr>
            </thead>

            <tbody>
            <?php foreach ($products as $product) { ?>
                <tr>
                    <td><?php _htmlsc($product->product_name); ?></td>
                    <td><?php _htmlsc($product->family_name); ?>
                        <?php echo $this->mdl_pictures->htmlpicture($product->picture_id);?>
                    </td>
                    <td><?php _htmlsc($product->product_sku); ?></td>
                    <td><?php echo nl2br(htmlsc($product->product_description)); ?></td>
                    <td class="amount"><?php echo format_currency($product->product_price); ?></td>
                    <td><?php _htmlsc($product->unit_name); ?></td>
                    <td><?php echo ($product->tax_rate_id) ? htmlsc($product->tax_rate_name) : trans('none'); ?></td>
                    <?php if (get_setting('sumex')) : ?>
                        <td><?php _htmlsc($product->product_tariff); ?></td>
                    <?php endif; ?>
                    <td>
                        <div class="options btn-group">
                            <a class="btn btn-default btn-sm dropdown-toggle"
                               data-toggle="dropdown" href="#">
                                <i class="fa fa-cog"></i> <?php _trans('options'); ?>
                            </a>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="<?php echo site_url('products/form/' . $product->product_id); ?>">
                                        <i class="fa fa-edit fa-margin"></i> <?php _trans('edit'); ?>
                                    </a>
                                </li>
                                <li>
                                    <form action="<?php echo site_url('products/delete/' . $product->product_id); ?>"
                                          method="POST">
                                        <?php _csrf_field(); ?>
                                        <button type="submit" class="dropdown-button"
                                                onclick="return confirm('<?php _trans('delete_record_warning'); ?>');">
                                            <i class="fa fa-trash-o fa-margin"></i> <?php _trans('delete'); ?>
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </td>
                </tr>
            <?php } ?>
            </tbody>

        </table>
    </div>

</div>
