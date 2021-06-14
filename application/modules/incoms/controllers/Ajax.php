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
 * Class Ajax
 */
class Ajax extends Admin_Controller
{

    public $ajax_controller = true;

    public function save()
    {
        $this->load->model('incoms/mdl_incom_items');
        $this->load->model('incoms/mdl_incoms');
        $this->load->model('units/mdl_units');

        $incom_id = $this->input->post('incom_id');

        $this->mdl_incoms->set_id($incom_id);

        if ($this->mdl_incoms->run_validation('validation_rules_save_incom')) {
            $items = json_decode($this->input->post('items'));

            foreach ($items as $item) {
                if ($item->item_name) {
                    $item->item_quantity = ($item->item_quantity ? standardize_amount($item->item_quantity) : floatval(0));
                    $item->item_price = ($item->item_price ? standardize_amount($item->item_price) : floatval(0));
                    $item->item_discount_amount = ($item->item_discount_amount) ? standardize_amount($item->item_discount_amount) : null;
                    $item->item_product_id = ($item->item_product_id ? $item->item_product_id : null);
                    $item->item_material_id = ($item->item_material_id ? $item->item_material_id : null);
                    $item->item_product_unit_id = ($item->item_product_unit_id ? $item->item_product_unit_id : null);
                    $item->item_product_unit = $this->mdl_units->get_name($item->item_product_unit_id, $item->item_quantity);

                    $item_id = ($item->item_id) ?: null;
                    unset($item->item_id);

                    $this->mdl_incom_items->save($item_id, $item);
                }
            }

            if ($this->input->post('incom_discount_amount') === '') {
                $incom_discount_amount = floatval(0);
            } else {
                $incom_discount_amount = $this->input->post('incom_discount_amount');
            }

            if ($this->input->post('incom_discount_percent') === '') {
                $incom_discount_percent = floatval(0);
            } else {
                $incom_discount_percent = $this->input->post('incom_discount_percent');
            }

            // Generate new incom number if needed
            $incom_number = $this->input->post('incom_number');
            $incom_status_id = $this->input->post('incom_status_id');

            $db_array = [
                'incom_number' => $incom_number,
                'incom_date_created' => date_to_mysql($this->input->post('incom_date_created')),
                'incom_date_expires' => date_to_mysql($this->input->post('incom_date_expires')),
                'incom_status_id' => $incom_status_id,
                'notes' => $this->input->post('notes'),
                'incom_discount_amount' => standardize_amount($incom_discount_amount),
                'incom_discount_percent' => standardize_amount($incom_discount_percent),
            ];

            $this->mdl_incoms->save($incom_id, $db_array);

            // Recalculate for discounts
            $this->load->model('incoms/mdl_incom_amounts');
            $this->mdl_incom_amounts->calculate($incom_id);

            $response = [
                'success' => 1,
            ];
        } else {
            $this->load->helper('json_error');
            $response = [
                'success' => 0,
                'validation_errors' => json_errors(),
            ];
        }


        // Save all custom fields
        if ($this->input->post('custom')) {
            $db_array = [];

            $values = [];
            foreach ($this->input->post('custom') as $custom) {
                if (preg_match("/^(.*)\[\]$/i", $custom['name'], $matches)) {
                    $values[$matches[1]][] = $custom['value'];
                } else {
                    $values[$custom['name']] = $custom['value'];
                }
            }

            foreach ($values as $key => $value) {
                preg_match("/^custom\[(.*?)\](?:\[\]|)$/", $key, $matches);
                if ($matches) {
                    $db_array[$matches[1]] = $value;
                }
            }
            $this->load->model('custom_fields/mdl_incom_custom');
            $result = $this->mdl_incom_custom->save_custom($incom_id, $db_array);
            if ($result !== true) {
                $response = [
                    'success' => 0,
                    'validation_errors' => $result,
                ];

                echo json_encode($response);
                exit;
            }
        }

        echo json_encode($response);
    }

    public function save_incom_tax_rate()
    {
        $this->load->model('incoms/mdl_incom_tax_rates');

        if ($this->mdl_incom_tax_rates->run_validation()) {
            $this->mdl_incom_tax_rates->save();

            $response = [
                'success' => 1,
            ];
        } else {
            $response = [
                'success' => 0,
                'validation_errors' => $this->mdl_incom_tax_rates->validation_errors,
            ];
        }

        echo json_encode($response);
    }

