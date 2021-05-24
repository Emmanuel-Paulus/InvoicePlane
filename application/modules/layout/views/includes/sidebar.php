<style>
    .sidebar .fa-users:after {content:"<?php _trans('clients'); ?>";}
    .sidebar .fa-file:after {content:"<?php _trans('quotes'); ?>";}
    .sidebar .fa-file-text:after {content:"<?php _trans('invoices'); ?>";}
    .sidebar .fa-money:after {content:"<?php _trans('payments'); ?>";}
    .sidebar .fa-database:after {content:"<?php _trans('products'); ?>";}
    .sidebar .fa-adjust:after {content:"<?php _trans('materials'); ?>";}
    .sidebar .fa-image:after {content:"<?php _trans('pictures'); ?>";}
    .sidebar .fa-briefcase:after {content:"<?php _trans('incoms'); ?>";}
    .sidebar .fa-industry:after {content:"<?php _trans('providers'); ?>";}
    .sidebar .fa-check-square-o:after {content:"<?php _trans('tasks'); ?>";}
    .sidebar .fa-cogs:after {content:"<?php _trans('system_settings'); ?>";}
</style>

<div class="sidebar hidden-xs">
    <ul>
        <li>
            <a href="<?php echo site_url('clients/index'); ?>" title="<?php _trans('clients'); ?>"
               class="tip" data-placement="right">
                <i class="fa fa-users"></i>
            </a>
        </li>
        <li>
            <a href="<?php echo site_url('quotes/index'); ?>" title="<?php _trans('quotes'); ?>"
               class="tip" data-placement="right">
                <i class="fa fa-file"></i>
            </a>
        </li>
        <li>
            <a href="<?php echo site_url('invoices/index'); ?>" title="<?php _trans('invoices'); ?>"
               class="tip" data-placement="right">
                <i class="fa fa-file-text"></i>
            </a>
        </li>
        <li>
            <a href="<?php echo site_url('payments/index'); ?>" title="<?php _trans('payments'); ?>"
               class="tip" data-placement="right">
                <i class="fa fa-money"></i>
            </a>
        </li>
        <li>
            <a href="<?php echo site_url('products/index'); ?>" title="<?php _trans('products'); ?>"
               class="tip" data-placement="right">
                <i class="fa fa-database"></i>
            </a>
        </li>
        <li>
            <a href="<?php echo site_url('materials/index'); ?>" title="<?php _trans('materials'); ?>"
               class="tip" data-placement="right">
                <i class="fa fa-adjust"></i>
            </a>
        </li>
        <li>
            <a href="<?php echo site_url('pictures/index'); ?>" title="<?php _trans('pictures'); ?>"
               class="tip" data-placement="right">
                <i class="fa fa-image"></i>
            </a>
        </li>
        <li>
            <a href="<?php echo site_url('incoms/index'); ?>" title="<?php _trans('incoms'); ?>"
               class="tip" data-placement="right">
                <i class="fa fa-briefcase"></i>
            </a>
        </li>
        <li>
            <a href="<?php echo site_url('providers/index'); ?>" title="<?php _trans('providers'); ?>"
               class="tip" data-placement="right">
                <i class="fa fa-industry"></i>
            </a>
        </li>
        <?php if (get_setting('projects_enabled') == 1) : ?>
            <li>
                <a href="<?php echo site_url('tasks/index'); ?>" title="<?php _trans('tasks'); ?>"
                   class="tip" data-placement="right">
                    <i class="fa fa-check-square-o"></i>
                </a>
            </li>
        <?php endif; ?>
        <li>
            <a href="<?php echo site_url('settings'); ?>" title="<?php _trans('system_settings'); ?>"
               class="tip" data-placement="right">
                <i class="fa fa-cogs"></i>
            </a>
        </li>
    </ul>
</div>
