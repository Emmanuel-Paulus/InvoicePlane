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
 * Class Providers
 */
class Providers extends Admin_Controller
{
    /**
     * Providers constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('mdl_providers');
    }

    public function index()
    {
        // Display active providers by default
        redirect('providers/status/active');
    }

    /**
     * @param string $status
     * @param int $page
     */
    public function status($status = 'active', $page = 0)
    {
        if (is_numeric(array_search($status, array('active', 'inactive')))) {
            $function = 'is_' . $status;
            $this->mdl_providers->$function();
        }

        $this->mdl_providers->paginate(site_url('providers/status/' . $status), $page);
        $providers = $this->mdl_providers->result();

        $this->layout->set(
            array(
                'records' => $providers,
                'filter_display' => true,
                'filter_placeholder' => trans('filter_providers'),
                'filter_method' => 'filter_providers'
            )
        );

        $this->layout->buffer('content', 'providers/index');
        $this->layout->render();
    }

    /**
     * @param null $id
     */
    public function form($id = null)
    {
        if ($this->input->post('btn_cancel')) {
            redirect('providers');
        }
        
        $new_provider = false;
        
        // Set validation rule based on is_update
        if ($this->input->post('is_update') == 0 && $this->input->post('provider_name') != '') {
            $check = $this->db->get_where('ip_providers', array(
                'provider_name' => $this->input->post('provider_name'),
                'provider_surname' => $this->input->post('provider_surname')
            ))->result();

            if (!empty($check)) {
                $this->session->set_flashdata('alert_error', trans('provider_already_exists'));
                redirect('providers/form');
            } else {
                $new_provider = true;
            }
        }
        
        if ($this->mdl_providers->run_validation()) {
            $id = $this->mdl_providers->save($id);
            
            $this->load->model('custom_fields/mdl_provider_custom');
            $result = $this->mdl_provider_custom->save_custom($id, $this->input->post('custom'));

            if ($result !== true) {
                $this->session->set_flashdata('alert_error', $result);
                $this->session->set_flashdata('alert_success', null);
                redirect('providers/form/' . $id);
                return;
            } else {
                redirect('providers/view/' . $id);
            }
        }

        if ($id and !$this->input->post('btn_submit')) {
            if (!$this->mdl_providers->prep_form($id)) {
                show_404();
            }

            $this->load->model('custom_fields/mdl_provider_custom');
            $this->mdl_providers->set_form_value('is_update', true);

            $provider_custom = $this->mdl_provider_custom->where('provider_id', $id)->get();

            if ($provider_custom->num_rows()) {
                $provider_custom = $provider_custom->row();

                unset($provider_custom->provider_id, $provider_custom->provider_custom_id);

                foreach ($provider_custom as $key => $val) {
                    $this->mdl_providers->set_form_value('custom[' . $key . ']', $val);
                }
            }
        } elseif ($this->input->post('btn_submit')) {
            if ($this->input->post('custom')) {
                foreach ($this->input->post('custom') as $key => $val) {
                    $this->mdl_providers->set_form_value('custom[' . $key . ']', $val);
                }
            }
        }

        $this->load->model('custom_fields/mdl_custom_fields');
        $this->load->model('custom_values/mdl_custom_values');
        $this->load->model('custom_fields/mdl_provider_custom');

        $custom_fields = $this->mdl_custom_fields->by_table('ip_provider_custom')->get()->result();
        $custom_values = [];
        foreach ($custom_fields as $custom_field) {
            if (in_array($custom_field->custom_field_type, $this->mdl_custom_values->custom_value_fields())) {
                $values = $this->mdl_custom_values->get_by_fid($custom_field->custom_field_id)->result();
                $custom_values[$custom_field->custom_field_id] = $values;
            }
        }

        $fields = $this->mdl_provider_custom->get_by_clid($id);

        foreach ($custom_fields as $cfield) {
            foreach ($fields as $fvalue) {
                if ($fvalue->provider_custom_fieldid == $cfield->custom_field_id) {
                    // TODO: Hackish, may need a better optimization
                    $this->mdl_providers->set_form_value(
                        'custom[' . $cfield->custom_field_id . ']',
                        $fvalue->provider_custom_fieldvalue
                    );
                    break;
                }
            }
        }

        $this->load->helper('country');
        $this->load->helper('custom_values');

        $this->layout->set(
            array(
                'custom_fields' => $custom_fields,
                'custom_values' => $custom_values,
                'countries' => get_country_list(trans('cldr')),
                'selected_country' => $this->mdl_providers->form_value('provider_country') ?: get_setting('default_country'),
                'languages' => get_available_languages(),
            )
        );

        $this->layout->buffer('content', 'providers/form');
        $this->layout->render();
    }

    /**
     * @param int $provider_id
     */
    public function view($provider_id)
    {
        $this->load->model('providers/mdl_provider_notes');
        $this->load->model('incoms/mdl_incoms');
        $this->load->model('custom_fields/mdl_custom_fields');
        $this->load->model('custom_fields/mdl_provider_custom');

        $provider = $this->mdl_providers
            ->where('ip_providers.provider_id', $provider_id)
            ->get()->row();

        $custom_fields = $this->mdl_provider_custom->get_by_provider($provider_id)->result();

        $this->mdl_provider_custom->prep_form($provider_id);

        if (!$provider) {
            show_404();
        }

        $this->layout->set(
            array(
                'provider' => $provider,
                'provider_notes' => $this->mdl_provider_notes->where('provider_id', $provider_id)->get()->result(),
                'incoms' => $this->mdl_incoms->by_provider($provider_id)->limit(20)->get()->result(),
                'custom_fields' => $custom_fields,
                'incom_statuses' => $this->mdl_incoms->statuses()
            )
        );

        $this->layout->buffer(
            array(
                array(
                    'incom_table',
                    'incoms/partial_incom_table'
                ),
                array(
                    'partial_notes',
                    'providers/partial_notes'
                ),
                array(
                    'content',
                    'providers/view'
                )
            )
        );

        $this->layout->render();
    }

    /**
     * @param int $provider_id
     */
    public function delete($provider_id)
    {
        $this->mdl_providers->delete($provider_id);
        redirect('providers');
    }

}
