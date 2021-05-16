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

}
