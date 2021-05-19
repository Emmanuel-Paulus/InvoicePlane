<?php  $class2 = "downborder"; ?>
<!DOCTYPE html>
<html lang="<?php echo trans('cldr'); ?>">
    <head>
        <title><?php echo trans('materiallist'); ?></title>
        <link rel="stylesheet" href="<?php echo base_url(); ?>assets/<?php echo get_setting('system_theme', 'invoiceplane'); ?>/css/reports.css" type="text/css">
        <style>
            table {
                border-spacing:0;
                border-collapse: collapse;
            }
            .fillforimage {
                height: 8.1em;
            }
            table td, table td * {
                vertical-align: top;
                border-bottom: 0px none black;
            }
            table td.downborder {
                border-bottom: 1px dashed black;
            }
            table th {
                border-bottom: 1px solid black;
                padding: 1px 4px;
            }
            img {
                margin: 1px 1px 1px 1px;
            }
            .align-right {
                text-align: right;
            }
            .align-center {
                text-align: center;
            }
        </style>
    </head>
    <body>

        <h3 class="report_title">
            <?php echo trans('materiallist'); ?><br/>
        </h3>

        <table cellspacing="0">
            <tr>
                <th width="2%"> </th>
                <th> </th>
                <th><?php _trans('material_name'); ?></th>
                <th><?php _trans('material_description'); ?></th>
                <th class="align-right" width="10%"><?php _trans('prod_matr_price'); ?></th>
                <th class="align-right" width="5%"><?php _trans('prod_matr_amount'); ?></th>
                <th width="10%"><?php _trans('material_price_descr'); ?></th>
            </tr>            
            <?php $count=0; foreach ($materials as $material) { $count++;?>
                <tr>
                    <td width="2%" class="align-right"><b><?php _htmlsc($count); ?></b> </td>
                    <?php if ($material->picture_id > 0) {  $class2 = "noborder"; ?>
                        <td class="align-center downborder" rowspan="3">
                            <?php echo $this->mdl_pictures->pdfpicture($material->picture_id); ?>
                        </td>
                    <?php } else { $class2 = "downborder"; ?>
                        <td  class="downborder" rowspan="2"></td>
                    <?php } ?>
                    <td><?php _htmlsc($material->material_name); ?></td>
                    <td><?php echo nl2br(htmlsc($material->material_description)); ?></td>
                    <td class="amount align-right"><?php echo format_currency($material->prod_matr_amount * $material->material_price / $material->material_price_amount); ?></td>
                    <td class="align-right"><?php _htmlsc($material->prod_matr_amount); ?></td>
                    <td class=""><?php _htmlsc($material->material_price_descr); ?></td>
                </tr>
                <tr>
                    <td class="<?php echo $class2; ?>"></td>
                    <td class="<?php echo $class2; ?>" colspan="2"><?php echo nl2br(htmlsc($material->material_provider_name)); ?>
                        <?php
                        if ($material->material_url) {
                            echo '<a href="' . $material->material_url . '" target="_blank"><i class="fa fa-link fa-margin"></i> &nbsp;</a>';
                        }
                        ?>
                    </td>
                    <td class="<?php echo $class2; ?> amount align-right"><?php echo format_currency($material->material_price); ?></td>
                    <td class="<?php echo $class2; ?> align-right"><?php _htmlsc($material->material_price_amount); ?></td>
                    <td class="<?php echo $class2; ?>"></td>
                <?php if ($material->picture_id > 0) { ?>
                </tr>
                <tr>
                    <td class="downborder"></td>
                    <td class="downborder fillforimage" colspan="5"></td>
                <?php } ?>
                </tr>
            <?php } ?>
        </table>

    </body>
</html>
