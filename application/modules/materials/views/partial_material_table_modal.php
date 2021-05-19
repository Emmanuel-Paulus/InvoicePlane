<div class="table-responsive">
    <table class="table table-hover table-bordered table-striped">
        <tr>
            <th>&nbsp;</th>
            <th><?php _trans('material_name'); ?></th>
            <th><?php _trans('material_description'); ?></th>
            <th><?php _trans('material_price'); ?></th>
            <th><?php _trans('material_price_amount'); ?></th>
            <th><?php _trans('material_price_descr'); ?></th>
            <th><?php _trans('material_provider_name'); ?></th>
        </tr>
        <?php foreach ($materials as $material) { ?>
            <tr class="material">
                <td class="text-left">
                    <input type="checkbox" name="material_ids[]"
                           value="<?php echo $material->material_id; ?>">
                </td>
                <td>
                    <?php echo htmlsc($material->material_name); ?>
                </td>
                <td>
                    <?php echo nl2br(htmlsc($material->material_description)); ?>
                </td>
                <td class="text-right">
                    <?php echo format_currency($material->material_price); ?>
                </td>
                <td class="text-right">
                    <?php _htmlsc($material->material_price_amount); ?>
                </td>
                <td>
                    <?php _htmlsc($material->material_price_descr); ?>
                </td>
                <td>
                    <?php _htmlsc($material->material_provider_name); ?>
                        <?php if ($material->material_url) { 
                                echo '<a href="'.$material->material_url.'" target="_blank"><i class="fa fa-link fa-margin"></i> &nbsp;</a>'; 
                        } ?>
                </td>
            </tr>
        <?php } ?>

    </table>
</div>
