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
 * Class Mdl_Incom_Custom
 */
class Mdl_Incom_Custom extends Validator
{
    public static $positions = array(
        'custom_fields',
        'properties'
    );
    public $table = 'ip_incom_custom';
    public $primary_key = 'ip_incom_custom.incom_custom_id';

    public function default_select()
    {
        $this->db->select('SQL_CALC_FOUND_ROWS ip_incom_custom.*, ip_custom_fields.*', false);
    }

    public function default_join()
    {
        $this->db->join('ip_custom_fields', 'ip_incom_custom.incom_custom_fieldid = ip_custom_fields.custom_field_id');
    }

    public function default_order_by()
    {
        $this->db->order_by('custom_field_table ASC, custom_field_order ASC, custom_field_label ASC');
    }


    /**
     * @param $incom_id
     * @param $db_array
     * @return bool|string
     */
    public function save_custom($incom_id, $db_array)
    {
        $result = $this->validate($db_array);

        if ($result === true) {
            $form_data = isset($this->_formdata) ? $this->_formdata : null;

            if (is_null($form_data)) {
                return true;
            }

            $incom_custom_id = null;

            foreach ($form_data as $key => $value) {
                $db_array = array(
                    'incom_id' => $incom_id,
                    'incom_custom_fieldid' => $key,
                    'incom_custom_fieldvalue' => $value
                );

                $incom_custom = $this->where('incom_id', $incom_id)->where('incom_custom_fieldid', $key)->get();

                if ($incom_custom->num_rows()) {
                    $incom_custom_id = $incom_custom->row()->incom_custom_id;
                }

                parent::save($incom_custom_id, $db_array);
            }

            return true;
        }

        return $result;
    }

    public function by_id($incom_id)
    {
        $this->db->where('ip_incom_custom.incom_id', $incom_id);
        return $this;
    }

}
