<?php
if (!defined('BASEPATH')) { exit('No direct script access allowed'); }

/*
 * InvoicePlane
 *
 * @author		InvoicePlane Developers & Contributors
 * @copyright	Copyright (c) 2012 - 2018 InvoicePlane.com
 * @license		https://invoiceplane.com/license.txt
 * @link		https://invoiceplane.com
 */

/**
 * Class Mdl_Pictures
 */
class Mdl_Pictures extends Response_Model
{
    public $table = 'ip_pictures';
    public $primary_key = 'ip_pictures.picture_id';
    public $submap = 'uploads/pictures/';
    public $picture_map = false;
    public $picture_url = false;
    public $max_size = 1024; // KB
    public $max_height = 256;
    public $max_width = 256;
    
    public function max_size() 
    {
        return $this->max_size * 1024;
    }
    
    /**
     * Admin_Controller constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->picture_map = FCPATH . $this->submap;
        $this->picture_url = base_url() .  $this->submap;
    }

    public function default_select()
    {
        $this->db->select('SQL_CALC_FOUND_ROWS *', false);
    }

    public function default_order_by()
    {
        $this->db->order_by('ip_pictures.picture_name');
    }

    public function by_picture($match)
    {
        $this->db->group_start();
        $this->db->like('ip_pictures.picture_name', $match);
        $this->db->group_end();
    }

    public function run_picture_validation()
    {
        $this->validation_rules = "validation_rules";
        if(isset($_FILES['picture']) && isset($_FILES['picture']['name'])) {
            $this->form_values["picture_name"] = $_FILES['picture']['name'];
        } else if (!key_exists ("picture_description", $_POST)){
            return false;
        }
        
        foreach (array_keys($_POST) as $key) {
            $this->form_values[$key] = $this->input->post($key);
        }

        return true;
    }
    
    /**
     * @return array
     */
    public function validation_rules()
    {
        return array(
            'picture_description' => array(
                'field' => 'picture_description',
                'label' => trans('picture_description'),
                'rules' => ''
            ),
        );
    }


    /**
     * @param $picture_id
     * @return null
     */
    public function get_picture($picture_id)
    {
        $this->db->select('picture_name');
        $this->db->where($this->primary_key, $picture_id);
        $query = $this->db->get($this->table);

        if ($query->row()) {
            return $query->row();
        } else {
            return null;
        }
    }

    /**
     * Returns the picture url
     *
     * @param $picture_id
     * @return string
     */
    public function get_url($picture)    
    {
        return $this->picture_url . $picture->picture_name;
    }
    
    /**
     * Returns the picture for html
     *
     * @param $picture_id
     * @return string
     */
    public function htmlpicture($picture_id = 0)    
    {
        if ($picture_id == 0) {
            $picture_id =$this->form_value('picture_id');
        }
        $picture= $this->get_picture($picture_id);
        if ($picture) {
            return '<img width="160" src="' . $this->get_url($picture) . '">';
        }

        return '';
    }
    
    /**
     * Returns the picture for pdf
     *
     * @param $picture_id
     * @return string
     */
    public function pdfpicture($picture_id)
    {
        $picture= $this->get_picture($picture_id);
        if ($picture) {
            return '<img src="file://' . $this->picture_map . $picture->picture_name . '" id="picture_'.$picture_id.'">';
        }

        return '';
    }
    
    public function save($id = null, $db_array = null)
    {
        if(isset($_FILES['picture']) && isset($_FILES['picture']['name'])) {
            if (!(is_dir($this->picture_map) || is_link($this->picture_map))) {
                mkdir($this->picture_map, 0777);
                chmod($this->picture_map, 0777);
            }

            $upload_config = array(
                'upload_path' => $this->picture_map,
                'allowed_types' => 'gif|jpg|jpeg|png|svg',
                'max_size' => $this->max_size,
                'max_width' => $this->max_width,
                'max_height' => $this->max_height
                );
            $this->load->library('upload', $upload_config);

            if (!$this->upload->do_upload('picture')) {
                $this->session->set_flashdata('alert_error', $this->upload->display_errors());
                redirect('pictures');
           }

           $db_array["picture_name"] = $this->upload->data()['file_name'];
        }

        return parent::save($id, $db_array);
    }

    public function delete($id)
    {
        $picture= $this->get_picture($id);
        unlink($this->picture_map . $picture->picture_name);
        parent::delete($id);
    }

    /**
     * @param $image_id
     * @param $field_id
     */
    public function SelectBlock($picture_id, $field_id ="picture_id")
    {
        $pictures = $this->get()->result();
        echo "<div class='panel-body'>\n";
        echo "<input type='checkbox' id='image-picker-switch' class='image-picker-switch'/>\n";
        echo "<label for='image-picker-switch'>" . trans('picture_picker') . "</label>\n";
        echo "<select id='" . $field_id . "' name='" . $field_id . "' class='image-picker'>\n";
        echo "<option>" . trans("select_picture") . "</option>\n";
        $picture_url = "";
        foreach ($pictures as $picture) {
            //echo "<optgroup label='XXX'>\n"; ** for further development of groups.
            echo "<option data-img-src='" . $this->get_url($picture) . "' value='" . $picture->picture_id . "'"; 
            if ($picture_id == $picture->picture_id) {
                echo " selected";
                $picture_url = $this->get_url($picture);
            }
            echo ">" . $picture->picture_name . "</option>\n";
            //echo "</optgroup>\n"; ** for further development of groups.
        }
        echo "</select>\n";
        echo "<div class='thumbnail selected'>\n";
        echo "<img id='" . $field_id . "_sel' class='image-picker_sel image_picker_image' src='" . $picture_url . "'>\n";
        echo "</div>\n";
    }
}
