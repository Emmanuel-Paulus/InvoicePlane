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
 * Class Mdl_Incoms
 */
class Mdl_Incoms extends Response_Model
{
    public $table = 'ip_incoms';
    public $primary_key = 'ip_incoms.incom_id';
    public $date_modified_field = 'incom_date_modified';

    /**
     * @return array
     */
    public function statuses()
    {
        return array(
            '1' => array(
                'label' => trans('draft'),
                'class' => 'draft',
                'href' => 'incoms/status/draft'
            ),
            '2' => array(
                'label' => trans('sent'),
                'class' => 'sent',
                'href' => 'incoms/status/sent'
            ),
            '3' => array(
                'label' => trans('viewed'),
                'class' => 'viewed',
                'href' => 'incoms/status/viewed'
            ),
            '4' => array(
                'label' => trans('approved'),
                'class' => 'approved',
                'href' => 'incoms/status/approved'
            ),
            '5' => array(
                'label' => trans('rejected'),
                'class' => 'rejected',
                'href' => 'incoms/status/rejected'
            ),
            '6' => array(
                'label' => trans('canceled'),
                'class' => 'canceled',
                'href' => 'incoms/status/canceled'
            )
        );
    }

    public function default_select()
    {
        $this->db->select("
            SQL_CALC_FOUND_ROWS
            ip_users.*,
			ip_providers.*,
			ip_incom_amounts.incom_amount_id,
			IFnull(ip_incom_amounts.incom_item_subtotal, '0.00') AS incom_item_subtotal,
			IFnull(ip_incom_amounts.incom_item_tax_total, '0.00') AS incom_item_tax_total,
			IFnull(ip_incom_amounts.incom_tax_total, '0.00') AS incom_tax_total,
			IFnull(ip_incom_amounts.incom_total, '0.00') AS incom_total,
			ip_incoms.*", false);
    }

    public function default_order_by()
    {
        $this->db->order_by('ip_incoms.incom_id DESC');
    }

    public function default_join()
    {
        $this->db->join('ip_providers', 'ip_providers.provider_id = ip_incoms.provider_id');
        $this->db->join('ip_users', 'ip_users.user_id = ip_incoms.user_id');
        $this->db->join('ip_incom_amounts', 'ip_incom_amounts.incom_id = ip_incoms.incom_id', 'left');
    }

    /**
     * @return array
     */
    public function validation_rules()
    {
        return array(
            'provider_id' => array(
                'field' => 'provider_id',
                'label' => trans('provider'),
                'rules' => 'required'
            ),
            'incom_date_created' => array(
                'field' => 'incom_date_created',
                'label' => trans('incom_date'),
                'rules' => 'required'
            ),
            'incom_password' => array(
                'field' => 'incom_password',
                'label' => trans('incom_password')
            ),
            'user_id' => array(
                'field' => 'user_id',
                'label' => trans('user'),
                'rule' => 'required'
            )
        );
    }

    /**
     * @return array
     */
    public function validation_rules_save_incom()
    {
        return array(
            'incom_number' => array(
                'field' => 'incom_number',
                'label' => trans('incom') . ' #',
                'rules' => 'is_unique[ip_incoms.incom_number' . (($this->id) ? '.incom_id.' . $this->id : '') . ']'
            ),
            'incom_date_created' => array(
                'field' => 'incom_date_created',
                'label' => trans('date'),
                'rules' => 'required'
            ),
            'incom_date_expires' => array(
                'field' => 'incom_date_expires',
                'label' => trans('due_date'),
                'rules' => 'required'
            ),
            'incom_password' => array(
                'field' => 'incom_password',
                'label' => trans('incom_password')
            )
        );
    }

    /**
     * @param null $db_array
     * @return int|null
     */
    public function create($db_array = null)
    {
        $incom_id = parent::save(null, $db_array);

        // Create an incom amount record
        $db_array = array(
            'incom_id' => $incom_id
        );

        $this->db->insert('ip_incom_amounts', $db_array);

        return $incom_id;
    }

    /**
     * Copies incom items, tax rates, etc from source to target
     * @param int $source_id
     * @param int $target_id
     */
    public function copy_incom($source_id, $target_id)
    {
        $this->load->model('incoms/mdl_incom_items');

        $incom_items = $this->mdl_incom_items->where('incom_id', $source_id)->get()->result();

        foreach ($incom_items as $incom_item) {
            $db_array = array(
                'incom_id' => $target_id,
                'item_tax_rate_id' => $incom_item->item_tax_rate_id,
                'item_name' => $incom_item->item_name,
                'item_description' => $incom_item->item_description,
                'item_quantity' => $incom_item->item_quantity,
                'item_price' => $incom_item->item_price,
                'item_order' => $incom_item->item_order
            );

            $this->mdl_incom_items->save(null, $db_array);
        }

        $incom_tax_rates = $this->mdl_incom_tax_rates->where('incom_id', $source_id)->get()->result();

        foreach ($incom_tax_rates as $incom_tax_rate) {
            $db_array = array(
                'incom_id' => $target_id,
                'tax_rate_id' => $incom_tax_rate->tax_rate_id,
                'include_item_tax' => $incom_tax_rate->include_item_tax,
                'incom_tax_rate_amount' => $incom_tax_rate->incom_tax_rate_amount
            );

            $this->mdl_incom_tax_rates->save(null, $db_array);
        }

        // Copy the custom fields
        $this->load->model('custom_fields/mdl_incom_custom');
        $db_array = $this->mdl_incom_custom->where('incom_id', $source_id)->get()->row_array();

        if (count($db_array) > 2) {
            unset($db_array['incom_custom_id']);
            $db_array['incom_id'] = $target_id;
            $this->mdl_incom_custom->save_custom($target_id, $db_array);
        }
    }

    /**
     * @return array
     */
    public function db_array()
    {
        $db_array = parent::db_array();

        // Get the provider id for the submitted incom
        $this->load->model('providers/mdl_providers');
        $cid = $this->mdl_providers->where('ip_providers.provider_id', $db_array['provider_id'])->get()->row()->provider_id;
        $db_array['provider_id'] = $cid;

        $db_array['incom_date_created'] = date_to_mysql($db_array['incom_date_created']);

        $db_array['notes'] = get_setting('default_incom_notes');

        if (!isset($db_array['incom_status_id'])) {
            $db_array['incom_status_id'] = 1;
        }

        // Generate the unique url key
        $db_array['incom_url_key'] = $this->get_url_key();

        return $db_array;
    }

    /**
     * @return string
     */
    public function get_url_key()
    {
        $this->load->helper('string');
        return random_string('alnum', 32);
    }

    /**
     * @param int $incom_id
     */
    public function delete($incom_id)
    {
        parent::delete($incom_id);

        $this->load->helper('orphan');
        delete_orphans();
    }

    /**
     * @return $this
     */
    public function is_draft()
    {
        $this->filter_where('incom_status_id', 1);
        return $this;
    }

    /**
     * @return $this
     */
    public function is_sent()
    {
        $this->filter_where('incom_status_id', 2);
        return $this;
    }

    /**
     * @return $this
     */
    public function is_viewed()
    {
        $this->filter_where('incom_status_id', 3);
        return $this;
    }

    /**
     * @return $this
     */
    public function is_approved()
    {
        $this->filter_where('incom_status_id', 4);
        return $this;
    }

    /**
     * @return $this
     */
    public function is_rejected()
    {
        $this->filter_where('incom_status_id', 5);
        return $this;
    }

    /**
     * @return $this
     */
    public function is_canceled()
    {
        $this->filter_where('incom_status_id', 6);
        return $this;
    }

    /**
     * Used by guest module; includes only sent and viewed
     *
     * @return $this
     */
    public function is_open()
    {
        $this->filter_where_in('incom_status_id', array(2, 3));
        return $this;
    }

    /**
     * @return $this
     */
    public function guest_visible()
    {
        $this->filter_where_in('incom_status_id', array(2, 3, 4, 5));
        return $this;
    }

    /**
     * @param $provider_id
     * @return $this
     */
    public function by_provider($provider_id)
    {
        $this->filter_where('ip_incoms.provider_id', $provider_id);
        return $this;
    }

    /**
     * @param $incomincom_id
     */
    public function reject_incom_by_id($incom_id)
    {
        $this->db->where_in('incom_status_id', array(2, 3));
        $this->db->where('incom_id', $incom_id);
        $this->db->set('incom_status_id', 5);
        $this->db->update('ip_incoms');
    }

}
