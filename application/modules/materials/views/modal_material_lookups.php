<script>
    $(function () {
        // Display the create invoice modal
        $('#modal-choose-items').modal('show');

        $(".simple-select").select2();

        // Creates the invoice
        $('.select-items-confirm').click(function () {
            var material_ids = [];

            $("input[name='material_ids[]']:checked").each(function () {
                material_ids.push(parseInt($(this).val()));
            });

            $.post("<?php echo site_url('materials/ajax/process_material_selections'); ?>", {
                material_ids: material_ids
            }, function (data) {
                <?php echo(IP_DEBUG ? 'console.log(data);' : ''); ?>
                var items = JSON.parse(data);

                for (var key in items) {
                    // Set default tax rate id if empty
                    if (!items[key].tax_rate_id) items[key].tax_rate_id = '<?php echo $default_item_tax_rate; ?>';

                    if ($('#item_table tbody:last input[name=item_name]').val() !== '') {
                        $('#new_row').clone().appendTo('#item_table').removeAttr('id').addClass('item').show();
                    }

                    var last_item_row = $('#item_table tbody:last');

                    last_item_row.find('input[name=item_name]').val(items[key].material_name);
                    last_item_row.find('textarea[name=item_description]').val(items[key].material_description);
                    last_item_row.find('input[name=item_price]').val(items[key].material_price);
                    last_item_row.find('input[name=item_material_id]').val(items[key].material_id);

                    $('#modal-choose-items').modal('hide');
                }
            });
        });

        // Toggle checkbox when click on row
        $(document).on('click', '.material', function (event) {
            if (event.target.type !== 'checkbox') {
                $(':checkbox', this).trigger('click');
            }
        });

        // Reset the form
        $('#material-reset-button').click(function () {
            var material_table = $('#material-lookup-table');

            material_table.html('<h2 class="text-center"><i class="fa fa-spin fa-spinner"></i></h2>');

            var lookup_url = "<?php echo site_url('materials/ajax/modal_material_lookups'); ?>/";
            lookup_url += Math.floor(Math.random() * 1000) + '/?';
            lookup_url += "&reset_table=true";

            // Reload modal with settings
            window.setTimeout(function () {
                material_table.load(lookup_url);
            }, 250);
        });

        // Filter on search button click
        $('#filter-button').click(function () {
            materials_filter();
        });

        // Filter on product dropdown change
        $("#filter_product").change(function () {
            materials_filter();
        });

        // Filter materials
        function materials_filter() {
            var filter_product = $('#filter_product').val();
            var filter_material = $('#filter_material').val();
            var material_table = $('#material-lookup-table');

            material_table.html('<h2 class="text-center"><i class="fa fa-spin fa-spinner"></i></h2>');

            var lookup_url = "<?php echo site_url('materials/ajax/modal_material_lookups'); ?>/";
            lookup_url += Math.floor(Math.random() * 1000) + '/?';

            if (filter_product) {
                lookup_url += "&filter_product=" + filter_product;
            }

            if (filter_material) {
                lookup_url += "&filter_material=" + filter_material;
            }

            // Reload modal with settings
            window.setTimeout(function () {
                material_table.load(lookup_url);
            }, 250);
        }

        // Bind enter to material search if search field is focused
        $(document).keypress(function(e){
            if (e.which === 13 && $('#filter_material').is(':focus')){
                $('#filter-button').click();
                return false;
            }
        });
    });
</script>

<div id="modal-choose-items" class="modal col-xs-12 col-sm-10 col-sm-offset-1"
     role="dialog" aria-labelledby="modal-choose-items" aria-hidden="true">
    <form class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal"><i class="fa fa-close"></i></button>
            <h4 class="panel-title"><?php _trans('add_material'); ?></h4>
        </div>
        <div class="modal-body">

            <div class="form-inline">
                <div class="form-group filter-form">
                    <select name="filter_product" id="filter_product" class="form-control simple-select">
                        <option value=""><?php _trans('any_product'); ?></option>
                        <?php foreach ($products as $product) { ?>
                            <option value="<?php echo $product->product_id; ?>"
                                <?php if (isset($filter_product) && $product->product_id == $filter_product) {
                                    echo ' selected="selected"';
                                } ?>>
                                <?php _htmlsc($product->product_name); ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
                <div class="form-group">
                    <input type="text" class="form-control" name="filter_material" id="filter_material"
                           placeholder="<?php _trans('material_name'); ?>"
                           value="<?php echo $filter_material ?>">
                </div>
                <button type="button" id="filter-button"
                        class="btn btn-default"><?php _trans('search_material'); ?></button>
                <button type="button" id="material-reset-button" class="btn btn-default">
                    <?php _trans('reset'); ?>
                </button>
            </div>

            <br/>

            <div id="material-lookup-table">
                <?php $this->layout->load_view('materials/partial_material_table_modal'); ?>
            </div>

        </div>
        <div class="modal-footer">
            <div class="btn-group">
                <button class="select-items-confirm btn btn-success" type="button">
                    <i class="fa fa-check"></i>
                    <?php _trans('submit'); ?>
                </button>
                <button class="btn btn-danger" type="button" data-dismiss="modal">
                    <i class="fa fa-times"></i>
                    <?php _trans('cancel'); ?>
                </button>
            </div>
        </div>
    </form>

</div>
