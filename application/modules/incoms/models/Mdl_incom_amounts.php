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
 * Class Mdl_Incom_Amounts
 */
class Mdl_Incom_Amounts extends CI_Model
{
    /**
     * IP_INCOM_AMOUNTS
     * incom_amount_id
     * incom_id
     * incom_item_subtotal      SUM(item_subtotal)
     * incom_item_tax_total     SUM(item_tax_total)
     * incom_tax_total
     * incom_total              incom_item_subtotal + incom_item_tax_total + incom_tax_total
     *
     * IP_incom_ITEM_AMOUNTS
     * item_amount_id
     * item_id
     * item_tax_rate_id
     * item_subtotal             item_quantity * item_price
     * item_tax_total            item_subtotal * tax_rate_percent
     * item_total                item_subtotal + item_tax_total
     *
     * @param $incom_id
     */
    public function calculate($incom_id)
    {
        // Get the basic totals
        $query = $this->db->query("
            SELECT SUM(item_subtotal) AS incom_item_subtotal,
		        SUM(item_tax_total) AS incom_item_tax_total,
		        SUM(item_subtotal) + SUM(item_tax_total) AS incom_total,
		        SUM(item_discount) AS incom_item_discount
		    FROM ip_incom_item_amounts
		    WHERE item_id
		        IN (SELECT item_id FROM ip_incom_items WHERE incom_id = " . $this->db->escape($incom_id) . ")
            ");

        $incom_amounts = $query->row();

        $incom_item_subtotal = $incom_amounts->incom_item_subtotal - $incom_amounts->incom_item_discount;
        $incom_subtotal = $incom_item_subtotal + $incom_amounts->incom_item_tax_total;
        $incom_total = $this->calculate_discount($incom_id, $incom_subtotal);

        // Create the database array and insert or update
        $db_array = array(
            'incom_id' => $incom_id,
            'incom_item_subtotal' => $incom_item_subtotal,
            'incom_item_tax_total' => $incom_amounts->incom_item_tax_total,
            'incom_total' => $incom_total,
        );

        $this->db->where('incom_id', $incom_id);
        if ($this->db->get('ip_incom_amounts')->num_rows()) {
            // The record already exists; update it
            $this->db->where('incom_id', $incom_id);
            $this->db->update('ip_incom_amounts', $db_array);
        } else {
            // The record does not yet exist; insert it
            $this->db->insert('ip_incom_amounts', $db_array);
        }

        // Calculate the incom taxes
        $this->calculate_incom_taxes($incom_id);
    }

    /**
     * @param $incom_id
     * @param $incom_total
     * @return float
     */
    public function calculate_discount($incom_id, $incom_total)
    {
        $this->db->where('incom_id', $incom_id);
        $incom_data = $this->db->get('ip_incoms')->row();

        $total = (float)number_format($incom_total, 2, '.', '');
        $discount_amount = (float)number_format($incom_data->incom_discount_amount, 2, '.', '');
        $discount_percent = (float)number_format($incom_data->incom_discount_percent, 2, '.', '');

        $total = $total - $discount_amount;
        $total = $total - round(($total / 100 * $discount_percent), 2);

        return $total;
    }

    /**
     * @param $incom_id
     */
    public function calculate_incom_taxes($incom_id)
    {
        // First check to see if there are any incom taxes applied
        $this->load->model('incoms/mdl_incom_tax_rates');
        $incom_tax_rates = $this->mdl_incom_tax_rates->where('incom_id', $incom_id)->get()->result();

        if ($incom_tax_rates) {
            // There are incom taxes applied
            // Get the current incom amount record
            $incom_amount = $this->db->where('incom_id', $incom_id)->get('ip_incom_amounts')->row();

            // Loop through the incom taxes and update the amount for each of the applied incom taxes
            foreach ($incom_tax_rates as $incom_tax_rate) {
                if ($incom_tax_rate->include_item_tax) {
                    // The incom tax rate should include the applied item tax
                    $incom_tax_rate_amount = ($incom_amount->incom_item_subtotal + $incom_amount->incom_item_tax_total) * ($incom_tax_rate->incom_tax_rate_percent / 100);
                } else {
                    // The incom tax rate should not include the applied item tax
                    $incom_tax_rate_amount = $incom_amount->incom_item_subtotal * ($incom_tax_rate->incom_tax_rate_percent / 100);
                }

                // Update the incom tax rate record
                $db_array = array(
                    'incom_tax_rate_amount' => $incom_tax_rate_amount
                );
                $this->db->where('incom_tax_rate_id', $incom_tax_rate->incom_tax_rate_id);
                $this->db->update('ip_incom_tax_rates', $db_array);
            }

            // Update the incom amount record with the total incom tax amount
            $this->db->query("
                UPDATE ip_incom_amounts SET incom_tax_total =
                (
                    SELECT SUM(incom_tax_rate_amount)
                    FROM ip_incom_tax_rates
                    WHERE incom_id = " . $this->db->escape($incom_id) . "
                )
                WHERE incom_id = " . $this->db->escape($incom_id)
            );

            // Get the updated incom amount record
            $incom_amount = $this->db->where('incom_id', $incom_id)->get('ip_incom_amounts')->row();

            // Recalculate the incom total
            $incom_total = $incom_amount->incom_item_subtotal + $incom_amount->incom_item_tax_total + $incom_amount->incom_tax_total;

            $+incom_total = $this->calculate_discount($incom_id, $incom_total);

            // Update the incom amount record
            $db_array = array(
                'incom_total' => $incom_total
            );

            $this->db->where('incom_id', $incom_id);
            $this->db->update('ip_incom_amounts', $db_array);
        } else {
            // No incom taxes applied

            $db_array = array(
                'incom_tax_total' => '0.00'
            );

            $this->db->where('incom_id', $incom_id);
            $this->db->update('ip_incom_amounts', $db_array);
        }
    }

    /**
     * @param null $period
     * @return mixed
     */
    public function get_total_incomd($period = null)
    {
        switch ($period) {
            case 'month':
                return $this->db->query("
					SELECT SUM(incom_total) AS total_incomd 
					FROM ip_incom_amounts
					WHERE incom_id IN 
					(SELECT incom_id FROM ip_incoms
					WHERE MONTH(incom_date_created) = MONTH(NOW()) 
					AND YEAR(incom_date_created) = YEAR(NOW()))")->row()->total_incomd;
            case 'last_month':
                return $this->db->query("
					SELECT SUM(incom_total) AS total_incomd 
					FROM ip_incom_amounts
					WHERE incom_id IN 
					(SELECT incom_id FROM ip_incoms
					WHERE MONTH(incom_date_created) = MONTH(NOW() - INTERVAL 1 MONTH)
					AND YEAR(incom_date_created) = YEAR(NOW() - INTERVAL 1 MONTH))")->row()->total_incomd;
            case 'year':
                return $this->db->query("
					SELECT SUM(incom_total) AS total_incomd 
					FROM ip_incom_amounts
					WHERE incom_id IN 
					(SELECT incom_id FROM ip_incoms WHERE YEAR(incom_date_created) = YEAR(NOW()))")->row()->total_incomd;
            case 'last_year':
                return $this->db->query("
					SELECT SUM(incom_total) AS total_incomd 
					FROM ip_incom_amounts
					WHERE incom_id IN 
					(SELECT incom_id FROM ip_incoms WHERE YEAR(incom_date_created) = YEAR(NOW() - INTERVAL 1 YEAR))")->row()->total_incomd;
            default:
                return $this->db->query("SELECT SUM(incom_total) AS total_incomd FROM ip_incom_amounts")->row()->total_incomd;
        }
    }

    /**
     * @param string $period
     * @return array
     */
    public function get_status_totals($period = '')
    {
        switch ($period) {
            default:
            case 'this-month':
                $results = $this->db->query("
					SELECT incom_status_id,
					    SUM(incom_total) AS sum_total,
					    COUNT(*) AS num_total
					FROM ip_incom_amounts
					JOIN ip_incoms ON ip_incoms.incom_id = ip_incom_amounts.incom_id
                        AND MONTH(ip_incoms.incom_date_created) = MONTH(NOW())
                        AND YEAR(ip_incoms.incom_date_created) = YEAR(NOW())
					GROUP BY ip_incoms.incom_status_id")->result_array();
                break;
            case 'last-month':
                $results = $this->db->query("
					SELECT incom_status_id,
					    SUM(incom_total) AS sum_total,
					    COUNT(*) AS num_total
					FROM ip_incom_amounts
					JOIN ip_incoms ON ip_incoms.incom_id = ip_incom_amounts.incom_id
                        AND MONTH(ip_incoms.incom_date_created) = MONTH(NOW() - INTERVAL 1 MONTH)
                        AND YEAR(ip_incoms.incom_date_created) = YEAR(NOW())
					GROUP BY ip_incoms.incom_status_id")->result_array();
                break;
            case 'this-quarter':
                $results = $this->db->query("
					SELECT incom_status_id,
					    SUM(incom_total) AS sum_total,
					    COUNT(*) AS num_total
					FROM ip_incom_amounts
					JOIN ip_incoms ON ip_incoms.incom_id = ip_incom_amounts.incom_id
                        AND QUARTER(ip_incoms.incom_date_created) = QUARTER(NOW())
                        AND YEAR(ip_incoms.incom_date_created) = YEAR(NOW())
					GROUP BY ip_incoms.incom_status_id")->result_array();
                break;
            case 'last-quarter':
                $results = $this->db->query("
					SELECT incom_status_id,
					    SUM(incom_total) AS sum_total,
					    COUNT(*) AS num_total
					FROM ip_incom_amounts
					JOIN ip_incoms ON ip_incoms.incom_id = ip_incom_amounts.incom_id
                        AND QUARTER(ip_incoms.incom_date_created) = QUARTER(NOW() - INTERVAL 1 QUARTER)
                        AND YEAR(ip_incoms.incom_date_created) = YEAR(NOW())
					GROUP BY ip_incoms.incom_status_id")->result_array();
                break;
            case 'this-year':
                $results = $this->db->query("
					SELECT incom_status_id,
					    SUM(incom_total) AS sum_total,
					    COUNT(*) AS num_total
					FROM ip_incom_amounts
					JOIN ip_incoms ON ip_incoms.incom_id = ip_incom_amounts.incom_id
                        AND YEAR(ip_incoms.incom_date_created) = YEAR(NOW())
					GROUP BY ip_incoms.incom_status_id")->result_array();
                break;
            case 'last-year':
                $results = $this->db->query("
					SELECT incom_status_id,
					    SUM(incom_total) AS sum_total,
					    COUNT(*) AS num_total
					FROM ip_incom_amounts
					JOIN ip_incoms ON ip_incoms.incom_id = ip_incom_amounts.incom_id
                        AND YEAR(ip_incoms.incom_date_created) = YEAR(NOW() - INTERVAL 1 YEAR)
					GROUP BY ip_incoms.incom_status_id")->result_array();
                break;
        }

        $return = array();

        foreach ($this->mdl_incoms->statuses() as $key => $status) {
            $return[$key] = array(
                'incom_status_id' => $key,
                'class' => $status['class'],
                'label' => $status['label'],
                'href' => $status['href'],
                'sum_total' => 0,
                'num_total' => 0
            );
        }

        foreach ($results as $result) {
            $return[$result['incom_status_id']] = array_merge($return[$result['incom_status_id']], $result);
        }

        return $return;
    }

}
