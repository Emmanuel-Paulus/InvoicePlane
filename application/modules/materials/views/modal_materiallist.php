<script>
    $(function () {
        // Display the create invoice modal
        $('#modal-materialist').modal('show');
    });
</script>
<div id="modal-materialist" class="modal col-xs-12 col-sm-10 col-sm-offset-1" style="background-color: white;"
     role="dialog" aria-labelledby="modal-materialist" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><i class="fa fa-close"></i></button>
        <h4 class="panel-title"><?php _trans('materials'); ?></h4>
    </div>
    <div class="modal-body">

        <table class="table table-hover table-striped table-picture">
            <thead>
                <tr>
                    <th><?php _trans('id'); ?></th>
                    <th><?php _trans('material_name'); ?></th>
                    <th><?php _trans('material_description'); ?></th>
                    <th><?php _trans('prod_matr_amount'); ?></th>
                    <th><?php _trans('material_price'); ?></th>
                    <th><?php _trans('material_price_amount'); ?></th>
                    <th><?php _trans('prod_matr_price'); ?></th>
                    <th><?php _trans('material_price_descr'); ?></th>
                    <th><?php _trans('material_provider_name'); ?></th>
                </tr>
            </thead>

            <tbody>
                <?php foreach ($materials as $material) { ?>
                    <tr>
                        <td><?php _htmlsc($material->material_id); ?>
                            <?php echo $this->mdl_pictures->htmlpicture($material->picture_id); ?>
                        </td>
                        <td><?php _htmlsc($material->material_name); ?></td>
                        <td><?php echo nl2br(htmlsc($material->material_description)); ?></td>
                        <td class="align-right"><?php _htmlsc($material->prod_matr_amount); ?></td>
                        <td class="amount"><?php echo format_currency($material->prod_matr_amount * $material->material_price / $material->material_price_amount); ?></td>
                        <td class="align-right"><?php _htmlsc($material->material_price_amount); ?></td>
                        <td class="amount"><?php echo format_currency($material->material_price); ?></td>
                        <td><?php _htmlsc($material->material_price_descr); ?></td>
                        <td><?php echo nl2br(htmlsc($material->material_provider_name)); ?>
                            <?php
                            if ($material->material_url) {
                                echo '<a href="' . $material->material_url . '" target="_blank"><i class="fa fa-link fa-margin"></i> &nbsp;</a>';
                            }
                            ?>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>

        </table>
        <div class="modal-footer">
            <div class="btn-group">
                <button class="btn btn-success" type="button" data-dismiss="modal">
                    <i class="fa fa-times"></i>
                    <?php _trans('cancel'); ?>
                </button>
            </div>
        </div>
    </div>
