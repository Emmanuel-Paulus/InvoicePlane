<?php
$cv = $this->controller->view_data["custom_values"];
?>
<script>

    $(function () {
        $('.btn_add_product').click(function () {
            $('#modal-placeholder').load(
                "<?php echo site_url('products/ajax/modal_product_lookups'); ?>/" +
                    Math.floor(Math.random() * 1000)
            );
        });

        $('.btn_add_row').click(function () {
            $('#new_row').clone().appendTo('#item_table').removeAttr('id').addClass('item').show();
        });

        $('.btn_materiallist').click(function () {
            $('#modal-placeholder').load(
                "<?php echo site_url('materials/ajax/incom/' . $incom_id); ?>/" +
                Math.floor(Math.random() * 1000)
            );
        });

        $('.btn_materiallist_pdf').click(function () {
            var w = window.open("<?php echo site_url('materials/incom_pdf/' . $incom_id); ?>/" 
                    + Math.floor(Math.random() * 1000), '_blank');
            w.focus();
        });

        $('.btn_materiallist_csv').click(function () {
            var w = window.open("<?php echo site_url('materials/incom_csv/' . $incom_id); ?>/" 
                    + Math.floor(Math.random() * 1000), '_blank');
            w.focus();
        });

        $('#incom_change_provider').click(function () {
            $('#modal-placeholder').load("<?php echo site_url('incoms/ajax/modal_change_provider'); ?>", {
                incom_id: <?php echo $incom_id; ?>,
                provider_id: "<?php echo $this->db->escape_str($incom->provider_id); ?>",
            });
        });

        <?php if (!$items) { ?>
        $('#new_row').clone().appendTo('#item_table').removeAttr('id').addClass('item').show();
        <?php } ?>


        $('#btn_save_incom').click(function () {
            var items = [];
            var item_order = 1;
            $('table tbody.item').each(function () {
                var row = {};
                $(this).find('input,select,textarea').each(function () {
                    if ($(this).is(':checkbox')) {
                        row[$(this).attr('name')] = $(this).is(':checked');
                    } else {
                        row[$(this).attr('name')] = $(this).val();
                    }
                });
                row['item_order'] = item_order;
                item_order++;
                items.push(row);
            });
            $.post("<?php echo site_url('incoms/ajax/save'); ?>", {
                    incom_id: <?php echo $incom_id; ?>,
                    incom_number: $('#incom_number').val(),
                    incom_date_created: $('#incom_date_created').val(),
                    incom_date_expires: $('#incom_date_expires').val(),
                    incom_status_id: $('#incom_status_id').val(),
                    incom_password: $('#incom_password').val(),
                    items: JSON.stringify(items),
                    incom_discount_amount: $('#incom_discount_amount').val(),
                    incom_discount_percent: $('#incom_discount_percent').val(),
                    notes: $('#notes').val(),
                    custom: $('input[name^=custom],select[name^=custom]').serializeArray(),
                },
                function (data) {
                    <?php echo(IP_DEBUG ? 'console.log(data);' : ''); ?>
                    var response = JSON.parse(data);
                    if (response.success === 1) {
                        window.location = "<?php echo site_url('incoms/view'); ?>/" + <?php echo $incom_id; ?>;
                    } else {
                        $('#fullpage-loader').hide();
                        $('.control-group').removeClass('has-error');
                        $('div.alert[class*="alert-"]').remove();
                        var resp_errors = response.validation_errors,
                            all_resp_errors = '';

                        if (typeof(resp_errors) == 'string') {
                            all_resp_errors = resp_errors;
                        } else {
                            for (var key in resp_errors) {
                                $('#' + key).parent().addClass('has-error');
                                all_resp_errors += resp_errors[key];
                            }
                        }

                        $('#quote_form').prepend('<div class="alert alert-danger">' + all_resp_errors + '</div>');
                    }
                });
        });

        $(document).on('click', '.btn_delete_item', function () {
            var btn = $(this);
            var item_id = btn.data('item-id');

            // Just remove the row if no item ID is set (new row)
            if (typeof item_id === 'undefined') {
                $(this).parents('.item').remove();
            }

            $.post("<?php echo site_url('incoms/ajax/delete_item/' . $incom->incom_id); ?>", {
                    'item_id': item_id,
                },
                function (data) {
                    <?php echo(IP_DEBUG ? 'console.log(data);' : ''); ?>
                    var response = JSON.parse(data);

                    if (response.success === 1) {
                        btn.parents('.item').remove();
                    } else {
                        btn.removeClass('btn-link').addClass('btn-danger').prop('disabled', true);
                    }
                });
        });

        $(document).ready(function () {
            if ($('#incom_discount_percent').val().length > 0) {
                $('#incom_discount_amount').prop('disabled', true);
            }
            if ($('#incom_discount_amount').val().length > 0) {
                $('#incom_discount_percent').prop('disabled', true);
            }
        });
        $('#incom_discount_amount').keyup(function () {
            if (this.value.length > 0) {
                $('#incom_discount_percent').prop('disabled', true);
            } else {
                $('#incom_discount_percent').prop('disabled', false);
            }
        });
        $('#incom_discount_percent').keyup(function () {
            if (this.value.length > 0) {
                $('#incom_discount_amount').prop('disabled', true);
            } else {
                $('#incom_discount_amount').prop('disabled', false);
            }
        });

        var fixHelper = function (e, tr) {
            var $originals = tr.children();
            var $helper = tr.clone();
            $helper.children().each(function (index) {
                $(this).width($originals.eq(index).width());
            });
            return $helper;
        };

        $('#item_table').sortable({
            helper: fixHelper,
            items: 'tbody',
        });
    });
