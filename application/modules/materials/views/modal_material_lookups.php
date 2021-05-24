<script>
    $(function () {
        // Display the create invoice modal
        $('#modal-choose-items').modal('show');

        $(".simple-select").select2();

        // Creates the invoice
        $('.select-items-confirm').click(function () {
            var material_ids = [];
            var material_ids_checked = false;

            $("input[name='material_ids[]']:checked").each(function () {
                material_ids.push(parseInt($(this).val()));
                material_ids_checked = true;
            });
            if (material_ids_checked == false) { return false; }

            $.post("<?php echo site_url('materials/ajax/process_material_selections'); ?>", {
                material_ids: material_ids
            }, function (data) {
                <?php echo(IP_DEBUG ? 'console.log(data);' : ''); ?>
                var items = JSON.parse(data);

                for (var key in items) {
                    if ($('#materiallist tbody:last input[name=item_name]').val() !== '') {
                        $('#new_row').clone().appendTo('#materiallist').removeAttr('id').addClass('material').show();
                    }

                    var last_item_row = $('#materiallist tbody:last');
                    last_item_row.find('input[name=material_id]').val(items[key].material_id);
                    last_item_row.find('input[name=material_name]').val(items[key].material_name);
                    last_item_row.find('input[name=material_description]').val(items[key].material_description);
                    last_item_row.find('input[name=material_price]').val(items[key].material_price_raw);
                    last_item_row.find('input[name=material_price_amount]').val(items[key].material_price_amount);
                    last_item_row.find('input[name=material_provider_name]').val(items[key].material_provider_name);
                    last_item_row.find('input[name=prod_matr_amount]').val(1);
                    last_item_row.find('input[name=prod_matr_amount]').trigger( "change" );

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

        // Filter on family dropdown change
        $("#filter_family").change(function () {
            materials_filter();
        });

        // Filter materials
        function materials_filter() {
            var filter_family = $('#filter_family').val();
            var filter_material = $('#filter_material').val();
            var material_table = $('#material-lookup-table');

            material_table.html('<h2 class="text-center"><i class="fa fa-spin fa-spinner"></i></h2>');

            var lookup_url = "<?php echo site_url('materials/ajax/modal_material_lookups'); ?>/";
            lookup_url += Math.floor(Math.random() * 1000) + '/?';

            if (filter_material) {
                lookup_url += "&filter_material=" + filter_material;
            }

            if (filter_family) {
                lookup_url += "&filter_family=" + filter_family;
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
                    <select name="filter_family" id="filter_family" class="form-control simple-select">
                        <option value=""><?php _trans('any_family'); ?></option>
                        <?php foreach ($families as $family) { ?>
                            <option value="<?php echo $family->family_id; ?>"
                                <?php if (isset($filter_family) && $family->family_id == $filter_family) {
                                    echo ' selected="selected"';
                                } ?>>
                                <?php _htmlsc($family->family_name); ?>
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
