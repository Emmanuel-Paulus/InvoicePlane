<div class="table-responsive">
    <table class="table table-hover table-bordered table-striped">
        <tr>
            <th>&nbsp;</th>
            <th><?php _trans('picture_name'); ?></th>
            <th><?php _trans('picture_description'); ?></th>
        </tr>
        <?php foreach ($pictures as $picture) { ?>
            <tr class="picture">
                <td class="text-left">
                    <input type="checkbox" name="picture_ids[]"
                           value="<?php echo $picture->picture_id; ?>">
                </td>
                <td>
                    <b><?php _htmlsc($picture->picture_name); ?></b>
                </td>
                <td>
                    <?php echo nl2br(htmlsc($picture->picture_description)); ?>
                </td>
            </tr>
        <?php } ?>

    </table>
</div>
