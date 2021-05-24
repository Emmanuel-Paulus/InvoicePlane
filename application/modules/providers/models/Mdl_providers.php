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
 * Class Mdl_Providers
 */
class Mdl_Providers extends Response_Model
{
    public $table = 'ip_providers';
    public $primary_key = 'ip_providers.provider_id';
    public $date_created_field = 'provider_date_created';
    public $date_modified_field = 'provider_date_modified';

    public function default_select()
    {
        $this->db->select(
            'SQL_CALC_FOUND_ROWS ' . $this->table . '.*, ' .
            'CONCAT(' . $this->table . '.provider_name, " ", ' . $this->table . '.provider_surname) as provider_fullname'
            , false);
    }

    public function default_order_by()
    {
        $this->db->order_by('ip_providers.provider_name');
    }

    public function validation_rules()
    {
        return array(
            'provider_name' => array(
                'field' => 'provider_name',
                'label' => trans('provider_name'),
                'rules' => 'required'
            ),
            'provider_surname' => array(
                'field' => 'provider_surname',
                'label' => trans('provider_surname')
            ),
            'provider_active' => array(
                'field' => 'provider_active'
            ),
            'provider_language' => array(
                'field' => 'provider_language',
                'label' => trans('language'),
            ),
            'provider_address_1' => array(
                'field' => 'provider_address_1'
            ),
            'provider_address_2' => array(
                'field' => 'provider_address_2'
            ),
            'provider_city' => array(
                'field' => 'provider_city'
            ),
            'provider_state' => array(
                'field' => 'provider_state'
            ),
            'provider_zip' => array(
                'field' => 'provider_zip'
            ),
            'provider_country' => array(
                'field' => 'provider_country'
            ),
            'provider_phone' => array(
                'field' => 'provider_phone'
            ),
            'provider_fax' => array(
                'field' => 'provider_fax'
            ),
            'provider_mobile' => array(
                'field' => 'provider_mobile'
            ),
            'provider_email' => array(
                'field' => 'provider_email'
            ),
            'provider_web' => array(
                'field' => 'provider_web'
            ),
            'provider_vat_id' => array(
                'field' => 'provider_vat_id'
            ),
            'provider_tax_code' => array(
                'field' => 'provider_tax_code'
            ),
            // SUMEX
            'provider_birthdate' => array(
                'field' => 'provider_birthdate',
                'rules' => 'callback_convert_date'
            ),
            'provider_gender' => array(
                'field' => 'provider_gender'
            ),
            'provider_avs' => array(
                'field' => 'provider_avs',
                'label' => trans('sumex_ssn'),
                'rules' => 'callback_fix_avs'
            ),
            'provider_insurednumber' => array(
                'field' => 'provider_insurednumber',
                'label' => trans('sumex_insurednumber')
            ),
            'provider_veka' => array(
                'field' => 'provider_veka',
                'label' => trans('sumex_veka')
            ),
        );
    }

    /**
     * @param int $amount
     * @return mixed
     */
    function get_latest($amount = 10)
    {
        return $this->mdl_providers
            ->where('provider_active', 1)
            ->order_by('provider_id', 'DESC')
            ->limit($amount)
            ->get()
            ->result();
    }

    /**
     * @param $input
     * @return string
     */
    function fix_avs($input)
    {
        if ($input != "") {
            if (preg_match('/(\d{3})\.(\d{4})\.(\d{4})\.(\d{2})/', $input, $matches)) {
                return $matches[1] . $matches[2] . $matches[3] . $matches[4];
            } else if (preg_match('/^\d{13}$/', $input)) {
                return $input;
            }
        }

        return "";
    }

    function convert_date($input)
    {
        $this->load->helper('date_helper');

        if ($input == '') {
            return '';
        }

        return date_to_mysql($input);
    }

    public function db_array()
    {
        $db_array = parent::db_array();

        if (!isset($db_array['provider_active'])) {
            $db_array['provider_active'] = 0;
        }

        return $db_array;
    }

    /**
     * @param int $id
     */
    public function delete($id)
    {
        parent::delete($id);

        $this->load->helper('orphan');
        delete_orphans();
    }

    /**
     * Returns provider_id of existing provider
     *
     * @param $provider_name
     * @return int|null
     */
    public function provider_lookup($provider_name)
    {
        $provider = $this->mdl_providers->where('provider_name', $provider_name)->get();

        if ($provider->num_rows()) {
            $provider_id = $provider->row()->provider_id;
        } else {
            $db_array = array(
                'provider_name' => $provider_name
            );

            $provider_id = parent::save(null, $db_array);
        }

        return $provider_id;
    }

    public function is_inactive()
    {
        $this->filter_where('provider_active', 0);
        return $this;
    }

    public function is_active()
    {
        $this->filter_where('provider_active', 1);
        return $this;
    }

}
