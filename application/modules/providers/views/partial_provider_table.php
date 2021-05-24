<div class="table-responsive">
    <table class="table table-hover table-striped">
        <thead>
        <tr>
            <th><?php _trans('active'); ?></th>
            <th><?php _trans('provider_name'); ?></th>
            <th><?php _trans('email_address'); ?></th>
            <th><?php _trans('phone_number'); ?></th>
            <th><?php _trans('options'); ?></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($records as $provider) : ?>
            <tr>
				<td>
					<?php echo ($provider->provider_active) ? '<span class="label active">' . trans('yes') . '</span>' : '<span class="label inactive">' . trans('no') . '</span>'; ?>
				</td>
                <td><?php echo anchor('providers/view/' . $provider->provider_id, htmlsc(format_provider($provider))); ?></td>
                <td><?php _htmlsc($provider->provider_email); ?></td>
                <td><?php _htmlsc($provider->provider_phone ? $provider->provider_phone : ($provider->provider_mobile ? $provider->provider_mobile : '')); ?></td>
                <td>
                    <div class="options btn-group">
                        <a class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown" href="#">
                            <i class="fa fa-cog"></i> <?php _trans('options'); ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <a href="<?php echo site_url('providers/view/' . $provider->provider_id); ?>">
                                    <i class="fa fa-eye fa-margin"></i> <?php _trans('view'); ?>
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo site_url('providers/form/' . $provider->provider_id); ?>">
                                    <i class="fa fa-edit fa-margin"></i> <?php _trans('edit'); ?>
                                </a>
                            </li>
                            <li>
                                <a href="#" class="provider-create-incom"
                                   data-provider-id="<?php echo $provider->provider_id; ?>">
                                    <i class="fa fa-file fa-margin"></i> <?php _trans('create_incom'); ?>
                                </a>
                            </li>
                            <li>
                                <form action="<?php echo site_url('providers/delete/' . $provider->provider_id); ?>"
                                      method="POST">
                                    <?php _csrf_field(); ?>
                                    <button type="submit" class="dropdown-button"
                                            onclick="return confirm('<?php _trans('delete_provider_warning'); ?>');">
                                        <i class="fa fa-trash-o fa-margin"></i> <?php _trans('delete'); ?>
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
