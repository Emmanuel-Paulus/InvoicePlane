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
 * Class Products
 */
class Products extends Admin_Controller
{
    /**
     * Products constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('mdl_products');
    }

    /**
     * @param int $page
     */
    public function index($page = 0)
    {
        $filter_product = $this->input->get('filter_product');
        $filter_family = $this->input->get('filter_family');
        $order = $this->input->get('order');
        $reset_table = $this->input->get('reset_table');

        $this->load->model('mdl_products');
        $this->load->model('pictures/mdl_pictures');
        $this->load->model('families/mdl_families');
        
        if (!empty($filter_family) && empty($reset_table)) {
            $this->mdl_products->filter_where('ip_products.family_id', $filter_family);
        }

        if (!empty($filter_product) && empty($reset_table)) {
            $this->mdl_products->filter_group_start();
            $this->mdl_products->filter_like('ip_products.product_name', $filter_product);
            $this->mdl_products->filter_or_like('ip_products.product_description', $filter_product);
            $this->mdl_products->filter_group_end();
        }

        if (empty($order)) {
            $this->mdl_products->filter_order_by('ip_products.product_name, ip_families.family_name');
        } else if ("family_name" == $order) {
            $this->mdl_products->filter_order_by('ip_families.family_name, ip_products.product_name');
        } else {
            $this->mdl_products->filter_order_by("ip_products.".$order);
        }

        $families = $this->mdl_families->get()->result();

        $this->mdl_products->paginate(site_url('products/index'), $page);
        $products = $this->mdl_products->result();


        $data = array(
            'products' => $products,
            'families' => $families,
            'filter_family' => $filter_family,
            'filter_product' => $filter_product,
            'order' => $order
        );
        $this->layout->set($data);
        $this->layout->buffer('content', 'products/index');
        $this->layout->render();
    }

    /**
     * @param null $id
     */
    public function form($id = null)
    {
        if ($this->input->post('btn_cancel')) {
            redirect('products');
        }

        if ($this->mdl_products->run_validation()) {
            // Get the db array
            $db_array = $this->mdl_products->db_array();
            $this->mdl_products->save($id, $db_array);
            redirect('products');
        }

        if ($id and !$this->input->post('btn_submit')) {
            if (!$this->mdl_products->prep_form($id)) {
                show_404();
            }
        }

        $this->load->model('families/mdl_families');
        $this->load->model('units/mdl_units');
        $this->load->model('tax_rates/mdl_tax_rates');
        $this->load->model('products/Mdl_product_materials');
        $this->load->model('materials/mdl_materials');
        $this->load->model('pictures/mdl_pictures');
        
        $this->layout->set(
            array(
                'families' => $this->mdl_families->get()->result(),
                'units' => $this->mdl_units->get()->result(),
                'tax_rates' => $this->mdl_tax_rates->get()->result(),
                'materials' => $this->mdl_materials->get_by_product($id)->result(),
                'product' => $this->mdl_products->get($id)->result()[0],
            )
        );

        $this->layout->buffer('content', 'products/form');
        $this->layout->render();
    }

    /**
     * @param $id
     */
    public function delete($id)
    {
        $this->mdl_products->delete($id);
        redirect('products');
    }

}
