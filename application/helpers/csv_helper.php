<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * InvoicePlane
 *
 * @author		InvoicePlane Developers & Contributors
 * @copyright	Copyright (c) 2012 - 2018 InvoicePlane.com
 * @license		https://invoiceplane.com/license.txt
 * @link		https://invoiceplane.com
 */

/**
 * Create a CSV
 * If $stream is true (default) the CSV will be displayed directly in the browser
 * otherwise will be returned as a download
 * @param $html
 * @param $filename
 * @param bool $stream
 *
 */
function csv_create(
    $data,
    $filename,
    $stream = true
) {
    // output headers so that the file is downloaded rather than displayed
    header('Content-Type: text/csv; charset=utf-8');
    if ($stream) {
        header('Content-Disposition: inline;');
    } else {
        header('Content-Disposition: attachment; filename='.$filename);
    }

    // create a file pointer connected to the output stream
    $output = fopen('php://output', 'w');
    fputs($output, $bom =( chr(0xEF) . chr(0xBB) . chr(0xBF) ));
    foreach($data as $row) {
        fputcsv($output, $row, ";");
    }
}
