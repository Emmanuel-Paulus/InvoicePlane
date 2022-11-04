<!DOCTYPE html>
<html lang="<?php echo trans('cldr'); ?>">
<head>
    <title><?php echo trans('sales_by_client'); ?></title>
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/<?php echo get_setting('system_theme', 'invoiceplane'); ?>/css/reports.css" type="text/css">
</head>
<body>

<h3 class="report_title">
    <?php echo trans('sales_by_client'); ?><br/>
    <small><?php echo $from_date . ' - ' . $to_date ?></small>
</h3>

<table>
    <tr>
        <th><?php echo trans('client'); ?></th>
        <th><?php echo trans('vat_id'); ?></th>
        <!-- <th class="amount"><?php echo trans('invoice_count'); ?></th> -->
        <th class="amount"><?php echo trans('sales'); ?></th>
        <!-- <th class="amount"><?php echo trans('sales_with_tax'); ?></th> -->
        <th class="amount"><?php echo trans('sales_tax'); ?></th>
    </tr>
    <?php foreach ($results as $result) { ?>
        <tr>
            <td><?php _htmlsc(format_client($result)); ?></td>
            <td><?php _htmlsc($result->client_vat_id); ?></td>
            <!-- <td class="amount"><?php echo $result->invoice_count; ?></td> -->
            <td class="amount"><?php echo format_currency($result->sales); ?></td>
            <!-- <td class="amount"><?php echo format_currency($result->sales_with_tax); ?></td> -->
            <td class="amount"><?php echo format_currency($result->sales_tax); ?></td>
        </tr>
    <?php } ?>
</table>
<br/><br/>
<a href="https://financien.belgium.be/nl/E-services/Intervat/klantenlijst-intracommunautaire-opgave">FOD Financieen - Klantenlijst</a><br/>
<a href="https://financien.belgium.be/sites/default/files/downloads/165-excel-lc.xlsx" download>In te vullen Excel voor BTW</a>

</body>
</html>
