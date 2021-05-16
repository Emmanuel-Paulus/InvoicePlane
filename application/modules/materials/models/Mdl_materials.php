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
class Mdl_Materials extends Response_Model
{
    public $table = 'ip_materials';
    public $primary_key = 'ip_materials.material_id';

    public function default_select()
    {
        $this->db->select('SQL_CALC_FOUND_ROWS *', false);
    }

    public function default_order_by()
    {
        $this->db->order_by('ip_materials.material_name');
    }

    public function default_join()
    {
        $this->db->join('ip_pictures', 'ip_pictures.picture_id = ip_materials.picture_id', 'left');
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
        $this->db->where('ip_products.family_id', $match);
    }

    public function get_by_product($product_id)
    {
        return $this->where('ip_materials.product_id', $product_id)->get();
    }

    /**
     * @return array
     */
    public function validation_rules()
    {
        return array(
            'material_name' => array(
                'field' => 'material_name',
                'label' => trans('material_name'),
                'rules' => 'required'
            ),
            'material_description' => array(
                'field' => 'material_description',
                'label' => trans('material_description'),
                'rules' => ''
            ),
            'material_price' => array(
                'field' => 'material_price',
                'label' => trans('material_price'),
                'rules' => ''
            ),
            'material_provider_name' => array(
                'field' => 'material_provider_name',
                'label' => trans('material_provider_name'),
                'rules' => ''
            ),
            'product_id' => array(
                'field' => 'product_id',
                'label' => trans('product'),
                'rules' => 'numeric'
            ),
            'picture_id' => array(
                'field' => 'picture_id',
                'label' => trans('picture'),
                'rules' => 'numeric'
            ),            
        );
    }

    /**
     * @return array
     */
    public function db_array()
    {
        $db_array = parent::db_array();

        $db_array['material_price'] = (empty($db_array['material_price']) ? null : standardize_amount($db_array['material_price']));
        $db_array['product_id'] = (empty($db_array['product_id']) ? null : $db_array['product_id']);
        return $db_array;
    }
}
