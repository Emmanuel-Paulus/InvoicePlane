<form method="post">

    <input type="hidden" name="<?php echo $this->config->item('csrf_token_name'); ?>"
           value="<?php echo $this->security->get_csrf_hash() ?>">

    <div id="headerbar">
        <h1 class="headerbar-title"><?php _trans('materials_form'); ?></h1>
        <?php $this->layout->load_view('layout/header_buttons'); ?>
    </div>

    <div id="content">

        <div class="row">
            <div class="col-xs-12 col-md-6">

                <?php $this->layout->load_view('layout/alerts'); ?>

                <div class="panel panel-default">
                    <div class="panel-heading">

                        <?php if ($this->mdl_materials->form_value('material_id')) : ?>
                            #<?php echo $this->mdl_materials->form_value('material_id'); ?>&nbsp;
                            <?php echo $this->mdl_materials->form_value('material_name', true); ?>
                        <?php else : ?>
                            <?php _trans('new_material'); ?>
                        <?php endif; ?>

                    </div>
                    <div class="panel-body">

                        <div class="form-group">
                            <label for="material_name">
                                <?php _trans('material_name'); ?>
                            </label>

                            <input type="text" name="material_name" id="material_name" class="form-control" required
                                   value="<?php echo $this->mdl_materials->form_value('material_name', true); ?>">
                        </div>

                        <div class="form-group">
                            <label for="family_id">
                                <?php _trans('family'); ?>
                            </label>

                            <select name="family_id" id="family_id" class="form-control simple-select">
                                <option value="0"><?php _trans('select_family'); ?></option>
                                <?php foreach ($families as $family) { ?>
                                    <option value="<?php echo $family->family_id; ?>"
                                    <?php check_select($this->mdl_materials->form_value('family_id'), $family->family_id) ?>
                                            ><?php echo $family->family_name; ?></option>
                                        <?php } ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="material_description">
                                <?php _trans('material_description'); ?>
                            </label>

                            <textarea name="material_description" id="material_description" class="form-control"
                                      rows="3"><?php echo $this->mdl_materials->form_value('material_description', true); ?></textarea>
                        </div>

                        <div class="form-group">
                            <label for="material_price">
                                <?php _trans('material_price'); ?>
                            </label>

                            <div class="input-group has-feedback">
                                <input type="text" name="material_price" id="material_price" class="form-control"
                                       value="<?php echo format_amount($this->mdl_materials->form_value('material_price')); ?>">
                                <span class="input-group-addon"><?php echo get_setting('currency_symbol'); ?></span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="material_price_amount">
                                <?php _trans('material_price_amount'); ?>
                            </label>

                            <div class="input-group has-feedback">
                                <input type="text" name="material_price_amount" id="material_price_amount" class="form-control"
                                       value="<?php echo $this->mdl_materials->form_value('material_price_amount'); ?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="material_pric_descr">
                                <?php _trans('material_price_descr'); ?>
                            </label>

                            <div class="input-group has-feedback">
                                <input type="text" name="material_price_descr" id="material_price_descr" class="form-control"
                                       value="<?php echo $this->mdl_materials->form_value('material_price_descr'); ?>">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xs-12 col-md-6">

                <div class="panel panel-default">
                    <div class="panel-heading">
                        <?php _trans('extra_information'); ?>
                    </div>
                    <div class="panel-body">

                        <div class="form-group">
                            <label for="material_provider_name">
                                <?php _trans('material_provider_name'); ?>
                            </label>

                            <input type="text" name="material_provider_name" id="material_provider_name" class="form-control"
                                   value="<?php echo $this->mdl_materials->form_value('material_provider_name', true); ?>">
                        </div>

                        <div class="form-group">
                            <label for="material_url">
                                <?php _trans('material_url'); ?>
                            </label>

                            <input type="text" name="material_url" id="material_url" class="form-control"
                                   value="<?php echo $this->mdl_materials->form_value('material_url', true); ?>">
                        </div>

                        <div class="form-group">
                            <label for="picture_id">
                                <?php _trans('picture'); ?>
                            </label>
                            <?php $this->mdl_pictures->ImageBlock($this->mdl_materials->form_value('picture_id')); ?>
                            <?php $this->mdl_pictures->SelectBlock($this->mdl_materials->form_value('picture_id')); ?>
                        </div>
                        
                    </div>
                </div>
            </div>

        </div>

    </div>

</form>