</script>

<?php echo $modal_delete_incom; ?>
<?php echo $modal_add_incom_tax; ?>

<div id="headerbar">
    <h1 class="headerbar-title">
        <?php
        echo trans('incom') . ' ';
        echo($incom->incom_number ? '#' . $incom->incom_number : $incom->incom_id);
        ?>
    </h1>

    <div class="headerbar-item pull-right">
        <div class="btn-group btn-group-sm">
            <a class="btn btn-default dropdown-toggle" data-toggle="dropdown" href="#">
                <?php _trans('options'); ?> <i class="fa fa-chevron-down"></i>
            </a>
            <ul class="dropdown-menu dropdown-menu-right">
                <li>
                    <a href="#add-incom-tax" data-toggle="modal">
                        <i class="fa fa-plus fa-margin"></i>
                        <?php _trans('add_incom_tax'); ?>
                    </a>
                </li>
                <li>
                    <a href="#" id="btn_copy_incom"
                       data-incom-id="<?php echo $incom_id; ?>"
                       data-provider-id="<?php echo $incom->provider_id; ?>">
                        <i class="fa fa-copy fa-margin"></i>
                        <?php _trans('copy_incom'); ?>
                    </a>
                </li>
                <li>
                    <a href="#delete-incom" data-toggle="modal">
                        <i class="fa fa-trash-o fa-margin"></i> <?php _trans('delete'); ?>
                    </a>
                </li>
            </ul>
        </div>

        <a href="#" class="btn btn-success btn-sm ajax-loader" id="btn_save_incom">
            <i class="fa fa-check"></i>
            <?php _trans('save'); ?>
        </a>
    </div>

</div>

