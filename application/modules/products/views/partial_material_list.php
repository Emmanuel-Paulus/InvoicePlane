<div class="table-responsive">
    <table id="materiallist" class="items table table-condensed table-bordered no-margin table-picture">
        <thead>
        <tr>
            <th><?php _trans('material_name'); ?></th>
            <th><?php _trans('material_description'); ?></th>
            <th><?php _trans('material_price_amount'); ?></th>
            <th><?php _trans('material_price'); ?></th>
            <th><?php _trans('material_provider_name'); ?></th>
            <th></th>
        </tr>
        </thead>

        <tbody id="new_row" style="display: none">
        <tr>
            <td class="td-text">
                <input type="hidden" name="product_material_id" value="">
                <input type="hidden" name="product_id" value="<?php echo $product->product_id; ?>">
                <input type="hidden" name="material_id" value="">
                <input type="hidden" name="material_price" value="">
                <input type="hidden" name="material_price_amount" value="">
                <div class="input-group">
                    <input type="text" name="material_name" class="input-sm form-control" value="" readonly>
                </div>
            </td>
            <td class="td-text">
                <div class="input-group">
                    <input type="text" name="material_description" class="input-sm form-control" value="" readonly>
                </div>
            </td>
            <td class="td-amount td-quantity">
                <div class="input-group">
                    <input type="text" name="prod_matr_amount" class="input-sm form-control amount" value="">
                </div>
            </td>
            <td class="td-amount ">
                <div class="input-group">
                    <input type="text" name="prod_matr_price" class="input-sm form-control amount"  value="" readonly>
                </div>
            </td>
            <td class="td-text ">
                <div class="input-group">
                    <input type="text" name="material_provider_name" class="input-sm form-control"  value="" readonly>
                </div>
            </td>
            <td class="td-icon text-right td-vert-middle">
                <button type="button" class="btn_delete_item btn btn-link btn-sm" title="<?php _trans('delete'); ?>">
                    <i class="fa fa-trash-o text-danger"></i>
                </button>
            </td>
        </tr>
        </tbody>

        <?php foreach ($materials as $material) { 
            $prod_matr_price = 0;
            if ($material->prod_matr_amount) {
                $prod_matr_price = $material->prod_matr_amount * $material->material_price / $material->material_price_amount;
            }
        ?>
            <tbody class="material">
            <tr>
                <td class="td-text">
                <input type="hidden" name="product_material_id" value="<?php echo $material->product_material_id; ?>">
                    <input type="hidden" name="product_id" value="<?php echo $product->product_id; ?>">
                    <input type="hidden" name="material_id" value="<?php echo $material->material_id; ?>">
                    <input type="hidden" name="material_price" value="<?php echo $material->material_price; ?>">
                    <input type="hidden" name="material_price_amount" value="<?php echo $material->material_price_amount; ?>">

                    <div class="input-group">
                        <input type="text" name="material_name" class="input-sm form-control" readonly
                               value="<?php _htmlsc($material->material_name); ?>">
                    </div>
                </td>
                <td class="td-text">
                    <div class="input-group">
                        <input type="text" name="material_description" class="input-sm form-control" readonly
                            value="<?php echo $material->material_description; ?>">
                        </span>
                    </div>
                </td>
                <td class="td-amount td-quantity">
                    <div class="input-group">
                        <input type="text" name="prod_matr_amount" class="input-sm form-control amount"
                               value="<?php echo format_amount($material->prod_matr_amount); ?>">
                    </div>
                </td>
                <td class="td-amount">
                    <div class="input-group">
                        <input type="text" name="prod_matr_price" class="input-sm form-control amount" readonly
                               value="<?php echo format_amount($prod_matr_price); ?>">
                    </div>
                </td>
                <td class="td-text">
                    <div class="input-group">
                        <input type="text" name="material_provider_name" class="input-sm form-control" readonly
                            value="<?php echo $material->material_provider_name; ?>">
                    </div>
                </td>
                <td class="td-icon text-right td-vert-middle">
                    <button type="button" class="btn_delete_item btn btn-link btn-sm" title="<?php _trans('delete'); ?>"
                            data-product_material_id="<?php echo $material->product_material_id; ?>">
                        <i class="fa fa-trash-o text-danger"></i>
                    </button>
                </td>
            </tr>
            </tbody>
        <?php } ?>

    </table>
</div>

<br/>

