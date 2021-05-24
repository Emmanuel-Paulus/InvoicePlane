<div class="table-responsive">
    <table class="table table-hover table-striped">

        <thead>
        <tr>
            <th><?php _trans('status'); ?></th>
            <th><?php _trans('incom'); ?></th>
            <th><?php _trans('created'); ?></th>
            <th><?php _trans('due_date'); ?></th>
            <th><?php _trans('provider_name'); ?></th>
            <th style="text-align: right; padding-right: 25px;"><?php _trans('amount'); ?></th>
            <th><?php _trans('options'); ?></th>
        </tr>
        </thead>

        <tbody>
        <?php
        $incom_idx = 1;
        $incom_count = count($incoms);
        $incom_list_split = $incom_count > 3 ? $incom_count / 2 : 9999;

        foreach ($incoms as $incom) {
            // Convert the dropdown menu to a dropup if incom is after the invoice split
            $dropup = $incom_idx > $incom_list_split ? true : false;
            ?>
            <tr>
                <td>
                    <span class="label <?php echo $incom_statuses[$incom->incom_status_id]['class']; ?>">
                        <?php echo $incom_statuses[$incom->incom_status_id]['label']; ?>
                    </span>
                </td>
                <td>
                    <a href="<?php echo site_url('incoms/view/' . $incom->incom_id); ?>"
                       title="<?php _trans('edit'); ?>">
                        <?php echo($incom->incom_number ? $incom->incom_number : $incom->incom_id); ?>
                    </a>
                </td>
                <td>
                    <?php echo date_from_mysql($incom->incom_date_created); ?>
                </td>
                <td>
                    <?php echo date_from_mysql($incom->incom_date_expires); ?>
                </td>
                <td>
                    <a href="<?php echo site_url('providers/view/' . $incom->provider_id); ?>"
                       title="<?php _trans('view_provider'); ?>">
                        <?php _htmlsc(format_provider($incom)); ?>
                    </a>
                </td>
                <td style="text-align: right; padding-right: 25px;">
                    <?php echo format_currency($incom->incom_total); ?>
                </td>
                <td>
                    <div class="options btn-group<?php echo $dropup ? ' dropup' : ''; ?>">
                        <a class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown"
                           href="#">
                            <i class="fa fa-cog"></i> <?php _trans('options'); ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <a href="<?php echo site_url('incoms/view/' . $incom->incom_id); ?>">
                                    <i class="fa fa-edit fa-margin"></i> <?php _trans('edit'); ?>
                                </a>
                            </li>
                            <li>
                                <form action="<?php echo site_url('incoms/delete/' . $incom->incom_id); ?>"
                                      method="POST">
                                    <?php _csrf_field(); ?>
                                    <button type="submit" class="dropdown-button"
                                            onclick="return confirm('<?php _trans('delete_incom_warning'); ?>');">
                                        <i class="fa fa-trash-o fa-margin"></i> <?php _trans('delete'); ?>
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </td>
            </tr>
            <?php
            $incom_idx++;
        } ?>
        </tbody>

    </table>
</div>
