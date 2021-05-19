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
        $this->db->from('ip_materials');
        $this->db->join('ip_product_materials', 'ip_product_materials.material_id = ip_materials.material_id');
        $this->db->join('ip_products', 'ip_products.product_id = ip_product_materials.product_id');
        $this->db->join('ip_pictures', 'ip_materials.picture_id = ip_pictures.picture_id', 'left');
        $this->db->where('ip_products.product_name', $match);
    }

    public function by_product_id($id)
    {
        $this->db->from('ip_materials');
        $this->db->join('ip_product_materials', 'ip_product_materials.material_id = ip_materials.material_id');
        $this->db->join('ip_products', 'ip_products.product_id = ip_product_materials.product_id');
        $this->db->join('ip_pictures', 'ip_materials.picture_id = ip_pictures.picture_id', 'left');
        $this->db->where('ip_products.product_id', $id);
    }

    public function get_by_product($product_id)
    {
        $this->db->from('ip_materials');
        $this->db->join('ip_product_materials', 'ip_product_materials.material_id = ip_materials.material_id');
        $this->db->join('ip_products', 'ip_products.product_id = ip_product_materials.product_id');
        $this->db->join('ip_pictures', 'ip_materials.picture_id = ip_pictures.picture_id', 'left');
        $this->db->where('ip_products.product_id', $product_id);
        return $this->db->get();
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
            'material_price_amount' => array(
                'field' => 'material_price_amount',
                'label' => trans('material_price'),
                'rules' => ''
            ),
            'material_price_descr' => array(
                'field' => 'material_price',
                'label' => trans('material_price_descr'),
                'rules' => ''
            ),
            'material_provider_name' => array(
                'field' => 'material_provider_name',
                'label' => trans('material_provider_name'),
                'rules' => ''
            ),
            'material_url' => array(
                'field' => 'material_url',
                'label' => trans('material_url'),
                'rules' => ''
            ),
            'picture_id' => array(
                'field' => 'picture_id',
                'label' => trans('picture'),
                'rules' => ''
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
        return $db_array;
    }
    
    public function by_invoice($id = null)
    {
        $this->db->from('ip_materials');
        $this->db->join('ip_product_materials', 'ip_product_materials.material_id = ip_materials.material_id');
        $this->db->join('ip_products', 'ip_products.product_id = ip_product_materials.product_id');
        $this->db->join('ip_invoice_items', 'ip_invoice_items.item_product_id = ip_products.product_id');
        $this->db->join('ip_pictures', 'ip_materials.picture_id = ip_pictures.picture_id', 'left');
        $this->db->where('ip_invoice_items.invoice_id', $id);
    }

    public function by_quote($id = null)
    {
        $this->db->from('ip_materials');
        $this->db->join('ip_product_materials', 'ip_product_materials.material_id = ip_materials.material_id');
        $this->db->join('ip_products', 'ip_products.product_id = ip_product_materials.product_id');
        $this->db->join('ip_quote_items', 'ip_quote_items.item_product_id = ip_products.product_id');
        $this->db->join('ip_pictures', 'ip_materials.picture_id = ip_pictures.picture_id', 'left');
        $this->db->where('ip_quote_items.quote_id', $id);
    }

}
