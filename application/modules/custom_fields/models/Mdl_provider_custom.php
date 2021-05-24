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
 * Class Mdl_Provider_Custom
 */
class Mdl_Provider_Custom extends Validator
{
    public static $positions = array(
        'custom_fields',
        'address',
        'contact_information',
        'personal_information',
        'tax_information'
    );
    public $table = 'ip_provider_custom';
    public $primary_key = 'ip_provider_custom.provider_custom_id';

    public function default_select()
    {
        $this->db->select('SQL_CALC_FOUND_ROWS ip_provider_custom.*, ip_custom_fields.*', false);
    }

    public function default_order_by()
    {
        $this->db->order_by('custom_field_table ASC, custom_field_order ASC, custom_field_label ASC');
    }

    public function default_join()
    {
        $this->db->join('ip_custom_fields', 'ip_provider_custom.provider_custom_fieldid = ip_custom_fields.custom_field_id', 'inner');
    }

    /**
     * @param integer $provider_id
     * @param array $db_array
     * @return bool|string
     */
    public function save_custom($provider_id, $db_array)
    {
        $result = $this->validate($db_array);

        if ($result === true) {
            $form_data = isset($this->_formdata) ? $this->_formdata : null;

            if (is_null($form_data)) {
                return true;
            }

            $provider_custom_id = null;
            $db_array['provider_id'] = $provider_id;

            foreach ($form_data as $key => $value) {
                $db_array = array(
                    'provider_id' => $provider_id,
                    'provider_custom_fieldid' => $key,
                    'provider_custom_fieldvalue' => $value
                );

                $provider_custom = $this->where('provider_id', $provider_id)->where('provider_custom_fieldid', $key)->get();

                if ($provider_custom->num_rows()) {
                    $provider_custom_id = $provider_custom->row()->provider_custom_id;
                }

                parent::save($provider_custom_id, $db_array);
            }

            return true;
        }

        return $result;
    }

    /**
     * @param null $id
     * @return void
     */
    public function prep_form($id = null)
    {
        if ($id) {
            $values = $this->get_by_provider($id)->result();
            $this->load->helper('custom_values_helper');
            $this->load->module('custom_fields/mdl_custom_fields');

            if ($values != null) {
                foreach ($values as $value) {
                    $type = $value->custom_field_type;
                    if ($type != null) {
                        $nicename = Mdl_Custom_Fields::get_nicename(
                            $type
                        );
                        $formatted = call_user_func("format_" . $nicename, $value->provider_custom_fieldvalue);
                        $this->set_form_value('cf_' . $value->custom_field_id, $formatted);
                    }
                }
            }

            parent::prep_form($id);
        }
    }

    /**
     * @param integer $provider_id
     * @return $this
     */
    public function get_by_provider($provider_id)
    {
        $this->where('provider_id', $provider_id);
        return $this->get();
    }

    /**
     * @param integer $provider_id
     * @return $this
     */
    public function by_id($provider_id)
    {
        $this->db->where('ip_provider_custom.provider_id', $provider_id);
        return $this;
    }

    /**
     * @param integer $provider_id
     * @return mixed
     */
    public function get_by_clid($provider_id)
    {
        $result = $this->where('ip_provider_custom.provider_id', $provider_id)->get()->result();
        return $result;
    }

    /**
     * @return array
     */
    public function db_array()
    {
        $db_array = parent::db_array();
        $this->load->module('custom_fields/mdl_custom_fields');
        $fields = $this->mdl_custom_fields->result();

        foreach ($fields as $field) {
            if ($field->custom_field_type == "DATE") {
                $db_array[$field->custom_field_column] = date_to_mysql(
                    $db_array[$field->custom_field_column]
                );
            } elseif ($field->custom_field_type == "MULTIPLE-CHOICE") {
                $db_array[$field->custom_field_column] = implode(",", $db_array[$field->custom_field_column]);
            }
        }

        return $db_array;
    }

}
