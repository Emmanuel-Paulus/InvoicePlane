<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * InvoicePlane
 *
 * @author		InvoicePlane Developers & Contributors
 * @copyright	Copyright (c) 2012 - 2018 InvoicePlane.com
 * @license		https://invoiceplane.com/license.txt
 * @link		https://invoiceplane.com
 *

/**
 * Class Ajax
 */
class Ajax extends Admin_Controller
{
    public $ajax_controller = true;

    public function modal_material_lookups()
    {
        $filter_material = $this->input->get('filter_material');
        $filter_family = $this->input->get('filter_family');
        $reset_table = $this->input->get('reset_table');

        $this->load->model('mdl_materials');
        $this->load->model('families/mdl_families');

        if (!empty($filter_family)) {
            $this->mdl_materials->by_family($filter_family);
        }

        if (!empty($filter_material)) {
            $this->mdl_materials->by_material($filter_material);
        }

        $materials = $this->mdl_materials->get()->result();
        $families = $this->mdl_families->get()->result();

        $data = array(
            'materials' => $materials,
            'families' => $families,
            'filter_family' => $filter_family,
            'filter_material' => $filter_material,
        );
        if ($filter_material || $filter_family || $reset_table) {
            $this->layout->load_view('materials/partial_material_table_modal', $data);
        } else {
            $this->layout->load_view('materials/modal_material_lookups', $data);
        }
    }

    public function modal_material_lookups_direct()
    {
        $filter_material = $this->input->get('filter_material');
        $filter_family = $this->input->get('filter_family');
        $reset_table = $this->input->get('reset_table');

        $this->load->model('mdl_materials');
        $this->load->model('families/mdl_families');

        if (!empty($filter_family)) {
            $this->mdl_materials->by_family($filter_family);
        }

        if (!empty($filter_material)) {
            $this->mdl_materials->by_material($filter_material);
        }

        $materials = $this->mdl_materials->get()->result();
        $families = $this->mdl_families->get()->result();
        $default_item_tax_rate = get_setting('default_item_tax_rate');
        $default_item_tax_rate = $default_item_tax_rate !== '' ?: 0;
        $data = array(
            'materials' => $materials,
            'families' => $families,
            'filter_family' => $filter_family,
            'filter_material' => $filter_material,
            'default_item_tax_rate' => $default_item_tax_rate,
        );
        if ($filter_material || $filter_family || $reset_table) {
            $this->layout->load_view('materials/partial_material_table_modal', $data);
        } else {
            $this->layout->load_view('materials/modal_material_lookups_direct', $data);
        }
    }

    public function process_material_selections()
    {
        $this->load->model('mdl_materials');
        $materials = $this->mdl_materials->where_in('material_id', $this->input->post('material_ids'))->get()->result();

        foreach ($materials as $material) {
            $material->material_price_raw = $material->material_price;
            $material->material_price = format_amount($material->material_price);
        }

        echo json_encode($materials);
    }

    public function format_price()
    {
        $price = $this->input->post('price');
        $retjson = ["price" => ""];
        if ($price) {
            $retjson["price"] = format_amount($price);            
        }
        echo json_encode($retjson);
    }
    
    public function product($id = null)
    {
        $this->load->model('mdl_materials');
        $this->mdl_materials->by_product_id($id);
        $this->show_material();
    }
   
    public function invoice($id = null)
    {
        $this->load->model('mdl_materials');
        $this->mdl_materials->by_invoice($id);
        $this->show_material();
    }
    
    public function quote($id = null)
    {
        $this->load->model('mdl_materials');
        $this->mdl_materials->by_quote($id);
        $this->show_material();
    }
    
    private function show_material()
    {
        $this->load->model('pictures/mdl_pictures');
        $materials = $this->db->get()->result();
        $data = ['materials' => $materials];
        $this->layout->load_view('materials/modal_materiallist', $data);            
    }
}
