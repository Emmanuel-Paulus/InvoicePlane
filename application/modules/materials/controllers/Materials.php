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
        $this->load->model('pictures/mdl_pictures');
        $this->mdl_materials->paginate(site_url('materials/index'), $page);
        $materials = $this->mdl_materials->result();

        $this->layout->set('materials', $materials);
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

        $this->load->model('pictures/mdl_pictures');

        $this->layout->set(
            array(
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
