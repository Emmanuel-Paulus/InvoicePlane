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
 * Class Mdl_Products
 */
class Mdl_Product_Materials extends Response_Model
{
    public $table = 'ip_product_materials';
    public $primary_key = 'ip_product_materials.product_material_id';

    public function default_select()
    {
        $this->db->select('SQL_CALC_FOUND_ROWS *', false);
    }

    public function default_order_by()
    {
        $this->db->order_by('ip_materials.product_material_id');
    }

    public function default_join()
    {
        $this->db->join('ip_materials', 'ip_materials.material_id = ip_materials.material_id');
        $this->db->join('ip_products', 'ip_products.product_id = ip_products.product_id');
    }

    public function by_material($match)
    {
        $this->db->group_start();
        $this->db->like('ip_materials.material_name', $match);
        $this->db->or_like('ip_materials.material_description', $match);
        $this->db->group_end();
    }

    public function by_product($match)
    {
        $this->db->join('ip_product_materials', 'ip_product_materials.material_id = ip_materials.material_id');
        $this->db->join('ip_products', 'ip_products.product_id = ip_product_materials.product_id');
        $this->db->where('ip_products.product_name', $match);
    }

    public function get_by_product($product_id)
    {
        $this->db->join('ip_product_materials', 'ip_product_materials.material_id = ip_materials.material_id');
        $this->db->join('ip_products', 'ip_products.product_id = ip_product_materials.product_id');
        return $this->where('ip_products.product_id', $product_id)->get();
    }

    /**
     * @return array
     */
    public function validation_rules()
    {
        return array(
            'product_id' => array(
                'field' => 'product_id',
                'label' => trans('product'),
                'rules' => 'required'
            ),            
            'material_id' => array(
                'field' => 'material_id',
                'label' => trans('material'),
                'rules' => 'required'
            ),
            'prod_matr_amount' => array(
                'field' => 'prod_matr_amount',
                'label' => trans('prod_matr_amount'),
                'rules' => 'required'
            ),
        );
    }

    /**
     * @return array
     */
    public function db_array()
    {
        $db_array = parent::db_array();
        $db_array['prod_matr_price'] = (empty($db_array['prod_matr_price']) ? null : standardize_amount($db_array['prod_matr_price']));
        return $db_array;
    }
}
