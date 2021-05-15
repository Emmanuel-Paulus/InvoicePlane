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

    public function modal_picture_lookups()
    {
        $filter_picture = $this->input->get('filter_picture');
        $reset_table = $this->input->get('reset_table');

        $this->load->model('mdl_pictures');

        $pictures = $this->mdl_products->get()->result();

        $data = array(
            'pictures' => $pictures,
            'filter_picture' => $filter_picture,
        );

        if ($filter_picture || $reset_table) {
            $this->layout->load_view('pictures/partial_picture_table_modal', $data);
        } else {
            $this->layout->load_view('pictures/modal_picture_lookups', $data);
        }
    }

    public function process_picture_selections()
    {
        $this->load->model('mdl_pictures');

        $pictures = $this->mdl_pictures->where_in('picture_id', $this->input->post('picture_ids'))->get()->result();

        echo json_encode($pictures);
    }

}
