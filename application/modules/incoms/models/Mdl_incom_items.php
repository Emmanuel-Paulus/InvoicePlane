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
 * Class Mdl_Incom_Items
 */
class Mdl_Incom_Items extends Response_Model
{

    public $table = 'ip_incom_items';

    public $primary_key = 'ip_incom_items.item_id';

    public $date_created_field = 'item_date_added';

    public function default_select()
    {
        $this->db->select('ip_incom_item_amounts.*, ip_incom_items.*, item_tax_rates.tax_rate_percent AS item_tax_rate_percent');
    }

    public function default_order_by()
    {
        $this->db->order_by('ip_incom_items.item_order');
    }

    public function default_join()
    {
        $this->db->join('ip_incom_item_amounts', 'ip_incom_item_amounts.item_id = ip_incom_items.item_id', 'left');
        $this->db->join('ip_tax_rates AS item_tax_rates', 'item_tax_rates.tax_rate_id = ip_incom_items.item_tax_rate_id', 'left');
    }

    /**
     * @return array
     */
    public function validation_rules()
    {
        return [
            'incom_id' => [
                'field' => 'incom_id',
                'label' => trans('incom'),
                'rules' => 'required',
            ],
            'item_name' => [
                'field' => 'item_name',
                'label' => trans('item_name'),
                'rules' => 'required',
            ],
            'item_description' => [
                'field' => 'item_description',
                'label' => trans('description'),
            ],
            'item_quantity' => [
                'field' => 'item_quantity',
                'label' => trans('quantity'),
            ],
            'item_price' => [
                'field' => 'item_price',
                'label' => trans('price'),
            ],
            'item_tax_rate_id' => [
                'field' => 'item_tax_rate_id',
                'label' => trans('item_tax_rate'),
            ],
            'item_picture_id' => array(
                'field' => 'item_picture_id',
                'label' => trans('picture'),
                'rules' => 'numeric'
            ),            
            'item_product_id' => [
                'field' => 'item_product_id',
                'label' => trans('original_product'),
            ],
        ];
    }

    /**
     * @param null $id
     * @param null $db_array
     *
     * @return int|null
     */
    public function save($id = null, $db_array = null)
    {
        $id = parent::save($id, $db_array);

        $this->load->model('incoms/mdl_incom_item_amounts');
        $this->mdl_incom_item_amounts->calculate($id);

        $this->load->model('incoms/mdl_incom_amounts');

        if (is_object($db_array) && isset($db_array->incom_id)) {
            $this->mdl_incom_amounts->calculate($db_array->incom_id);
        } elseif (is_array($db_array) && isset($db_array['incom_id'])) {
            $this->mdl_incom_amounts->calculate($db_array['incom_id']);
        }

        return $id;
    }

    /**
     * @param int $item_id
     *
     * @return bool
     */
    public function delete($item_id)
    {
        // Get item:
        // the incom id is needed to recalculate incom amounts
        $query = $this->db->get_where($this->table, ['item_id' => $item_id]);

        if ($query->num_rows() == 0) {
            return false;
        }

        $row = $query->row();
        $incom_id = $row->incom_id;

        // Delete the item itself
        parent::delete($item_id);

        // Delete the item amounts
        $this->db->where('item_id', $item_id);
        $this->db->delete('ip_incom_item_amounts');

        // Recalculate incom amounts
        $this->load->model('incoms/mdl_incom_amounts');
        $this->mdl_incom_amounts->calculate($incom_id);

        return true;
    }

}
