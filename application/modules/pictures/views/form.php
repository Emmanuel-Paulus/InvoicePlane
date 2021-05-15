<form enctype="multipart/form-data" method="POST">

    <input type="hidden" name="<?php echo $this->config->item('csrf_token_name'); ?>"
           value="<?php echo $this->security->get_csrf_hash() ?>">

    <div id="headerbar">
        <h1 class="headerbar-title"><?php _trans('pictures_form'); ?></h1>
        <?php $this->layout->load_view('layout/header_buttons'); ?>
    </div>

    <div id="content">

        <div class="row">
            <div class="col-xs-12 col-md-6">

                <?php $this->layout->load_view('layout/alerts'); ?>

                <div class="panel panel-default">
                    <div class="panel-heading">

                        <?php if ($this->mdl_pictures->form_value('picture_id')) : ?>
                            #<?php echo $this->mdl_pictures->form_value('picture_id'); ?>&nbsp;
                            <?php echo $this->mdl_pictures->form_value('picture_name', true); ?>
                        <?php else : ?>
                            <?php _trans('new_picture'); ?>
                        <?php endif; ?>

                    </div>
                    <div class="panel-body">

                        <div class="form-group">
                            <?php if ($this->mdl_pictures->form_value('picture_id')) : ?>
                                <?php echo $this->mdl_pictures->htmlpicture(); ?>
                            <?php else : ?>
                                <label for="picture">
                                    <?php _trans('picture'); ?>
                                </label>
                                <input type="hidden" name="MAX_FILE_SIZE" value="<?php $this->mdl_pictures->max_size();  ?>" />
                                <input type="file" name="picture" id="picture" class="form-control" required />
                            <?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label for="picture_description">
                                <?php _trans('picture_description'); ?>
                            </label>

                            <textarea name="picture_description" id="picture_description" class="form-control"
                                      rows="3"><?php echo $this->mdl_pictures->form_value('picture_description', true); ?></textarea>
                        </div>

                    </div>
                </div>
            </div>

        </div>

    </div>

</form>
