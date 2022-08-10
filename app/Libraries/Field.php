<?php

namespace App\Libraries;

use Config\Services;

/**
 * Class to represent Field Name and Retrieve data from database based on column
 *
 * @author Oki Permana
 */
class Field
{
    protected $db;
    protected $validation;

    public function __construct()
    {
        $this->db = db_connect();
        $this->validation = Services::validation();
    }

    /**
     * Retrieve field and data from database
     *
     * $table
     * $data result from database
     * $query type join table or not
     */
    public function store($table, $data, $query = null)
    {
        $result = [];

        if ($this->db->fieldExists('code', $table)) {
            $result[] = [
                'field' => 'title',
                'label' => $data[0]->code
            ];
        } else if ($this->db->fieldExists('value', $table) && $this->db->fieldExists('name', $table)) {
            $result[] = [
                'field' => 'title',
                'label' => $data[0]->value . '_' . $data[0]->name
            ];
        } else if ($this->db->fieldExists('value', $table)) {
            $result[] = [
                'field' => 'title',
                'label' => $data[0]->value
            ];
        } else if ($this->db->fieldExists('name', $table)) {
            $result[] = [
                'field' => 'title',
                'label' => $data[0]->name
            ];
        } else if ($this->db->fieldExists('title', $table)) {
            $result[] = [
                'field' => 'title',
                'label' => $data[0]->title
            ];
        } else if ($this->db->fieldExists('documentno', $table)) {
            $result[] = [
                'field' => 'title',
                'label' => $data[0]->documentno
            ];
        } else if ($this->db->fieldExists('assetcode', $table)) {
            $result[] = [
                'field' => 'title',
                'label' => $data[0]->assetcode
            ];
        } else {
            $result[] = [
                'field' => 'title',
                'label' => 'Title not found'
            ];
        }

        /**
         * Check generating data using query or modeling data
         * #empty query using modeling data
         */
        if (empty($query)) {
            $fields = $this->db->getFieldNames($table);
            foreach ($fields as $field) :
                foreach ($data as $row) :
                    $result[] = [
                        'field' =>  $field,
                        'label' =>  $row->$field
                    ];
                endforeach;
            endforeach;
        } else if (is_object($query)) {
            $fields = $query->getFieldNames();
            foreach ($fields as $field) :
                foreach ($data as $row) :
                    $result[] = [
                        'field' =>  $field,
                        'label' =>  $row->$field
                    ];
                endforeach;
            endforeach;
        } else if (is_string($query)) {
            $result = $data;
        }

        return $result;
    }

    /**
     * Get error validation field
     *
     * $table untuk mendapatkan nama table
     * $field_post mendapatkan field dari method post
     * @return $result
     */
    function errorValidation($table, $field_post)
    {
        $allError = $this->validation->getErrors();

        $result = [];
        $arrField = [];

        $result[] = [
            'error' => true,
            'field' => $table
        ];

        // Populate array field from object all error
        foreach ($allError as $field => $msg) :
            if (strpos($field, '.*')) {
                $field_replace = str_replace('.*', '', $field);
                $array = explode('.', $field_replace);

                $arrField[] = end($array);
            }
        endforeach;

        foreach ($field_post as $key => $field) :
            // Check field is array or not
            if (!is_array($field)) {
                // Validation field is not inarray
                if (in_array($key, $arrField)) {
                    $result[] = [
                        'error' => 'error_' . $key,
                        'field' => $key,
                        'label' => $this->validation->getError($key . '.*')
                    ];
                } else {
                    $result[] = [
                        'error' => 'error_' . $key,
                        'field' => $key,
                        'label' => $this->validation->getError($key)
                    ];
                }
            } else {
                foreach ($field as $key2 => $obj) :
                    foreach ($arrField as $row) :
                        $errorField = $key . '.' . $key2 . '.*.' . $row;
                        $result[] = [
                            'error' => 'error_' . $key2,
                            'field' => $row,
                            'label' => $this->validation->getError($errorField)
                        ];
                    endforeach;
                endforeach;
            }
        endforeach;

        return $result;
    }

