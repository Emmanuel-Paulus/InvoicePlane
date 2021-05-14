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

    public function modal_material_lookups()
    {
        $filter_material = $this->input->get('filter_material');
        $filter_product = $this->input->get('filter_product');
        $reset_table = $this->input->get('reset_table');

        $this->load->model('mdl_materials');
        $this->load->model('products/mdl_products');

        if (!empty($filter_product)) {
            $this->mdl_materials->by_product($filter_product);
        }
        $materials = $this->mdl_products->get()->result();
        $products = $this->mdl_families->get()->result();

        $data = array(
            'materials' => $materials,
            'products' => $products,
            'filter_material' => $filter_material,
            'filter_product' => $filter_product,
        );

        if ($filter_material || $filter_product || $reset_table) {
            $this->layout->load_view('materials/partial_material_table_modal', $data);
        } else {
            $this->layout->load_view('materials/modal_material_lookups', $data);
        }
    }

    public function process_material_selections()
    {
        $this->load->model('mdl_materials');

        $materials = $this->mdl_materials->where_in('material_id', $this->input->post('material_ids'))->get()->result();

        foreach ($materials as $material) {
            $material->material_price = format_amount($material->material_price);
        }

        echo json_encode($materials);
    }

}
