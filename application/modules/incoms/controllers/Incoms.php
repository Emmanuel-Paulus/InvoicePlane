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
 * Class Incoms
 */
class Incoms extends Admin_Controller
{
    /**
     * Incoms constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('mdl_incoms');
    }

    public function index()
    {
        // Display all incoms by default
        redirect('incoms/status/all');
    }

    /**
     * @param string $status
     * @param int $page
     */
    public function status($status = 'all', $page = 0)
    {
        // Determine which group of incoms to load
        switch ($status) {
            case 'draft':
                $this->mdl_incoms->is_draft();
                break;
            case 'sent':
                $this->mdl_incoms->is_sent();
                break;
            case 'viewed':
                $this->mdl_incoms->is_viewed();
                break;
            case 'approved':
                $this->mdl_incoms->is_approved();
                break;
            case 'rejected':
                $this->mdl_incoms->is_rejected();
                break;
            case 'canceled':
                $this->mdl_incoms->is_canceled();
                break;
        }

        $this->mdl_incoms->paginate(site_url('incoms/status/' . $status), $page);
        $incoms = $this->mdl_incoms->result();

        $this->layout->set(
            array(
                'incoms' => $incoms,
                'status' => $status,
                'filter_display' => true,
                'filter_placeholder' => trans('filter_incoms'),
                'filter_method' => 'filter_incoms',
                'incom_statuses' => $this->mdl_incoms->statuses()
            )
        );

        $this->layout->buffer('content', 'incoms/index');
        $this->layout->render();
    }

    /**
     * @param $incom_id
     */
    public function view($incom_id)
    {
        $this->load->helper('custom_values');
        $this->load->model('mdl_incom_items');
        $this->load->model('tax_rates/mdl_tax_rates');
        $this->load->model('units/mdl_units');
        $this->load->model('mdl_incom_tax_rates');
        $this->load->model('custom_fields/mdl_custom_fields');
        $this->load->model('custom_values/mdl_custom_values');
        $this->load->model('custom_fields/mdl_incom_custom');
        $this->load->model('pictures/mdl_pictures');

        $fields = $this->mdl_incom_custom->by_id($incom_id)->get()->result();
        $this->db->reset_query();

        $incom_custom = $this->mdl_incom_custom->where('incom_id', $incom_id)->get();

        if ($incom_custom->num_rows()) {
            $incom_custom = $incom_custom->row();

            unset($incom_custom->incom_id, $incom_custom->incom_custom_id);

            foreach ($incom_custom as $key => $val) {
                $this->mdl_incoms->set_form_value('custom[' . $key . ']', $val);
            }
        }

        $incom = $this->mdl_incoms->get_by_id($incom_id);


        if (!$incom) {
            show_404();
        }

        $custom_fields = $this->mdl_custom_fields->by_table('ip_incom_custom')->get()->result();
        $custom_values = [];
        foreach ($custom_fields as $custom_field) {
            if (in_array($custom_field->custom_field_type, $this->mdl_custom_values->custom_value_fields())) {
                $values = $this->mdl_custom_values->get_by_fid($custom_field->custom_field_id)->result();
                $custom_values[$custom_field->custom_field_id] = $values;
            }
        }

        foreach ($custom_fields as $cfield) {
            foreach ($fields as $fvalue) {
                if ($fvalue->incom_custom_fieldid == $cfield->custom_field_id) {
                    // TODO: Hackish, may need a better optimization
                    $this->mdl_incoms->set_form_value(
                        'custom[' . $cfield->custom_field_id . ']',
                        $fvalue->incom_custom_fieldvalue
                    );
                    break;
                }
            }
        }

        $this->layout->set(
            array(
                'incom' => $incom,
                'items' => $this->mdl_incom_items->where('incom_id', $incom_id)->get()->result(),
                'incom_id' => $incom_id,
                'tax_rates' => $this->mdl_tax_rates->get()->result(),
                'units' => $this->mdl_units->get()->result(),
                'incom_tax_rates' => $this->mdl_incom_tax_rates->where('incom_id', $incom_id)->get()->result(),
                'custom_fields' => $custom_fields,
                'custom_values' => $custom_values,
                'custom_js_vars' => array(
                    'currency_symbol' => get_setting('currency_symbol'),
                    'currency_symbol_placement' => get_setting('currency_symbol_placement'),
                    'decimal_point' => get_setting('decimal_point')
                ),
                'incom_statuses' => $this->mdl_incoms->statuses()
            )
        );

        $this->layout->buffer(
            array(
                array('modal_delete_incom', 'incoms/modal_delete_incom'),
                array('modal_add_incom_tax', 'incoms/modal_add_incom_tax'),
                array('content', 'incoms/view')
            )
        );

        $this->layout->render();
    }

    /**
     * @param $incom_id
     */
    public function delete($incom_id)
    {
        // Delete the incom
        $this->mdl_incoms->delete($incom_id);

        // Redirect to incom index
        redirect('incoms/index');
    }

    /**
     * @param $incom_id
     * @param $incom_tax_rate_id
     */
    public function delete_incom_tax($incom_id, $incom_tax_rate_id)
    {
        $this->load->model('mdl_incom_tax_rates');
        $this->mdl_incom_tax_rates->delete($incom_tax_rate_id);

        $this->load->model('mdl_incom_amounts');
        $this->mdl_incom_amounts->calculate($incom_id);

        redirect('incoms/view/' . $incom_id);
    }

    public function recalculate_all_incoms()
    {
        $this->db->select('incom_id');
        $incom_ids = $this->db->get('ip_incoms')->result();

        $this->load->model('mdl_incom_amounts');

        foreach ($incom_ids as $incom_id) {
            $this->mdl_incom_amounts->calculate($incom_id->incom_id);
        }
    }

}
