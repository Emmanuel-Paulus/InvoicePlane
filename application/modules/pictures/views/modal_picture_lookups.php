<script>
    $(function () {
        // Display the create invoice modal
        $('#modal-choose-items').modal('show');

        $(".simple-select").select2();

        // Creates the invoice
        $('.select-items-confirm').click(function () {
            var picture_ids = [];

            $("input[name='picture_ids[]']:checked").each(function () {
                picture_ids.push(parseInt($(this).val()));
            });

            $.post("<?php echo site_url('pictures/ajax/process_picture_selections'); ?>", {
                picture_ids: picture_ids
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

                    last_item_row.find('input[name=item_name]').val(items[key].picture_name);
                    last_item_row.find('textarea[name=item_description]').val(items[key].picture_description);
                    last_item_row.find('input[name=item_picture_id]').val(items[key].picture_id);

                    $('#modal-choose-items').modal('hide');
                }
            });
        });

        // Toggle checkbox when click on row
        $(document).on('click', '.picture', function (event) {
            if (event.target.type !== 'checkbox') {
                $(':checkbox', this).trigger('click');
            }
        });

        // Reset the form
        $('#picture-reset-button').click(function () {
            var picture_table = $('#picture-lookup-table');

            picture_table.html('<h2 class="text-center"><i class="fa fa-spin fa-spinner"></i></h2>');

            var lookup_url = "<?php echo site_url('pictures/ajax/modal_picture_lookups'); ?>/";
            lookup_url += Math.floor(Math.random() * 1000) + '/?';
            lookup_url += "&reset_table=true";

            // Reload modal with settings
            window.setTimeout(function () {
                picture_table.load(lookup_url);
            }, 250);
        });


        // Filter pictures
        function pictures_filter() {
            var filter_picture = $('#filter_picture').val();
            var picture_table = $('#picture-lookup-table');

            picture_table.html('<h2 class="text-center"><i class="fa fa-spin fa-spinner"></i></h2>');

            var lookup_url = "<?php echo site_url('pictures/ajax/modal_picture_lookups'); ?>/";
            lookup_url += Math.floor(Math.random() * 1000) + '/?';

            if (filter_picture) {
                lookup_url += "&filter_picture=" + filter_picture;
            }

            // Reload modal with settings
            window.setTimeout(function () {
                picture_table.load(lookup_url);
            }, 250);
        }

        // Bind enter to picture search if search field is focused
        $(document).keypress(function(e){
            if (e.which === 13 && $('#filter_picture').is(':focus')){
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
            <h4 class="panel-title"><?php _trans('add_picture'); ?></h4>
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
                    <input type="text" class="form-control" name="filter_picture" id="filter_picture"
                           placeholder="<?php _trans('picture_name'); ?>"
                           value="<?php echo $filter_picture ?>">
                </div>
                <button type="button" id="filter-button"
                        class="btn btn-default"><?php _trans('search_picture'); ?></button>
                <button type="button" id="picture-reset-button" class="btn btn-default">
                    <?php _trans('reset'); ?>
                </button>
            </div>

            <br/>

            <div id="picture-lookup-table">
                <?php $this->layout->load_view('pictures/partial_picture_table_modal'); ?>
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
