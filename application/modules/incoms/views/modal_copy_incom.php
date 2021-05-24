<script>
    $(function () {
        $('#modal_copy_incom').modal('show');

        // Select2 for all select inputs
        $(".simple-select").select2();

        <?php $this->layout->load_view('providers/script_select2_provider_id.js'); ?>

        // Creates the incom
        $('#copy_incom_confirm').click(function () {
            $.post("<?php echo site_url('incoms/ajax/copy_incom'); ?>", {
                    incom_id: <?php echo $incom_id; ?>,
                    provider_id: $('#create_incom_provider_id').val(),
                    incom_date_created: $('#incom_date_created').val(),
                    invoice_group_id: $('#invoice_group_id').val(),
                    user_id: $('#user_id').val()
                },
                function (data) {
                    <?php echo(IP_DEBUG ? 'console.log(data);' : ''); ?>
                    var response = JSON.parse(data);
                    if (response.success === 1) {
                        window.location = "<?php echo site_url('incoms/view'); ?>/" + response.incom_id;
                    }
                    else {
                        // The validation was not successful
                        $('.control-group').removeClass('has-error');
                        for (var key in response.validation_errors) {
                            $('#' + key).parent().parent().addClass('has-error');
                        }
                    }
                });
        });
    });

</script>

<div id="modal_copy_incom" class="modal modal-lg" role="dialog" aria-labelledby="modal_copy_incom" aria-hidden="true">
    <form class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal"><i class="fa fa-close"></i></button>
            <h4 class="panel-title"><?php _trans('copy_incom'); ?></h4>
        </div>
        <div class="modal-body">

            <input type="hidden" name="user_id" id="user_id" value="<?php echo $incom->user_id; ?>">

            <div class="form-group">
                <label for="create_incom_provider_id"><?php _trans('provider'); ?></label>
                <select name="provider_id" id="create_incom_provider_id" class="provider-id-select form-control"
                        autofocus="autofocus">
                    <?php if (!empty($provider)) : ?>
                        <option value="<?php echo $provider->provider_id; ?>"><?php _htmlsc(format_provider($provider)); ?></option>
                    <?php endif; ?>
                </select>
            </div>

            <div class="form-group has-feedback">
                <label for="incom_date_created">
                    <?php _trans('incom_date'); ?>
                </label>

                <div class="input-group">
                    <input name="incom_date_created" id="incom_date_created"
                           class="form-control datepicker"
                           value="<?php echo date_from_mysql($incom->incom_date_created, true); ?>">
                    <span class="input-group-addon">
                        <i class="fa fa-calendar fa-fw"></i>
                    </span>
                </div>
            </div>

        </div>

        <div class="modal-footer">
            <div class="btn-group">
                <button class="btn btn-success" id="copy_incom_confirm" type="button">
                    <i class="fa fa-check"></i> <?php _trans('submit'); ?>
                </button>
                <button class="btn btn-danger" type="button" data-dismiss="modal">
                    <i class="fa fa-times"></i> <?php _trans('cancel'); ?>
                </button>
            </div>
        </div>

    </form>

</div>