    /**
     * fieldTable
     * $tag => Tag element HTML
     * $type => Jenis type input tag
     * $name => Nama tag element
     * $class => Jenis class input atau select
     * $required => Mandatory field
     * $readonly => Readonly field
     * $checked => Checked value untuk type checkbox
     * $data => array data
     * $defaultValue => Default value input
     * $length => Panjang tag element
     */
    function fieldTable($tag, $type = null, $name, $class = null, $required = false, $readonly = null, $checked = null, $list = [], $defaultValue = null, $length = 50, $field = 'id', $field2 = 'name')
    {
        $div = '<div class="form-group">';

        $element = '';

        if ($tag === 'input') {
            if ($type === 'number' || $class === 'number') {
                $element .= '<input type="' . $type . '" class="form-control line ' . $class . '" name="' . $name . '" value="' . $defaultValue . '" ' . $readonly . ' ' . $required . ' style="width: ' . $length . 'px;">';
            } else if ($type === 'checkbox') {
                $disabled = '';

                if ($readonly == 'readonly') {
                    $disabled = 'disabled';
                }

                $div = '<div class="form-check">';
                $element .= '<label class="form-check-label">';

                // Check default value is not null
                if (!empty($defaultValue)) {
                    if ($defaultValue === 'Y')
                        $element .= '<input type="' . $type . '" class="form-check-input line ' . $class . '" name="' . $name . '" checked ' . $disabled . '>';
                    else
                        $element .= '<input type="' . $type . '" class="form-check-input line ' . $class . '" name="' . $name . '" ' . $disabled . '>';
                } else {
                    $element .= '<input type="' . $type . '" class="form-check-input line ' . $class . '" name="' . $name . '" ' . $checked . ' ' . $disabled . '>';
                }

                $element .= '<span class="form-check-sign"></span></label>';
            } else {
                if ($class === 'rupiah') {
                    $element .= '<input type="' . $type . '" class="form-control text-right line ' . $class . '" name="' . $name . '" value="' . $defaultValue . '" ' . $readonly . ' ' . $required . ' style="width: ' . $length . 'px;">';
                } else {
                    $element .= '<input type="' . $type . '" class="form-control line ' . $class . '" name="' . $name . '" value="' . $defaultValue . '" ' . $readonly . ' ' . $required . ' style="width: ' . $length . 'px;">';
                }
            }
        }

        if ($tag === 'select') {
            $disabled = '';
            $select = 'select2';

            if ($readonly == 'readonly') {
                $disabled = 'disabled';
            }

            // Condition to merge select2 and another class
            if (!empty($class)) {
                $class = $select . ' ' . $class;
            } else {
                $class = $select;
            }

            $element .= '<select class="form-control line ' . $class . '" name="' . $name . '" ' . $disabled . ' ' . $required . ' style="width: ' . $length . 'px;">';

            if (is_array($list) && count($list) > 0) {
                $element .= '<option value=""></option>';

                foreach ($list as $row) :
                    // Check default value is not null and default value equal $field
                    if (!empty($defaultValue) && ((is_string($defaultValue) && strtoupper($defaultValue) == strtoupper($row->$field2)) || ($defaultValue == $row->$field)))
                        $element .= '<option value="' . $row->$field . '" selected>' . $row->$field2 . '</option>';
                    else
                        $element .= '<option value="' . $row->$field . '">' . $row->$field2 . '</option>';
                endforeach;
            }

            $element .= '</select>';
        }

        if ($tag === 'button') {
            if ($field !== 'id') {
                // To set value on the variable field
                $field = $defaultValue;

                // defaultValue to empty value
                $defaultValue = "";
            }

            $element .= '<button type="button" title="' . ucwords($name) . '" class="btn btn-link btn-danger line btn_delete" id="' . $defaultValue . '" name="' . $name . '" value="' . $field . '">
                                    <i class="fa fa-times"></i>
                                </button>';
        }

        $div .= $element;

        $div .= '</div>';

        return $div;
    }

    /**
     * Function to add array key to more than one data
     *
     * @param [type] $table
     * @param [type] $data
     * @param string $field
     * @param [type] $value
     * @param [type] $text
     * @return void
     */
    public function setDataSelect($table, $data, $field = 'id', $value, $text)
    {
        foreach ($data as $row) :
            if ($this->db->fieldExists($field, $table))
                $row->$field = ([
                    'id' => $value,
                    'name' => $text
                ]);
        endforeach;

        return $data;
    }

    /**
     * Function for merge object to array object
     *
     * @param [type] $arr
     * @param array $data array object
     * @return void
     */
    public function mergeArrObject($arr, $data = [])
    {
        foreach ($arr as $key => $value) :
            $row = $value;

            if (count($data) > 0)
                foreach ($data as $field => $val) :
                    $row->$field = $val;
                endforeach;

            $arr[$key] = $row;
        endforeach;

        return $arr;
    }
}
