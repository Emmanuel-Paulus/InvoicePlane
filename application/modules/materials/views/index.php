<script>
    $(function () {
        //$(".simple-select").select2();

        // Reset the form
        $('#material-reset-button').click(function () {
            var lookup_url = "<?php echo site_url('materials/index'); ?>/";
            lookup_url += '/?rnd=' + Math.floor(Math.random() * 1000);
            lookup_url += "&reset_table=true";
            location.href = lookup_url;
        });

        // Filter on search button click
        $('#filter-button').click(function () {
            materials_filter();
        });

        // Filter on family dropdown change
        $("#filter_family").change(function () {
            materials_filter();
        });


        // Filter on family dropdown change
        $("#order").change(function () {
            materials_filter();
        });
        
        // Filter materials
        function materials_filter() {
            var filter_family = $('#filter_family').val();
            var filter_material = $('#filter_material').val();
            var sortorder = $('#order').val();

            var lookup_url = "<?php echo site_url('materials/index'); ?>/";
            lookup_url += '/?rnd=' + Math.floor(Math.random() * 1000);

            if (filter_material) {
                lookup_url += "&filter_material=" + filter_material;
            }

            if (filter_family) {
                lookup_url += "&filter_family=" + filter_family;
            }

            if (sortorder) {
                lookup_url += "&order=" + sortorder;
            }

            location.href = lookup_url;
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
<div id="headerbar">
    <h1 class="headerbar-title"><?php _trans('materials'); ?></h1>

    <div class="headerbar-item pull-right">
        <a class="btn btn-sm btn-primary" href="<?php echo site_url('materials/form'); ?>">
            <i class="fa fa-plus"></i> <?php _trans('new'); ?>
        </a>
    </div>

    <div class="headerbar-item pull-right">
        <?php echo pager(site_url('materials/index'), 'mdl_materials'); ?>
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
                    <input type="text" class="form-control" name="filter_material" id="filter_material"
                           placeholder="<?php _trans('material_name'); ?> - <?php _trans('material_desr'); ?>"
                           value="<?php echo $filter_material ?>">
                </div>
                <div class="form-group filter-form">
<?php
function option_for_select($order, $id, $name) {
    echo "<option value='".$id."'".((isset($order) && $id == $order) ? " selected='selected'" : "").">".$name."</option>\n";    
}
?>
                    <select name="order" id="order" class="form-control">
                        <option value=""><?php _trans('order'); ?></option>
                        <?php option_for_select($order, "material_id", trans("id")); ?>
                        <?php option_for_select($order, "material_name", trans("material_name")); ?>
                        <?php option_for_select($order, "family_name", trans("family_name")); ?>
                        <?php option_for_select($order, "material_description", trans("material_description")); ?>
                        <?php option_for_select($order, "material_price", trans("material_price")); ?>
                        <?php option_for_select($order, "material_price_amount", trans("material_price_amount")); ?>
                        <?php option_for_select($order, "material_provider_name", trans("material_provider_name")); ?>
                    </select>
                </div>
                <button type="button" id="filter-button"
                        class="btn btn-default"><?php _trans('search_material'); ?></button>
                <button type="button" id="material-reset-button" class="btn btn-default">
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
                    <th><?php _trans('id'); ?></th>
                    <th><?php _trans('material_name'); ?></th>
                    <th><?php _trans('family'); ?></th>
                    <th><?php _trans('material_description'); ?></th>
                    <th><?php _trans('material_price'); ?></th>
                    <th><?php _trans('material_price_amount'); ?></th>
                    <th><?php _trans('material_price_descr'); ?></th>
                    <th><?php _trans('material_provider_name'); ?></th>
                    <th><?php _trans('options'); ?></th>
                </tr>
            </thead>

            <tbody>
                        <?php foreach ($materials as $material) { ?>
                    <tr>
                        <td><?php _htmlsc($material->material_id); ?>
    <?php echo $this->mdl_pictures->htmlpicture($material->picture_id); ?>
                        </td>
                        <td><?php _htmlsc($material->material_name); ?></td>
                        <td><?php _htmlsc($material->family_name); ?></td>
                        <td><?php echo nl2br(htmlsc($material->material_description)); ?></td>
                        <td class="amount"><?php echo format_currency($material->material_price); ?></td>
                        <td class="align-right"><?php _htmlsc($material->material_price_amount); ?></td>
                        <td><?php _htmlsc($material->material_price_descr); ?></td>
                        <td><?php echo nl2br(htmlsc($material->material_provider_name)); ?>
                            <?php
                            if ($material->material_url) {
                                echo '<a href="' . $material->material_url . '" target="_blank"><i class="fa fa-link fa-margin"></i> &nbsp;</a>';
                            }
                            ?>
                        </td>
                        <td>
                            <div class="options btn-group">
                                <a class="btn btn-default btn-sm dropdown-toggle"
                                   data-toggle="dropdown" href="#">
                                    <i class="fa fa-cog"></i> <?php _trans('options'); ?>
                                </a>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a href="<?php echo site_url('materials/form/' . $material->material_id); ?>">
                                            <i class="fa fa-edit fa-margin"></i> <?php _trans('edit'); ?>
                                        </a>
                                    </li>
                                    <li>
                                        <form action="<?php echo site_url('materials/delete/' . $material->material_id); ?>"
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
