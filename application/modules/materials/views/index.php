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

</div>

<div id="content" class="table-content">

    <?php $this->layout->load_view('layout/alerts'); ?>

    <div class="table-responsive">
        <table class="table table-hover table-striped">

            <thead>
            <tr>
                <th><?php _trans('product'); ?></th>
                <th><?php _trans('material_name'); ?></th>
                <th><?php _trans('material_description'); ?></th>
                <th><?php _trans('material_price'); ?></th>
                <th><?php _trans('material_provider_name'); ?></th>
                <th><?php _trans('options'); ?></th>
            </tr>
            </thead>

            <tbody>
            <?php foreach ($materials as $material) { ?>
                <tr>
                    <td><?php _htmlsc($material->product_name); ?></td>
                    <td><?php _htmlsc($material->material_name); ?></td>
                    <td><?php echo nl2br(htmlsc($material->material_description)); ?></td>
                    <td class="amount"><?php echo format_currency($material->material_price); ?></td>
                    <td><?php echo nl2br(htmlsc($material->material_provider_name)); ?></td>
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