<div class="row">
    <div class="col-xs-12 col-md-4">
        <div class="btn-group">
            <a href="#" class="btn_add_row btn btn-sm btn-default">
                <i class="fa fa-plus"></i> <?php _trans('add_new_row'); ?>
            </a>
        </div>
    </div>
</div>

<script>
    $(function () {
        $('.btn_add_row').click(function () {
            $('#modal-placeholder').load(
                "<?php echo site_url('materials/ajax/modal_material_lookups/'); ?>" + Math.floor(Math.random() * 1000)
            );
        });

        $('#btn-submit').click(function () {
            var items = [];
            var item_order = 1;
            $('#materiallist tr').each(function () {
                var row = {};
                row["product_material_id"] = $(this).find('input[name=product_material_id]').val();
                row["product_id"] = $(this).find('input[name=product_id]').val();
                row["material_id"] = $(this).find('input[name=material_id]').val();
                row["prod_matr_amount"] = $(this).find('input[name=prod_matr_amount]').val();
                row['item_order'] = item_order;
                if (row["material_id"]) {
                    item_order++;
                    items.push(row);
                }
            });
            $.post("<?php echo site_url('products/ajax/save'); ?>", {
                    product_id: <?php echo $this->mdl_products->form_value('product_id'); ?>,
                    product_sku: $('#product_sku').val(),
                    product_name: $('#product_name').val(),
                    product_description: $('#product_description').val(),
                    product_price: $('#product_price').val(),
                    unit_id: $('#unit_id').val(),
                    tax_rate_id: $('#tax_rate_id').val(),
                    provider_name: $('#provider_name').val(),
                    purchase_price: $('#purchase_price').val(),
                    product_tariff: $('#product_tariff').val(),
                    picture_id: $('#picture_id').val(),
                    product_materials: JSON.stringify(items),
                },
                function (data) {
                    <?php echo(IP_DEBUG ? 'console.log(data);' : ''); ?>
                    var response = JSON.parse(data);
                    if (response.success === 1) {
                        window.location = "<?php echo site_url('products/form'); ?>/" + <?php echo $product->product_id; ?>;
                    } else {
                        $('#fullpage-loader').hide();
                        $('.control-group').removeClass('has-error');
                        $('div.alert[class*="alert-"]').remove();
                        var resp_errors = response.validation_errors,
                            all_resp_errors = '';
                        for (var key in resp_errors) {
                            $('#' + key).parent().addClass('has-error');
                            all_resp_errors += resp_errors[key];
                        }
                        $('#product_form').prepend('<div class="alert alert-danger">' + all_resp_errors + '</div>');
                    }
                });
                return false;
        });

        $(document).on('click', '.btn_delete_item', function () {
            var btn = $(this);
            var product_material_id = btn.data('product_material_id');

            // Just remove the row if no item ID is set (new row)
            if (typeof product_material_id === 'undefined') {
                $(this).parents('.material').remove();
            } else {
                $.post("<?php echo site_url('products/ajax/delete_material/' . $product->product_id); ?>", {
                    'product_material_id': product_material_id,
                },
                function (data) {
                    <?php echo(IP_DEBUG ? 'console.log(data);' : ''); ?>
                    var response = JSON.parse(data);
                    if (response.success === 1) {
                        btn.parents('.material').remove();
                    } else {
                        btn.removeClass('btn-link').addClass('btn-danger').prop('disabled', true);
                    }   
                });
            }
        });
        
        $(document).on('change', '[name ="prod_matr_amount"]', function () {
            var currentRow=$(this).closest("tr"); 
            var material_price = currentRow.find('input[name=material_price]').val() ?? 0;
            var material_price_amount = currentRow.find('input[name=material_price_amount]').val() ?? 1;
            var prod_matr_amount = currentRow.find('input[name=prod_matr_amount]').val() ?? 1;
            var pm_price = prod_matr_amount * material_price / material_price_amount;
            currentRow.find('input[name=prod_matr_price]').val("");
            $.post("<?php echo site_url('materials/ajax/format_price'); ?>", {
                price: (isNaN(pm_price) ? "0" : pm_price),
            }, function (data) {
                <?php echo(IP_DEBUG ? 'console.log(data);' : ''); ?>
                var items = JSON.parse(data);
                currentRow.find('input[name=prod_matr_price]').val(items["price"]);
            });
        });
    });
</script>
