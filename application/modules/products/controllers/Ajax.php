<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

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
class Ajax extends Admin_Controller {

    public $ajax_controller = true;

    public function modal_product_lookups() {
        $filter_product = $this->input->get('filter_product');
        $filter_family = $this->input->get('filter_family');
        $reset_table = $this->input->get('reset_table');

        $this->load->model('mdl_products');
        $this->load->model('families/mdl_families');

        if (!empty($filter_family)) {
            $this->mdl_products->by_family($filter_family);
        }

        if (!empty($filter_product)) {
            $this->mdl_products->by_product($filter_product);
        }

        $products = $this->mdl_products->get()->result();
        $families = $this->mdl_families->get()->result();

        $default_item_tax_rate = get_setting('default_item_tax_rate');
        $default_item_tax_rate = $default_item_tax_rate !== '' ?: 0;

        $data = array(
            'products' => $products,
            'families' => $families,
            'filter_product' => $filter_product,
            'filter_family' => $filter_family,
            'default_item_tax_rate' => $default_item_tax_rate,
        );

        if ($filter_product || $filter_family || $reset_table) {
            $this->layout->load_view('products/partial_product_table_modal', $data);
        } else {
            $this->layout->load_view('products/modal_product_lookups', $data);
        }
    }

    public function process_product_selections() {
        $this->load->model('mdl_products');

        $products = $this->mdl_products->where_in('product_id', $this->input->post('product_ids'))->get()->result();

        foreach ($products as $product) {
            $product->product_price = format_amount($product->product_price);
        }

        echo json_encode($products);
    }

    public function delete_material() {
        $product_material_id = $this->input->post('product_material_id');
        $this->load->model('mdl_product_materials');
        $this->mdl_product_materials->delete($product_material_id);
        echo json_encode([
            'success' => 1
        ]);
    }
    
    public function save() {
        $this->load->model('products/mdl_products');
        $this->load->model('materials/mdl_materials');
        $this->load->model('products/Mdl_product_materials');

        $product_id = $this->input->post('product_id');
        $this->mdl_products->set_id($product_id);

        if (!$this->mdl_products->run_validation()) {
            $this->load->helper('json_error');
            echo json_encode(['success' => 0,'validation_errors' => json_errors()]);
            exit;
        }

        $db_array = [
            'product_sku' => $this->input->post('product_sku'),
            'product_name' => $this->input->post('product_name'),
            'product_description' => $this->input->post('product_description'),
            'product_price' => standardize_amount($this->input->post('product_price')),
            'unit_id' => $this->input->post('unit_id'),
            'tax_rate_id' => $this->input->post('tax_rate_id'),
            'provider_name' => $this->input->post('provider_name'),
            'purchase_price' => standardize_amount($this->input->post('purchase_price')),
            'product_tariff' => $this->input->post('product_tariff'),
            'picture_id' => $this->input->post('picture_id'),
        ];

        $this->mdl_products->save($product_id, $db_array);

        // Save product_materials
        if ($this->input->post('product_materials')) {
            $materials = json_decode($this->input->post('product_materials'));
            foreach ($materials as $product_material) {
                if (!$product_material->material_id) {
                    continue;                    
                }
                $product_material_id = $product_material->product_material_id;
                $db_array = [
                    'product_id' => $this->input->post('product_id'),
                    'material_id' => $product_material->material_id,
                    'prod_matr_amount' => $product_material->prod_matr_amount,
                ];
                $this->Mdl_product_materials->save($product_material_id, $db_array);
            }
      }
  
      echo json_encode(['success' => 1]);
      exit;
  }
} 