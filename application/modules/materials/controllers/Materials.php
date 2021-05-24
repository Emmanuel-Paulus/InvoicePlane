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
 * Class Materials
 */
class Materials extends Admin_Controller
{
    /**
     * Materials constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('mdl_materials');
    }

    /**
     * @param int $page
     */
    public function index($page = 0)
    {
        $filter_material = $this->input->get('filter_material');
        $filter_family = $this->input->get('filter_family');
        $order = $this->input->get('order');
        $reset_table = $this->input->get('reset_table');

        $this->load->model('mdl_materials');
        $this->load->model('pictures/mdl_pictures');
        $this->load->model('families/mdl_families');
        
        if (!empty($filter_family) && empty($reset_table)) {
            $this->mdl_materials->filter_where('ip_materials.family_id', $filter_family);
        }

        if (!empty($filter_material) && empty($reset_table)) {
            $this->mdl_materials->filter_group_start();
            $this->mdl_materials->filter_like('ip_materials.material_name', $filter_material);
            $this->mdl_materials->filter_or_like('ip_materials.material_description', $filter_material);
            $this->mdl_materials->filter_group_end();
        }

        if (empty($order)) {
            $this->mdl_materials->filter_order_by('ip_materials.material_name, ip_families.family_name');
        } else if ("family_name" == $order) {
            $this->mdl_materials->filter_order_by('ip_families.family_name, ip_materials.material_name');
        } else {
            $this->mdl_materials->filter_order_by("ip_materials.".$order);
        }

        $families = $this->mdl_families->get()->result();
        $this->mdl_materials->paginate(site_url('materials/index'), $page);
        $materials = $this->mdl_materials->result();

        $data = array(
            'materials' => $materials,
            'families' => $families,
            'filter_family' => $filter_family,
            'filter_material' => $filter_material,
            'order' => $order
        );
        $this->layout->set($data);
        $this->layout->buffer('content', 'materials/index');
        $this->layout->render();
    }

    /**
     * @param null $id
     */
    public function form($id = null)
    {
        if ($this->input->post('btn_cancel')) {
            redirect('materials');
        }

        if ($this->mdl_materials->run_validation()) {
            // Get the db array
            $db_array = $this->mdl_materials->db_array();
            $this->mdl_materials->save($id, $db_array);
            redirect('materials');
        }

        if ($id and !$this->input->post('btn_submit')) {
            if (!$this->mdl_materials->prep_form($id)) {
                show_404();
            }
        }

        $this->load->model('families/mdl_families');
        $this->load->model('pictures/mdl_pictures');

        $this->layout->set(
            array(
                'families' => $this->mdl_families->get()->result(),
            )
        );

        $this->layout->buffer('content', 'materials/form');
        $this->layout->render();
    }

    /**
     * @param $id
     */
    public function delete($id)
    {
        $this->mdl_materials->delete($id);
        redirect('materials');
    }


    public function product_pdf($id = null)
    {
        $this->load->model('mdl_materials');
        $this->mdl_materials->by_product_id($id);
        $this->show_material_pdf();
    }
   
    public function invoice_pdf($id = null)
    {
        $this->load->model('mdl_materials');
        $this->mdl_materials->by_invoice($id);
        $this->show_material_pdf();
    }
    
    public function quote_pdf($id = null)
    {
        $this->load->model('mdl_materials');
        $this->mdl_materials->by_quote($id);
        $this->show_material_pdf();
    }
    
    /**
     * @param null $materials
     */
    private function show_material_pdf()
    {
        $this->load->model('pictures/mdl_pictures');
        $materials = $this->db->get()->result();
        $data = ['materials' => $materials];
        $this->load->helper('mpdf');
        $html = $this->load->view('reports/materiallist', $data, true);
        pdf_create($html, trans('materiallist'), true, null, null, null, false, null, ['mode' => 'utf-8', 'format' => 'A4-L']);
    }

    public function product_csv($id = null)
    {
        $this->load->model('mdl_materials');
        $this->mdl_materials->by_product_id($id);
        $this->show_material_csv('product_'.$id);
    }
   
    public function invoice_csv($id = null)
    {
        $this->load->model('mdl_materials');
        $this->mdl_materials->by_invoice($id);
        $this->show_material_csv('invoice_'.$id);
    }
    
    public function quote_csv($id = null)
    {
        $this->load->model('mdl_materials');
        $this->mdl_materials->by_quote($id);
        $this->show_material_csv('quote_'.$id);
    }
    
    /**
     * @param null $materials
     */
    private function show_material_csv($name_file)
    {
        $this->load->model('pictures/mdl_pictures');
        $materials = $this->db->get()->result();
        $data = $materials;
        $this->load->helper('csv');
        $data = [[
            trans('material_name'),
            trans('material_description'),
            trans('prod_matr_price'),
            trans('prod_matr_amount'),
            trans('material_price_descr'),
            trans('material_price'),
            trans('material_price_amount'),
            trans('material_provider_name'),
            trans('material_url')
            ]];
        foreach($materials as $material) {
            $data[] = [
                $material->material_name,
                $material->material_description,
                round($material->prod_matr_amount * $material->material_price / $material->material_price_amount, 2),
                $material->prod_matr_amount,
                $material->material_price_descr,
                $material->material_price,
                $material->material_price_amount,
                $material->material_provider_name,
                $material->material_url
                ];
        }
        csv_create($data, 'materiallist_'.$name_file.'.csv',false);
        die();
    }
}
