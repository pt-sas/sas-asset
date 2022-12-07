<?php

function message($param, $value, $message)
{
    if (strtolower($param) == 'success') {
        return [
            [
                strtolower($param)      => $value,
                'message'               => $message
            ]
        ];
    } else if (strtolower($param) == 'error') {
        return [
            [
                strtolower($param)      => $value,
                'message'               => $message
            ]
        ];
    } else {
        return [
            [
                'parameter'             => $param,
                'message'               => 'Undefined message'
            ]
        ];
    }
}

function active($string)
{
    return $string === 'Y' ? '<center><span class="badge badge-success">Yes</span></center>' :
        '<center><span class="badge badge-danger">No</span></center>';
}

function truncate($string, $length = 50, $append = "...")
{
    $string = trim($string);

    if (strlen($string) > $length) {
        $string = wordwrap($string, $length);
        $string = explode("\n", $string, 2);
        $string = $string[0] . $append;
    }

    return $string;
}

function setCheckbox($string)
{
    return $string ? 'Y' : 'N';
}

/**
 * To set and get length data from table line
 *
 * @param [type] $table
 * @return void
 */
function countLine($count)
{
    return count($count) == 0 ? "" : $count;
}

/**
 * Remove special character on the format rupiah
 *
 * @param [type] $rupiah
 * @return int
 */
function replaceFormat(string $rupiah)
{
    return preg_replace("/\./", "", $rupiah);
}

/**
 * Convert format number to rupiah
 *
 * @param [type] $numeric
 * @return float
 */
function formatRupiah(int $numeric)
{
    return number_format($numeric, 0, '', '.');
}

/**
 * Populate array table
 *
 * @param array $table
 * @return array
 */
function arrTableLine(array $table, string $str = null)
{
    $result = [];

    if (empty($str))
        $str = "line";

    foreach ($table as $value) :
        foreach ($value as $key => $val) :
            $row = [];
            $row[$key . '_' . $str] = $val;

            $result[] = $row;
        endforeach;
    endforeach;

    return $result;
}

function array_duplicates(array $array)
{
    return array_diff_assoc($array, array_unique($array));
}

function notification($param)
{
    $msg = '';

    if (strtolower($param) == 'insert')
        $msg = 'Your data has been inserted successfully !';
    else
        $msg = 'Your data has been updated successfully !';

    return $msg;
}

/**
 * Return badge info document status table
 *
 * @param string $str
 * @return void
 */
function docStatus(string $str)
{
    $msg = "";

    if ($str === "CO")
        $msg .= '<center><span class="badge badge-success">Completed</span></center>';
    else if ($str === "IP")
        $msg .= '<center><span class="badge badge-info">In Progress</span></center>';
    else if ($str === "VO")
        $msg .= '<center><span class="badge badge-primary">Voided</span></center>';
    else if ($str === "IN")
        $msg .= '<center><span class="badge badge-danger">Invalid</span></center>';
    else
        $msg .= '<center><span class="badge badge-warning">Drafted</span></center>';

    return $msg;
}

function addYear($date, string $value)
{
    return strtotime("+" . $value . " years", strtotime($date));
}

/**
 *  Array sum based on data
 *
 * @param string $field Column data
 * @param array $data Data
 * @return void
 */
function arrSumField(string $field, array $data)
{
    $arr = [];

    foreach ($data as $value) :
        $arr[] = $value->{$field};
    endforeach;

    return array_sum($arr);
}

/**
 * Associative Array sort by
 *
 * @return void
 */
function array_orderby()
{
    $args = func_get_args();
    $data = array_shift($args);
    foreach ($args as $n => $field) {
        if (is_string($field)) {
            $tmp = array();
            foreach ($data as $key => $row)
                $tmp[$key] = $row[$field];
            $args[$n] = $tmp;
        }
    }
    $args[] = &$data;
    call_user_func_array('array_multisort', $args);
    return array_pop($args);
}
