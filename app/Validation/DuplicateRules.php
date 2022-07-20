<?php

namespace App\Validation;

use Config\Services;
use Config\Database;

class DuplicateRules
{
    public function is_exists()
    {
        $request = Services::request();

        $post = $request->getVar();
        $table = json_decode($post['table']);

        $list = arrTableLine($table);
        $array = [];

        // Check field is exists
        for ($i = 0; $i < count($list); $i++) {
            if (isset($list[$i]['assetcode']) && $list[$i]['assetcode'] !== '') {
                $array[] = $list[$i]['assetcode'];
            }
        }

        // Check duplicate array
        $result = array_duplicates($array);

        if (empty(count($result)))
            return true;

        return false;
    }

    /**
     * Checks the database to see if the given value is exists. Can
     * ignore a single record by field/value to make it useful during
     * record updates and check a single record based on another field/value.
     *
     * Example:
     *    is_exist[table.field,ignore_field,ignore_value,another_field,another_value]
     *    is_exist[users.email,id,5,foreign_id,1]
     */
    public function is_exist(?string $str, string $field, array $data): bool
    {
        [$field, $ignoreField, $ignoreValue, $anotherField, $anotherValue] = array_pad(explode(',', $field), 5, null);

        sscanf($field, '%[^.].%[^.]', $table, $field);

        $row = Database::connect($data['DBGroup'] ?? null)
            ->table($table)
            ->select('1')
            ->where($field, $str)
            ->limit(1);

        if (!empty($ignoreField) && !empty($ignoreValue) && !preg_match('/^\{(\w+)\}$/', $ignoreValue)) {
            $row = $row->where("{$ignoreField} !=", $ignoreValue);
        }

        if (!empty($anotherField) && !empty($anotherValue) && !preg_match('/^\{(\w+)\}$/', $anotherValue)) {
            $row = $row->where("{$anotherField}", $anotherValue);
        }

        return $row->get()->getRow() === null;
    }
}
