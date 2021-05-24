<?php
$cv = $this->controller->view_data['custom_values'];
?>

<script type="text/javascript">
    $(function () {
        $("#provider_country").select2({
            placeholder: "<?php _trans('country'); ?>",
            allowClear: true
        });
    });
</script>

<form method="post">

    <input type="hidden" name="<?php echo $this->config->item('csrf_token_name'); ?>"
           value="<?php echo $this->security->get_csrf_hash() ?>">

    <div id="headerbar">
        <h1 class="headerbar-title"><?php _trans('provider_form'); ?></h1>
        <?php $this->layout->load_view('layout/header_buttons'); ?>
    </div>

    <div id="content">

        <?php $this->layout->load_view('layout/alerts'); ?>

        <input class="hidden" name="is_update" type="hidden"
            <?php if ($this->mdl_providers->form_value('is_update')) {
                echo 'value="1"';
            } else {
                echo 'value="0"';
            } ?>
        >

        <div class="row">
            <div class="col-xs-12 col-sm-6">

                <div class="panel panel-default">
                    <div class="panel-heading form-inline clearfix">
                        <?php _trans('personal_information'); ?>

                        <div class="pull-right">
                            <label for="provider_active" class="control-label">
                                <?php _trans('active_provider'); ?>
                                <input id="provider_active" name="provider_active" type="checkbox" value="1"
                                    <?php if ($this->mdl_providers->form_value('provider_active') == 1
                                        || !is_numeric($this->mdl_providers->form_value('provider_active'))
                                    ) {
                                        echo 'checked="checked"';
                                    } ?>>
                            </label>
                        </div>
                    </div>

                    <div class="panel-body">

                        <div class="form-group">
                            <label for="provider_name">
                                <?php _trans('provider_name'); ?>
                            </label>
                            <input id="provider_name" name="provider_name" type="text" class="form-control" required
                                   autofocus
                                   value="<?php echo $this->mdl_providers->form_value('provider_name', true); ?>">
                        </div>

                        <div class="form-group">
                            <label for="provider_surname">
                                <?php _trans('provider_surname_optional'); ?>
                            </label>
                            <input id="provider_surname" name="provider_surname" type="text" class="form-control"
                                   value="<?php echo $this->mdl_providers->form_value('provider_surname', true); ?>">
                        </div>

                        <div class="form-group no-margin">
                            <label for="provider_language">
                                <?php _trans('language'); ?>
                            </label>
                            <select name="provider_language" id="provider_language" class="form-control simple-select">
                                <option value="system">
                                    <?php _trans('use_system_language') ?>
                                </option>
                                <?php foreach ($languages as $language) {
                                    $provider_lang = $this->mdl_providers->form_value('provider_language');
                                    ?>
                                    <option value="<?php echo $language; ?>"
                                        <?php check_select($provider_lang, $language) ?>>
                                        <?php echo ucfirst($language); ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>

                    </div>
                </div>

            </div>
        </div>

        <div class="row">
            <div class="col-xs-12 col-sm-6">
                <div class="panel panel-default">

                    <div class="panel-heading">
                        <?php _trans('address'); ?>
                    </div>

                    <div class="panel-body">
                        <div class="form-group">
                            <label for="provider_address_1"><?php _trans('street_address'); ?></label>

                            <div class="controls">
                                <input type="text" name="provider_address_1" id="provider_address_1" class="form-control"
                                       value="<?php echo $this->mdl_providers->form_value('provider_address_1', true); ?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="provider_address_2"><?php _trans('street_address_2'); ?></label>

                            <div class="controls">
                                <input type="text" name="provider_address_2" id="provider_address_2" class="form-control"
                                       value="<?php echo $this->mdl_providers->form_value('provider_address_2', true); ?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="provider_city"><?php _trans('city'); ?></label>

                            <div class="controls">
                                <input type="text" name="provider_city" id="provider_city" class="form-control"
                                       value="<?php echo $this->mdl_providers->form_value('provider_city', true); ?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="provider_state"><?php _trans('state'); ?></label>

                            <div class="controls">
                                <input type="text" name="provider_state" id="provider_state" class="form-control"
                                       value="<?php echo $this->mdl_providers->form_value('provider_state', true); ?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="provider_zip"><?php _trans('zip_code'); ?></label>

                            <div class="controls">
                                <input type="text" name="provider_zip" id="provider_zip" class="form-control"
                                       value="<?php echo $this->mdl_providers->form_value('provider_zip', true); ?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="provider_country"><?php _trans('country'); ?></label>

                            <div class="controls">
                                <select name="provider_country" id="provider_country" class="form-control">
                                    <option value=""><?php _trans('none'); ?></option>
                                    <?php foreach ($countries as $cldr => $country) { ?>
                                        <option value="<?php echo $cldr; ?>"
                                            <?php check_select($selected_country, $cldr); ?>
                                        ><?php echo $country ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>

                        <!-- Custom Fields -->
                        <?php foreach ($custom_fields as $custom_field): ?>
                            <?php if ($custom_field->custom_field_location != 1) {
                                continue;
                            } ?>
                            <?php print_field($this->mdl_providers, $custom_field, $cv); ?>
                        <?php endforeach; ?>
                    </div>

                </div>

            </div>
            <div class="col-xs-12 col-sm-6">

                <div class="panel panel-default">

                    <div class="panel-heading">
                        <?php _trans('contact_information'); ?>
                    </div>

                    <div class="panel-body">
                        <div class="form-group">
                            <label for="provider_phone"><?php _trans('phone_number'); ?></label>

                            <div class="controls">
                                <input type="text" name="provider_phone" id="provider_phone" class="form-control"
                                       value="<?php echo $this->mdl_providers->form_value('provider_phone', true); ?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="provider_fax"><?php _trans('fax_number'); ?></label>

                            <div class="controls">
                                <input type="text" name="provider_fax" id="provider_fax" class="form-control"
                                       value="<?php echo $this->mdl_providers->form_value('provider_fax', true); ?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="provider_mobile"><?php _trans('mobile_number'); ?></label>

                            <div class="controls">
                                <input type="text" name="provider_mobile" id="provider_mobile" class="form-control"
                                       value="<?php echo $this->mdl_providers->form_value('provider_mobile', true); ?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="provider_email"><?php _trans('email_address'); ?></label>

                            <div class="controls">
                                <input type="text" name="provider_email" id="provider_email" class="form-control"
                                       value="<?php echo $this->mdl_providers->form_value('provider_email', true); ?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="provider_web"><?php _trans('web_address'); ?></label>

                            <div class="controls">
                                <input type="text" name="provider_web" id="provider_web" class="form-control"
                                       value="<?php echo $this->mdl_providers->form_value('provider_web', true); ?>">
                            </div>
                        </div>

                        <!-- Custom fields -->
                        <?php foreach ($custom_fields as $custom_field): ?>
                            <?php if ($custom_field->custom_field_location != 2) {
                                continue;
                            } ?>
                            <?php print_field($this->mdl_providers, $custom_field, $cv); ?>
                        <?php endforeach; ?>
                    </div>

                </div>

            </div>
        </div>

        <div class="row">
            <div class="col-xs-12 col-sm-6">

                <div class="panel panel-default">

                    <div class="panel-heading">
                        <?php _trans('personal_information'); ?>
                    </div>

                    <div class="panel-body">
                        <div class="form-group">
                            <label for="provider_gender"><?php _trans('gender'); ?></label>

                            <div class="controls">
                                <select name="provider_gender" id="provider_gender"
                                	class="form-control simple-select" data-minimum-results-for-search="Infinity">
                                    <?php
                                    $genders = array(
                                        trans('gender_male'),
                                        trans('gender_female'),
                                        trans('gender_other'),
                                    );
                                    foreach ($genders as $key => $val) { ?>
                                        <option value=" <?php echo $key; ?>" <?php check_select($key, $this->mdl_providers->form_value('provider_gender')) ?>>
                                            <?php echo $val; ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group has-feedback">
                            <label for="provider_birthdate"><?php _trans('birthdate'); ?></label>
                            <?php
                            $bdate = $this->mdl_providers->form_value('provider_birthdate');
                            if ($bdate && $bdate != "0000-00-00") {
                                $bdate = date_from_mysql($bdate);
                            } else {
                                $bdate = '';
                            }
                            ?>
                            <div class="input-group">
                                <input type="text" name="provider_birthdate" id="provider_birthdate"
                                       class="form-control datepicker"
                                       value="<?php _htmlsc($bdate); ?>">
                                <span class="input-group-addon">
                                <i class="fa fa-calendar fa-fw"></i>
                            </span>
                            </div>
                        </div>

                        <?php if ($this->mdl_settings->setting('sumex') == '1'): ?>

                            <div class="form-group">
                                <label for="provider_avs"><?php _trans('sumex_ssn'); ?></label>
                                <?php $avs = $this->mdl_providers->form_value('provider_avs'); ?>
                                <div class="controls">
                                    <input type="text" name="provider_avs" id="provider_avs" class="form-control"
                                           value="<?php echo htmlspecialchars(format_avs($avs)); ?>">
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="provider_insurednumber"><?php _trans('sumex_insurednumber'); ?></label>
                                <?php $insuredNumber = $this->mdl_providers->form_value('provider_insurednumber'); ?>
                                <div class="controls">
                                    <input type="text" name="provider_insurednumber" id="provider_insurednumber"
                                           class="form-control"
                                           value="<?php echo htmlentities($insuredNumber); ?>">
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="provider_veka"><?php _trans('sumex_veka'); ?></label>
                                <?php $veka = $this->mdl_providers->form_value('provider_veka'); ?>
                                <div class="controls">
                                    <input type="text" name="provider_veka" id="provider_veka" class="form-control"
                                           value="<?php echo htmlentities($veka); ?>">
                                </div>
                            </div>

                        <?php endif; ?>

                        <!-- Custom fields -->
                        <?php foreach ($custom_fields as $custom_field): ?>
                            <?php if ($custom_field->custom_field_location != 3) {
                                continue;
                            } ?>
                            <?php print_field($this->mdl_providers, $custom_field, $cv); ?>
                        <?php endforeach; ?>
                    </div>

                </div>

            </div>
            <div class="col-xs-12 col-sm-6">

                <div class="panel panel-default">
                    <div class="panel-heading">
                        <?php _trans('tax_information'); ?>
                    </div>

                    <div class="panel-body">
                        <div class="form-group">
                            <label for="provider_vat_id"><?php _trans('vat_id'); ?></label>

                            <div class="controls">
                                <input type="text" name="provider_vat_id" id="provider_vat_id" class="form-control"
                                       value="<?php echo $this->mdl_providers->form_value('provider_vat_id', true); ?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="provider_tax_code"><?php _trans('tax_code'); ?></label>

                            <div class="controls">
                                <input type="text" name="provider_tax_code" id="provider_tax_code" class="form-control"
                                       value="<?php echo $this->mdl_providers->form_value('provider_tax_code', true); ?>">
                            </div>
                        </div>

                        <!-- Custom fields -->
                        <?php foreach ($custom_fields as $custom_field): ?>
                            <?php if ($custom_field->custom_field_location != 4) {
                                continue;
                            } ?>
                            <?php print_field($this->mdl_providers, $custom_field, $cv); ?>
                        <?php endforeach; ?>
                    </div>

                </div>

            </div>
        </div>
        <?php if ($custom_fields): ?>
            <div class="row">
                <div class="col-xs-12 col-md-6">

                    <div class="panel panel-default">

                        <div class="panel-heading">
                            <?php _trans('custom_fields'); ?>
                        </div>

                        <div class="panel-body">
                            <?php foreach ($custom_fields as $custom_field): ?>
                                <?php if ($custom_field->custom_field_location != 0) {
                                    continue;
                                }
                                print_field($this->mdl_providers, $custom_field, $cv);
                                ?>
                            <?php endforeach; ?>
                        </div>

                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</form>
