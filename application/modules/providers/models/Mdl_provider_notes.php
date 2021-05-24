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
 * Class Mdl_Provider_Notes
 */
class Mdl_Provider_Notes extends Response_Model
{
    public $table = 'ip_provider_notes';
    public $primary_key = 'ip_provider_notes.provider_note_id';

    public function default_order_by()
    {
        $this->db->order_by('ip_provider_notes.provider_note_date DESC');
    }

    public function validation_rules()
    {
        return array(
            'provider_id' => array(
                'field' => 'provider_id',
                'label' => trans('provider'),
                'rules' => 'required'
            ),
            'provider_note' => array(
                'field' => 'provider_note',
                'label' => trans('note'),
                'rules' => 'required'
            )
        );
    }

    public function db_array()
    {
        $db_array = parent::db_array();

        $db_array['provider_note_date'] = date('Y-m-d');

        return $db_array;
    }

}
