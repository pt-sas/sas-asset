<?php

namespace App\Models;

use CodeIgniter\Model;
use CodeIgniter\HTTP\RequestInterface;
use App\Models\M_Product;
use App\Models\M_SequenceNo;
use stdClass;

class M_Sequence extends Model
{
    protected $table      = 'md_sequence';
    protected $primaryKey = 'md_sequence_id';
    protected $allowedFields = [
        'name',
        'description',
        'isactive',
        'vformat',
        'isautosequence',
        'incrementno',
        'startno',
        'currentnext',
        'prefix',
        'suffix',
        'startnewyear',
        'datecolumn',
        'decimalpattern',
        'startnewmonth',
        'isgassetlevelsequence',
        'gassetcolumn',
        'iscategorylevelsequence',
        'categorycolumn',
        'maxvalue',
        'created_by',
        'updated_by',
    ];
    protected $useTimestamps = true;
    protected $returnType = 'App\Entities\Sequence';
    protected $column_order = [
        '', // Hide column
        '', // Number column
        'md_sequence.name',
        'md_sequence.description',
        'md_sequence.isautosequence',
        'md_sequence.vformat',
        'md_sequence.incrementno',
        'md_sequence.maxvalue',
        'md_sequence.currentnext',
        'md_sequence.decimalpattern',
        // 'md_sequence.prefix',
        // 'md_sequence.suffix',
        'md_sequence.isgassetlevelsequence',
        'md_sequence.gassetcolumn',
        'md_sequence.startnewyear',
        'md_sequence.datecolumn',
        'md_sequence.startnewmonth',
        'md_sequence.startno',
        'md_sequence.isactive'
    ];
    protected $column_search = [
        'md_sequence.name',
        'md_sequence.description',
        'md_sequence.isautosequence',
        'md_sequence.vformat',
        'md_sequence.incrementno',
        'md_sequence.maxvalue',
        'md_sequence.currentnext',
        'md_sequence.decimalpattern',
        // 'md_sequence.prefix',
        // 'md_sequence.suffix',
        'md_sequence.isgassetlevelsequence',
        'md_sequence.gassetcolumn',
        'md_sequence.startnewyear',
        'md_sequence.datecolumn',
        'md_sequence.startnewmonth',
        'md_sequence.startno',
        'md_sequence.isactive'
    ];
    protected $order = ['name' => 'ASC'];
    protected $request;
    protected $db;
    protected $builder;

    public function __construct(RequestInterface $request)
    {
        parent::__construct();
        $this->db = db_connect();
        $this->request = $request;
        $this->builder = $this->db->table($this->table);
    }