    public function create()
    {
        $this->load->model('incoms/mdl_incoms');

        if ($this->mdl_incoms->run_validation()) {
            $incom_id = $this->mdl_incoms->create();

            $response = [
                'success' => 1,
                'incom_id' => $incom_id,
            ];
        } else {
            $this->load->helper('json_error');
            $response = [
                'success' => 0,
                'validation_errors' => json_errors(),
            ];
        }

        echo json_encode($response);
    }

    public function modal_change_provider()
    {
        $this->load->module('layout');
        $this->load->model('providers/mdl_providers');

        $data = [
            'provider_id' => $this->input->post('provider_id'),
            'incom_id' => $this->input->post('incom_id'),
            'providers' => $this->mdl_providers->get_latest(),
        ];

        $this->layout->load_view('incoms/modal_change_provider', $data);
    }

    public function change_provider()
    {
        $this->load->model('incoms/mdl_incoms');
        $this->load->model('providers/mdl_providers');

        // Get the provider ID
        $provider_id = $this->input->post('provider_id');
        $provider = $this->mdl_providers->where('ip_providers.provider_id', $provider_id)
            ->get()->row();

        if (!empty($provider)) {
            $incom_id = $this->input->post('incom_id');

            $db_array = [
                'provider_id' => $provider_id,
            ];
            $this->db->where('incom_id', $incom_id);
            $this->db->update('ip_incoms', $db_array);

            $response = [
                'success' => 1,
                'incom_id' => $incom_id,
            ];
        } else {
            $this->load->helper('json_error');
            $response = [
                'success' => 0,
                'validation_errors' => json_errors(),
            ];
        }

        echo json_encode($response);
    }

    public function get_item()
    {
        $this->load->model('incoms/mdl_incom_items');

        $item = $this->mdl_incom_items->get_by_id($this->input->post('item_id'));

        echo json_encode($item);
    }

    public function modal_create_incom()
    {
        $this->load->module('layout');
        $this->load->model('tax_rates/mdl_tax_rates');
        $this->load->model('providers/mdl_providers');

        $data = [
            'tax_rates' => $this->mdl_tax_rates->get()->result(),
            'provider' => $this->mdl_providers->get_by_id($this->input->post('provider_id')),
            'providers' => $this->mdl_providers->get_latest(),
        ];

        $this->layout->load_view('incoms/modal_create_incom', $data);
    }

    public function modal_copy_incom()
    {
        $this->load->module('layout');

        $this->load->model('incoms/mdl_incoms');
        $this->load->model('tax_rates/mdl_tax_rates');
        $this->load->model('providers/mdl_providers');

        $data = [
            'tax_rates' => $this->mdl_tax_rates->get()->result(),
            'incom_id' => $this->input->post('incom_id'),
            'incom' => $this->mdl_incoms->where('ip_incoms.incom_id', $this->input->post('incom_id'))->get()->row(),
            'provider' => $this->mdl_providers->get_by_id($this->input->post('provider_id')),
        ];

        $this->layout->load_view('incoms/modal_copy_incom', $data);
    }

    public function copy_incom()
    {
        $this->load->model('incoms/mdl_incoms');
        $this->load->model('incoms/mdl_incom_items');
        $this->load->model('incoms/mdl_incom_tax_rates');

        if ($this->mdl_incoms->run_validation()) {
            $target_id = $this->mdl_incoms->save();
            $source_id = $this->input->post('incom_id');

            $this->mdl_incoms->copy_incom($source_id, $target_id);

            $response = [
                'success' => 1,
                'incom_id' => $target_id,
            ];
        } else {
            $this->load->helper('json_error');
            $response = [
                'success' => 0,
                'validation_errors' => json_errors(),
            ];
        }

        echo json_encode($response);
    }

    /**
     * @param $incom_id
     */
    public function delete_item($incom_id)
    {
        $success = 0;
        $item_id = $this->input->post('item_id');
        $this->load->model('mdl_incoms');

        // Only continue if the incom exists or no item id was provided
        if ($this->mdl_incoms->get_by_id($incom_id) || empty($item_id)) {

            // Delete incom item
            $this->load->model('mdl_incom_items');
            $item = $this->mdl_incom_items->delete($item_id);

            // Check if deletion was successful
            if ($item) {
                $success = 1;
            }

        }

        // Return the response
        echo json_encode([
            'success' => $success,
        ]);
    }

}
