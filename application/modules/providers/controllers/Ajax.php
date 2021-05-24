<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * InvoicePlane
 *
 * @author      InvoicePlane Developers & Contributors
 * @copyright   Copyright (c) 2012 - 2018 InvoicePlane.com
 * @license     https://invoiceplane.com/license.txt
 * @link        https://invoiceplane.com
 */

/**
 * Class Ajax
 */
class Ajax extends Admin_Controller
{

    public $ajax_controller = true;

    public function name_query()
    {
        // Load the model & helper
        $this->load->model('providers/mdl_providers');

        $response = [];

        // Get the post input
        $query = $this->input->get('query');
        $permissiveSearchProviders = $this->input->get('permissive_search_providers');

        if (empty($query)) {
            echo json_encode($response);
            exit;
        }

        // Search for chars "in the middle" of providers names
        $permissiveSearchProviders ? $moreProvidersQuery = '%' : $moreProvidersQuery = '';

        // Search for providers
        $escapedQuery = $this->db->escape_str($query);
        $escapedQuery = str_replace("%", "", $escapedQuery);
        $providers = $this->mdl_providers
            ->where('provider_active', 1)
            ->having('provider_name LIKE \'' . $moreProvidersQuery . $escapedQuery . '%\'')
            ->or_having('provider_surname LIKE \'' . $moreProvidersQuery . $escapedQuery . '%\'')
            ->or_having('provider_fullname LIKE \'' . $moreProvidersQuery . $escapedQuery . '%\'')
            ->order_by('provider_name')
            ->get()
            ->result();

        foreach ($providers as $provider) {
            $response[] = [
                'id' => $provider->provider_id,
                'text' => htmlsc(format_provider($provider)),
            ];
        }

        // Return the results
        echo json_encode($response);
    }

    /**
     * Get the latest providers
     */
    public function get_latest()
    {
        // Load the model & helper
        $this->load->model('providers/mdl_providers');

        $response = [];

        $providers = $this->mdl_providers
            ->where('provider_active', 1)
            ->limit(5)
            ->order_by('provider_date_created')
            ->get()
            ->result();

        foreach ($providers as $provider) {
            $response[] = [
                'id' => $provider->provider_id,
                'text' => htmlsc(format_provider($provider)),
            ];
        }

        // Return the results
        echo json_encode($response);
    }

    public function save_preference_permissive_search_providers()
    {
        $this->load->model('mdl_settings');
        $permissiveSearchProviders = $this->input->get('permissive_search_Providers');

        if (!preg_match('!^[0-1]{1}$!', $permissiveSearchProviders)) {
            exit;
        }

        $this->mdl_settings->save('enable_permissive_search_providers', $permissiveSearchProviders);
    }

    public function save_provider_note()
    {
        $this->load->model('providers/mdl_provider_notes');

        if ($this->mdl_provider_notes->run_validation()) {
            $this->mdl_provider_notes->save();

            $response = [
                'success' => 1,
                'new_token' => $this->security->get_csrf_hash(),
            ];
        } else {
            $this->load->helper('json_error');
            $response = [
                'success' => 0,
                'new_token' => $this->security->get_csrf_hash(),
                'validation_errors' => json_errors(),
            ];
        }

        echo json_encode($response);
    }

    public function load_provider_notes()
    {
        $this->load->model('providers/mdl_provider_notes');
        $data = [
            'provider_notes' => $this->mdl_provider_notes->where('provider_id',
                $this->input->post('provider_id'))->get()->result(),
        ];

        $this->layout->load_view('providers/partial_notes', $data);
    }

}