<div id="content">
    <?php echo $this->layout->load_view('layout/alerts'); ?>
    <div id="quote_form">
        <div class="incom">

            <div class="row">
                <div class="col-xs-12 col-sm-6 col-md-5">

                    <h3>
                        <a href="<?php echo site_url('providers/view/' . $incom->provider_id); ?>">
                            <?php _htmlsc(format_provider($incom)) ?>
                        </a>
                        <?php if ($incom->incom_status_id == 1) { ?>
                            <span id="incom_change_provider" class="fa fa-edit cursor-pointer small"
                                  data-toggle="tooltip" data-placement="bottom"
                                  title="<?php _trans('change_provider'); ?>"></span>
                        <?php } ?>
                    </h3>
                    <br>
                    <div class="provider-address">
                        <?php $this->layout->load_view('providers/partial_provider_address', ['provider' => $incom]); ?>
                    </div>
                    <?php if ($incom->provider_phone || $incom->provider_email) : ?>
                        <hr>
                    <?php endif; ?>
                    <?php if ($incom->provider_phone): ?>
                        <div>
                            <?php _trans('phone'); ?>:&nbsp;
                            <?php _htmlsc($incom->provider_phone); ?>
                        </div>
                    <?php endif; ?>
                    <?php if ($incom->provider_email): ?>
                        <div>
                            <?php _trans('email'); ?>:&nbsp;
                            <?php _auto_link($incom->provider_email); ?>
                        </div>
                    <?php endif; ?>

                </div>

                <div class="col-xs-12 visible-xs"><br></div>

                <div class="col-xs-12 col-sm-6 col-md-7">
                    <div class="details-box">
                        <div class="row">

                            <div class="col-xs-12 col-md-6">

                                <div class="incom-properties">
                                    <label for="incom_number">
                                        <?php _trans('incom'); ?> #
                                    </label>
                                    <input type="text" id="incom_number" name="incom_number" class="form-control input-sm"
                                        <?php if ($incom->incom_number) : ?> value="<?php echo $incom->incom_number; ?>"
                                        <?php else : ?> placeholder="<?php _trans('not_set'); ?>"
                                        <?php endif; ?>>
                                </div>
                                <div class="incom-properties has-feedback">
                                    <label for="incom_date_created">
                                        <?php _trans('date'); ?>
                                    </label>
                                    <div class="input-group">
                                        <input name="incom_date_created" id="incom_date_created"
                                               class="form-control input-sm datepicker"
                                               value="<?php echo date_from_mysql($incom->incom_date_created); ?>"/>
                                        <span class="input-group-addon">
                                            <i class="fa fa-calendar fa-fw"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="incom-properties has-feedback">
                                    <label for="incom_date_expires">
                                        <?php _trans('expires'); ?>
                                    </label>
                                    <div class="input-group">
                                        <input name="incom_date_expires" id="incom_date_expires"
                                               class="form-control input-sm datepicker"
                                               value="<?php echo date_from_mysql($incom->incom_date_expires); ?>">
                                        <span class="input-group-addon">
                                            <i class="fa fa-calendar fa-fw"></i>
                                        </span>
                                    </div>
                                </div>

                                <!-- Custom fields -->
                                <?php foreach ($custom_fields as $custom_field): ?>
                                    <?php if ($custom_field->custom_field_location != 1) {
                                        continue;
                                    } ?>
                                    <?php print_field($this->mdl_incoms, $custom_field, $cv); ?>
                                <?php endforeach; ?>

                            </div>
                            <div class="col-xs-12 col-md-6">

                                <div class="incom-properties">
                                    <label for="incom_status_id">
                                        <?php _trans('status'); ?>
                                    </label>
                                    <select name="incom_status_id" id="incom_status_id"
                                            class="form-control input-sm simple-select" data-minimum-results-for-search="Infinity">
                                        <?php foreach ($incom_statuses as $key => $status) { ?>
                                            <option value="<?php echo $key; ?>"
                                                    <?php if ($key == $incom->incom_status_id) { ?>selected="selected"
                                                <?php } ?>>
                                                <?php echo $status['label']; ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>

                            </div>

                        </div>
                    </div>
                </div>
            </div>

        </div>

        <?php $this->layout->load_view('incoms/partial_item_table'); ?>

        <hr/>

        <div class="row">
            <div class="col-xs-12 col-md-6">

                <div class="panel panel-default no-margin">
                    <div class="panel-heading">
                        <?php _trans('notes'); ?>
                    </div>
                    <div class="panel-body">
                        <textarea name="notes" id="notes" rows="3"
                                  class="input-sm form-control"><?php _htmlsc($incom->notes); ?></textarea>
                    </div>
                </div>

                <div class="col-xs-12 visible-xs visible-sm"><br></div>

            </div>
            <div class="col-xs-12 col-md-6">

                <?php $this->layout->load_view('upload/dropzone-incom-html'); ?>

                <?php if ($custom_fields): ?>
                    <?php $cv = $this->controller->view_data["custom_values"]; ?>
                    <div class="row">
                        <div class="col-xs-12">

                            <hr>

                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <?php _trans('custom_fields'); ?>
                                </div>
                                <div class="panel-body">
                                    <div class="row">
                                        <div class="col-xs-6">
                                            <?php $i = 0; ?>
                                            <?php foreach ($custom_fields as $custom_field): ?>
                                                <?php if ($custom_field->custom_field_location != 0) {
                                                    continue;
                                                } ?>
                                                <?php $i++; ?>
                                                <?php if ($i % 2 != 0): ?>
                                                    <?php print_field($this->mdl_incoms, $custom_field, $cv); ?>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        </div>
                                        <div class="col-xs-6">
                                            <?php $i = 0; ?>
                                            <?php foreach ($custom_fields as $custom_field): ?>
                                                <?php if ($custom_field->custom_field_location != 0) {
                                                    continue;
                                                } ?>
                                                <?php $i++; ?>
                                                <?php if ($i % 2 == 0): ?>
                                                    <?php print_field($this->mdl_incoms, $custom_field, $cv); ?>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
    </div>
</div>

<?php $this->layout->load_view('upload/dropzone-incom-scripts'); ?>
