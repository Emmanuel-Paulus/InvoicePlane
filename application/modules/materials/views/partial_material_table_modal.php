<div class="table-responsive">
    <table class="table table-hover table-bordered table-striped">
        <tr>
            <th>&nbsp;</th>
            <th><?php _trans('material_name'); ?></th>
            <th><?php _trans('material_description'); ?></th>
            <th class="text-right"><?php _trans('material_price'); ?></th>
            <th><?php _trans('material_provider_name'); ?></th>
        </tr>
        <?php foreach ($materials as $material) { ?>
            <tr class="material">
                <td class="text-left">
                    <input type="checkbox" name="material_ids[]"
                           value="<?php echo $material->material_id; ?>">
                </td>
                <td>
                    <b><?php _htmlsc($material->product_name); ?></b>
                </td>
                <td>
                    <?php echo nl2br(htmlsc($material->material_description)); ?>
                </td>
                <td class="text-right">
                    <?php echo format_currency($material->material_price); ?>
                </td>
                <td>
                    <b><?php _htmlsc($material->material_provider_name); ?></b>
                </td>
            </tr>
        <?php } ?>

    </table>
</div>
