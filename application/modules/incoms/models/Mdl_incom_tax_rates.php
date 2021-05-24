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
 * Class Mdl_Incom_Tax_Rates
 */
class Mdl_Incom_Tax_Rates extends Response_Model
{
    public $table = 'ip_incom_tax_rates';
    public $primary_key = 'ip_incom_tax_rates.incom_tax_rate_id';

    public function default_select()
    {
        $this->db->select('ip_tax_rates.tax_rate_name AS incom_tax_rate_name');
        $this->db->select('ip_tax_rates.tax_rate_percent AS incom_tax_rate_percent');
        $this->db->select('ip_incom_tax_rates.*');
    }

    public function default_join()
    {
        $this->db->join('ip_tax_rates', 'ip_tax_rates.tax_rate_id = ip_incom_tax_rates.tax_rate_id');
    }

    /**
     * @param null $id
     * @param null $db_array
     * @return void
     */
    public function save($id = null, $db_array = null)
    {
        parent::save($id, $db_array);

        $this->load->model('incoms/mdl_incom_amounts');

        $incom_id = $this->input->post('incom_id');

        if ($incom_id) {
            $this->mdl_incom_amounts->calculate($incom_id);
        }
    }

    /**
     * @return array
     * @return void
     */
    public function validation_rules()
    {
        return array(
            'incom_id' => array(
                'field' => 'incom_id',
                'label' => trans('incom'),
                'rules' => 'required'
            ),
            'tax_rate_id' => array(
                'field' => 'tax_rate_id',
                'label' => trans('tax_rate'),
                'rules' => 'required'
            ),
            'include_item_tax' => array(
                'field' => 'include_item_tax',
                'label' => trans('tax_rate_placement'),
                'rules' => 'required'
            )
        );
    }

}
