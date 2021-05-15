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
 * Class Pictures
 */
class Pictures extends Admin_Controller
{
    /**
     * Pictures constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('mdl_pictures');
    }

    /**
     * @param int $page
     */
    public function index($page = 0)
    {
        $this->mdl_pictures->paginate(site_url('pictures/index'), $page);
        $pictures = $this->mdl_pictures->result();

        $this->layout->set('pictures', $pictures);
        $this->layout->buffer('content', 'pictures/index');
        $this->layout->render();
    }

    /**
     * @param null $id
     */
    public function form($id = null)
    {
        if ($this->input->post('btn_cancel')) {
            redirect('pictures');
        }
        if ($this->mdl_pictures->run_picture_validation()) {
            // Get the db array
            $db_array = $this->mdl_pictures->db_array();
            $this->mdl_pictures->save($id, $db_array);
            redirect('pictures');
        }

        if ($id and !$this->input->post('btn_submit')) {
            if (!$this->mdl_pictures->prep_form($id)) {
                show_404();
            }
        }

        $this->layout->buffer('content', 'pictures/form');
        $this->layout->render();
    }

    /**
     * @param $id
     */
    public function delete($id)
    {
        $this->mdl_pictures->delete($id);
        redirect('pictures');
    }

}