    public function getDocumentNoFromSeq($po, $line = [])
    {
        $product = new M_Product($this->request);
        $sequenceNo = new M_SequenceNo($this->request);
        $entity = new \App\Entities\Sequence();

        $NoYearNoMonth = '-';
        $arrData = [];
        $result = [];

        foreach ($line as $key => $val) :
            $obj = new stdClass();

            $row = $product->getProductAsset($val->getProductId())->getRow();

            $date_column = $row->datecolumn;

            $obj->md_sequence_id = $row->md_sequence_id;
            $obj->MD_GroupAsset_ID = 0;
            $obj->MD_Category_ID = 0;
            $obj->calendaryearmonth = $NoYearNoMonth;

            if ($row->isgassetlevelsequence === 'Y' && $row->gassetcolumn === 'MD_GroupAsset_ID') {
                $columnName = $row->gassetcolumn;
                $obj->$columnName = $row->md_groupasset_id;
            }

            if ($row->iscategorylevelsequence === 'Y' && $row->categorycolumn === 'MD_Category_ID') {
                $columnName = $row->categorycolumn;
                $obj->$columnName = $row->md_category_id;
            }

            $obj->prefix = $row->groupasset_code . '/' . $row->category_code . '/';

            if ($row->startnewyear === 'Y') {

                if ($row->startnewmonth === 'Y')
                    $format = 'ym';
                else
                    $format = 'y';


                if ($po && !empty($date_column)) {
                    $doc_date = strtotime($po->$date_column);
                    $obj->calendaryearmonth = date($format, $doc_date);
                } else {
                    $obj->calendaryearmonth = date($format);
                }

                $obj->prefix = $row->groupasset_code . '/' . $row->category_code . '/' . $obj->calendaryearmonth . '/';
            }

            $obj->line_id = $val->getReceiptDetailId();

            $arrData[$key] = (array) $obj;
        endforeach;

        //? Process array data 
        foreach ($arrData as $key => $val) :
            $md_sequence_id = $val['md_sequence_id'];
            $md_groupasset_id = $val['MD_GroupAsset_ID'];
            $md_category_id = $val['MD_Category_ID'];
            $calendaryearmonth = $val['calendaryearmonth'];

            $seq = $this->find($md_sequence_id);

            $increment = $seq->getIncrementNo();
            $current_next = $seq->getCurrentNext();
            $decimal_pattern = $seq->getDecimalPattern();
            $start_no = $seq->getStartNo();
            $max_value = $seq->getMaxValue();
            // $prefix = $seq->getPrefix();
            // $sufix = $seq->getSuffix();

            $prefix = $val['prefix'];
            $line_id = $val['line_id'];

            //* string length
            $str_length = strlen($decimal_pattern);


            if (empty($md_groupasset_id) && empty($md_category_id) && $calendaryearmonth === $NoYearNoMonth) {
                //* increment variable str_length
                if (strlen($current_next) > $str_length)
                    $str_length += 1;

                $auto_numeric = substr("$decimal_pattern{$current_next}", -$str_length);

                //? Check data maxvalue is not null and currentnext greather then maxvalue
                if ($max_value > 0 && $current_next > $max_value) {
                    $test[] = 'DocumentNo exceeds maximum value';
                } else {
                    $result[] = [
                        'sequence'    => $prefix . $auto_numeric,
                        'line_id'     => $line_id
                    ];

                    //* set value at the field currentnext
                    $current_next += $increment;

                    $entity->setCurrentNext($current_next);
                    $entity->setSequenceId($md_sequence_id);
                    $entity->save($entity);
                }
            } else {
                unset($val['prefix']);
                unset($val['line_id']);

                $seqNo = $sequenceNo->where($val)->first();

                if ($seqNo) {
                    $current_next = $seqNo->getCurrentNext();
                    $max_value = $seqNo->getMaxValue();

                    if (($seqNo->getGroupAssetId() == $md_groupasset_id) && ($seqNo->getCategoryId() == $md_category_id)) {
                        //* increment variable str_length
                        if (strlen($current_next) > $str_length)
                            $str_length += 1;

                        $auto_numeric = substr("$decimal_pattern{$current_next}", -$str_length);

                        //? Check data maxvalue is not null and currentnext greather then maxvalue
                        if ($max_value > 0 && $current_next > $max_value) {
                            $test[] = 'DocumentNo exceeds maximum value';
                        } else {
                            $result[] = [
                                'sequence'    => $prefix . $auto_numeric,
                                'line_id'     => $line_id
                            ];

                            //* set value at the field currentnext
                            $current_next += $increment;

                            $arrData = [
                                'currentnext'       => $current_next
                            ];

                            $arrWhere = [
                                'md_sequence_id'    => $md_sequence_id,
                                'md_groupasset_id'  => $md_groupasset_id,
                                'md_category_id'    => $md_category_id,
                                'calendaryearmonth' => $calendaryearmonth,
                            ];

                            $sequenceNo->create($arrData, $arrWhere);
                        }
                    }
                } else {
                    //* increment variable str_length
                    if (strlen($start_no) > $str_length)
                        $str_length += 1;

                    $auto_numeric = substr("$decimal_pattern{$start_no}", -$str_length);

                    $result[] = [
                        'sequence'    => $prefix . $auto_numeric,
                        'line_id'     => $line_id
                    ];

                    //* set value at the field currentnext
                    $start_no += $increment;

                    $arrData = [
                        'md_sequence_id'    => $md_sequence_id,
                        'md_groupasset_id'  => $md_groupasset_id,
                        'md_category_id'    => $md_category_id,
                        'calendaryearmonth' => $calendaryearmonth,
                        'currentnext'       => $start_no,
                        'maxvalue'          => $max_value
                    ];

                    $sequenceNo->create($arrData);
                }
            }
        endforeach;

        return $result;
    }
}
