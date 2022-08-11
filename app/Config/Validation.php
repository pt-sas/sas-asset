<?php

namespace Config;

use CodeIgniter\Validation\CreditCardRules;
use CodeIgniter\Validation\FileRules;
use CodeIgniter\Validation\FormatRules;
use CodeIgniter\Validation\Rules;

use App\Validation\PasswordRules;
use App\Validation\DuplicateRules;

class Validation
{
    //--------------------------------------------------------------------
    // Setup
    //--------------------------------------------------------------------

    /**
     * Stores the classes that contain the
     * rules that are available.
     *
     * @var string[]
     */
    public $ruleSets = [
        Rules::class,
        FormatRules::class,
        FileRules::class,
        CreditCardRules::class,
        PasswordRules::class,
        DuplicateRules::class
    ];

    /**
     * Specifies the views that are used to display the
     * errors.
     *
     * @var array<string, string>
     */
    public $templates = [
        'list'   => 'CodeIgniter\Validation\Views\list',
        'single' => 'CodeIgniter\Validation\Views\single',
    ];

    //--------------------------------------------------------------------
    // Rules
    //--------------------------------------------------------------------
    public $menu = [
        'name'              => [
            'rules'         =>    'required|is_unique[sys_menu.name,sys_menu_id,{id}]',
            'errors'        => [
                'is_unique' => 'This {field} already exists.'
            ]
        ],
        'url'               => [
            'rules'         =>    'required|valid_url'
        ],
        'icon'              => [
            'rules'         =>    'required'
        ],
        'sequence'          => [
            'rules'         =>    'required'
        ]
    ];

    public $submenu = [
        'name'              => [
            'rules'         =>    'required|is_unique[sys_submenu.name,sys_submenu_id,{id}]',
            'errors'        => [
                'is_unique' => 'This {field} already exists.'
            ]
        ],
        'url'               => [
            'rules'         =>    'required|valid_url'
        ],
        'sequence'          => [
            'rules'         =>    'required'
        ]
    ];

    public $role = [
        'name'              => [
            'rules'         =>    'required|is_unique[sys_role.name,sys_role_id,{id}]',
            'errors'        => [
                'is_unique' => 'This {field} already exists.'
            ]
        ]
    ];

    public $user = [
        'username'          => [
            'rules'         =>    'required|is_unique[sys_user.username,sys_user_id,{id}]',
            'errors'        => [
                'is_unique' => 'This {field} already exists.'
            ]
        ],
        'name'              => [
            'rules'         =>    'required|is_unique[sys_user.name,sys_user_id,{id}]',
            'errors'        => [
                'is_unique' => 'This {field} already exists.'
            ]
        ],
        'password'          => 'required',
        'role.*'            => [
            'label'         => 'role',
            'rules'         => 'required'
        ]
    ];

    public $login = [
        'username'    => 'required',
        'password'    => 'required'
    ];

    public $change_password = [
        'password'        => [
            'label'        => 'old password',
            'rules'        => 'required|match',
            'errors'    => [
                'match'    => 'The {field} does not match'
            ]
        ],
        'new_password'    => [
            'label'        => 'new password',
            'rules'        => 'required|min_length[5]'
        ],
        'conf_password'    => [
            'label'        => 'confirmation password',
            'rules'        => 'required|matches[new_password]'
        ]
    ];

    public $employee = [
        'value'             => [
            'label'            => 'Employee Code',
            'rules'         =>    'required|min_length[7]|max_length[7]|is_unique[md_employee.value,md_employee_id,{id}]',
            'errors'         => [
                'is_unique' => 'This {field} already exists.',
                'required'    => 'Please Insert the {field} first'
            ]
        ],
        'name'                 => [
            'label'            => 'Employee Name',
            'rules'            =>    'required|is_unique[md_employee.name,md_employee_id,{id}]',
            'errors'        => [
                'is_unique' => 'This {field} already exists.',
                'required'    => 'Please Insert the {field} first'
            ]
        ],
        'md_branch_id'         => [
            'label'            =>    'Branch',
            'rules'            =>    'required'
        ],
        'md_division_id'     => [
            'label'            =>    'Division',
            'rules'            =>    'required'
        ],
        'md_room_id'         => [
            'label'            =>    'Room',
            'rules'            =>    'required'
        ],
        'alert.*'           => [
            'label'         => 'Alert',
            'rules'         => 'required'
        ]
    ];

    public $branch = [
        'value'             => [
            'label'            => 'Branch Code',
            'rules'            =>    'required|min_length[7]|max_length[7]|is_unique[md_branch.value,md_branch_id,{id}]',
            'errors'        => [
                'is_unique' => 'This {field} already exists.',
                'required'    => 'Please Fill {field} first'
            ]
        ],
        'name'                 => [
            'label'            => 'Branch Name',
            'rules'            =>    'required|is_unique[md_branch.name,md_branch_id,{id}]',
            'errors'        => [
                'is_unique' => 'This {field} already exists.',
                'required'    => 'Please Insert the {field} first'
            ]
        ]
    ];

    public $division = [
        'value'             => [
            'label'            => 'Division Code',
            'rules'         =>    'required|min_length[7]|max_length[7]|is_unique[md_division.value,md_division_id,{id}]',
            'errors'         => [
                'is_unique' => 'This {field} already exists.',
                'required'    => 'Please Insert the {field} first'
            ]
        ],
        'name'                 => [
            'label'            => 'Division Name',
            'rules'            =>    'required|is_unique[md_division.name,md_division_id,{id}]',
            'errors'         => [
                'is_unique' => 'This {field} already exists.',
                'required'    => 'Please Insert the {field} first'
            ]
        ]
    ];

    public $room = [
        'value'             => [
            'label'            => 'Room Code',
            'rules'            =>  'required|min_length[7]|max_length[7]|is_unique[md_room.value,md_room_id,{id}]',
            'errors'        => [
                'required'    => 'Please Insert the {field} first'
            ]
        ],
        'name'                 => [
            'label'            =>    'Room Name',
            // 'rules'         =>    'required|is_unique[md_room.name,md_room_id,{id}]',
            'rules'         =>    'required',
            'errors'         => [
                // 'is_unique' => 'This {field} already exists.',
                'required'    => 'Please Insert the {field} first'
            ]
        ],
        'md_branch_id'         => [
            'label'            =>    'Branch',
            'rules'            =>    'required'
        ]
    ];

    public $supplier = [
        'value'             => [
            'label'            =>    'Supplier Code',
            'rules'         =>    'required|min_length[7]|max_length[7]|is_unique[md_supplier.value,md_supplier_id,{id}]',
            'errors'         => [
                'is_unique' => 'This {field} already exists.',
                'required'    => 'Please Insert the {field} first'
            ]
        ],
        'name'                 => [
            'label'            =>    'Supplier Name',
            'rules'         =>    'required|is_unique[md_supplier.name,md_supplier_id,{id}]',
            'errors'         => [
                'is_unique' => 'This {field} already exists.',
                'required'    => 'Please Insert the {field} first'
            ]
        ],
        'owner'             => [
            'label'            => 'Supplier Owner',
            'rules'         =>    'required',
            'errors'        => [
                'required'    => 'Please Insert the {field} first'
            ]
        ]
    ];

    public $brand = [
        'value'                => [
            'label'            => 'Brand Code',
            'rules'         =>    'required|min_length[7]|max_length[7]|is_unique[md_brand.value,md_brand_id,{id}]',
            'errors'         => [
                'is_unique' => 'This {field} already exists.'
            ]
        ],
        'name'                => [
            'label'            => 'Brand Name',
            'rules'         => 'required|is_unique[md_brand.name,md_brand_id,{id}]',
            'errors'         => [
                'is_unique' => 'This {field} already exists.'
            ]
        ]
    ];

    public $category = [
        'value'    => [
            'label'        =>    'Category Code',
            'rules'            =>    'required|min_length[7]|max_length[7]|is_unique[md_category.value,md_category_id,{id}]',
            'errors'     => [
                'is_unique' => 'This {field} already exists.',
                'required'    => 'Please Fill {field} first'
            ]
        ],
        'name'        => [
            'label'        =>    'Category Name',
            'rules'            =>    'required|is_unique[md_category.name,md_category_id,{id}]',
            'errors'     => [
                'is_unique' => 'This {field} already exists.',
                'required'    => 'Please Fill {field} first'
            ]
        ],
        'initialcode' => [
            'label'                 => 'Initial Code',
            'rules'                 => 'required|min_length[2]|max_length[2]',
            'errors'                => [
                'required' => 'Please Insert the {field}'
            ]
        ],
        'md_groupasset_id'            => [
            'label'            => 'Group Asset',
            'rules'            => 'required',
            'errors'        => [
                'required'    => 'Please Choose the {field} first'
            ]
        ]
    ];

    public $subcategory = [
        'value'             => [
            'label'            => 'Subcategory Code',
            'rules'            =>    'required|min_length[7]|max_length[7]|is_unique[md_subcategory.value,md_subcategory_id,{id}]',
            'errors'        => [
                'is_unique' => 'This {field} already exists.',
                'required'    => 'Please Fill {field} first'
            ]
        ],
        'name'                 => [
            'label'            => 'Subcategory Name',
            'rules'            => 'required|is_exist[md_subcategory.name,md_subcategory_id,{id},md_category_id,{md_category_id}]',
            'errors'        => [
                'is_exist' => 'This {field} already exists.',
                'required'    => 'Please Insert the {field} first'
            ]
        ],
        'md_category_id'            => [
            'label'            => 'Category',
            'rules'            =>    'required',
            'errors'        => [
                'required'    => 'Please Choose the {field} first'
            ]
        ]
    ];

    public $type = [
        'value'                 => [
            'label'             => 'Type Code',
            'rules'             => 'required|min_length[7]|max_length[7]|is_unique[md_type.value,md_type_id,{id}]',
            'errors'            => [
                'is_unique'     => 'This {field} already exists.',
                'required'      => 'Please Fill {field} first'
            ]
        ],
        'name'                  => [
            'label'             => 'Type Name',
            'rules'             => 'required|is_exist[md_type.name,md_type_id,{id},md_subcategory_id,{md_subcategory_id}]',
            'errors'            => [
                'is_exist'      => 'This {field} already exists.',
                'required'      => 'Please Insert the {field} first'
            ]
            // ],
            // 'md_subcategory_id'     => [
            //     'label'             => 'Sub-Category',
            //     'rules'             => 'required',
            //     'errors'            => [
            //         'required'      => 'Please Choose the {field} first'
            //     ]
        ]
    ];

    public $product = [
        'value'             => [
            'label'            => 'Product Code',
            'rules'            =>    'required|min_length[7]|max_length[7]|is_unique[md_product.value,md_product_id,{id}]',
            'errors'        => [
                'is_unique' => 'This {field} already exists.',
                'required'    => 'Please Insert {field} first'
            ]
        ],
        'name'                 => [
            'label'            => 'Product Name',
            'rules'            => 'is_unique[md_product.name,md_product_id,{id}]',
            'errors'        => [
                'is_unique' => 'The {field} already exists.'
            ]
        ],
        'md_brand_id'        => [
            'label'            => 'Brand',
            'rules'            =>    'required',
            'errors'        => [
                'required'    => 'Please Choose the {field} first'
            ]
        ],
        'md_category_id'    => [
            'label'            => 'Category',
            'rules'            =>    'required',
            'errors'        => [
                'required'    => 'Please Choose the {field} first'
            ]
            // ],
            // 'md_subcategory_id'    => [
            //     'label'            => 'Sub-Category',
            //     'rules'            =>    'required',
            //     'errors'        => [
            //         'required'    => 'Please Choose the {field} first'
            //     ]
        ],
        // 'md_type_id'        => [
        //     'label'            => 'Type',
        //     'rules'            =>    'required',
        //     'errors'        => [
        //         'required'    => 'Please Choose the {field} first'
        //     ]
        // ]
    ];

    public $status = [
        'value'             => [
            'label'         => 'Status Code',
            'rules'         => 'required|min_length[7]|max_length[7]|is_unique[md_status.value,md_status_id,{id}]',
            'errors'        => [
                'is_unique' => 'This {field} already exists.',
                'required'  => 'Please Insert the {field} first'
            ]
        ],
        'name'              => [
            'label'         => 'Status Name',
            'rules'         => 'required|is_unique[md_status.name,md_status_id,{id}]',
            'errors'        => [
                'required'  => 'Please Insert the {field} first.',
                'is_unique' => 'The {field} already exists.'
            ]
        ],
        'menu_id'           => [
            'label'         => 'Menu',
            'rules'         => 'required',
            'errors'        => [
                'required'  => 'Please Insert the {field} first.'
            ]
        ]
    ];

    public $service = [
        'documentno'        => [
            'label'         => 'Document No',
            'rules'         => 'required|is_unique[trx_service.documentno,trx_service_id,{id}]',
            'errors'        => [
                'is_unique' => 'This {field} already exists.',
                'required'  => 'Please Insert the {field} first.'
            ]
        ],
        'md_supplier_id'    => [
            'label'         => 'Supplier',
            'rules'         => 'required',
            'errors'        => [
                'required'  => 'Please Insert the {field} first.'
            ]
        ],
        'servicedate'       => [
            'label'         => 'Date Service',
            'rules'         => 'required',
            'errors'        => [
                'required'  => 'Please Insert the {field} first.'
            ]
        ],
        'line'              => [
            'label'         => 'Service Detail',
            'rules'         => 'required',
            'errors'        => [
                'required'  => 'Please Insert the {field} first.'
            ]
        ],
        'detail.table.*.assetcode'  => [
            'label'                 => 'Asset Code',
            'rules'                 => 'required|is_exists',
            'errors'                => [
                'required' => 'Please Insert the {field} Line',
                'is_exists' => 'The {field} duplicate value'
            ]
        ],
        'detail.table.*.product_id'  => [
            'label'             => 'Product',
            'rules'             => 'required',
            'errors'            => [
                'required'      => 'Please Insert the {field} Line'
            ]
        ],
        'detail.table.*.status_id'  => [
            'label'             => 'Status',
            'rules'             => 'required',
            'errors'            => [
                'required'      => 'Please Choose the {field} Line'
            ]
            // ],
            // 'detail.table.*.unitprice'  => [
            //     'label'             => 'Unit Price',
            //     'rules'             => 'required',
            //     'errors'            => [
            //         'required'      => 'Please Insert the {field} Line'
            //     ]
        ],
    ];

    public $quotation = [
        'documentno'            => [
            'label'             => 'Document No',
            'rules'             => 'required|min_length[10]|max_length[10]|is_unique[trx_quotation.documentno,trx_quotation_id,{id}]',
            'errors'            => [
                'is_unique'     => 'This {field} already exists.',
                'required'      => 'Please Insert the {field} first'
            ]
        ],
        'quotationdate'         => [
            'label'             => 'Date Quotation',
            'rules'             => 'required',
            'errors'            => [
                'required'      => 'Please Insert the {field} first.'
            ]
        ],
        'md_status_id'          => [
            'label'             => 'Status',
            'rules'             => 'required',
            'errors'            => [
                'required'      => 'Please Choose the {field} first.'
            ]
        ],
        'md_supplier_id'        => [
            'label'             => 'Supplier',
            'rules'             => 'required',
            'errors'            => [
                'required'      => 'Please Choose the {field} first.'
            ]
        ],
        'line'                  => [
            'label'             => 'Quotation Detail',
            'rules'             => 'required',
            'errors'            => [
                'required'      => 'Please Insert the {field} first.'
            ]
        ],
        'detail.table.*.product_id'  => [
            'label'             => 'Product',
            'rules'             => 'required',
            'errors'            => [
                'required'      => 'Please Choose the {field} Line'
            ]
        ],
        'detail.table.*.qtyentered'  => [
            'label'             => 'Qty',
            'rules'             => 'required|is_natural_no_zero',
            'errors'            => [
                'required'      => 'Please Insert the {field} Line',
                'is_natural_no_zero'    => 'The {field} field must only contain digits and must be greater than zero Line'
            ]
        ],
        'detail.table.*.unitprice'  => [
            'label'             => 'Unit Price',
            'rules'             => 'required',
            'errors'            => [
                'required'      => 'Please Insert the {field} Line'
            ]
        ],
        'detail.table.*.lineamt'  => [
            'label'             => 'Line Amount',
            'rules'             => 'required',
            'errors'            => [
                'required'      => 'Please Insert the {field} Line'
            ]
        ],
        'detail.table.*.employee_id'  => [
            'label'             => 'Employee',
            'rules'             => 'required',
            'errors'            => [
                'required'      => 'Please Choose the {field} Line'
            ]
        ]
    ];

    public $receipt = [
        'documentno'                => [
            'label'                 => 'Document No',
            'rules'                 => 'required|min_length[10]|max_length[10]|is_unique[trx_receipt.documentno,trx_receipt_id,{id}]',
            'errors'                => [
                'is_unique' => 'This {field} already exists.',
                'required'  => 'Please Insert the {field} first'
            ]
        ],
        'receiptdate'               => [
            'label'                 => 'Receipt Date',
            'rules'                 => 'required',
            'errors'                => [
                'required'  => 'Please Insert the {field} first.'
            ]
        ],
        'md_status_id'              => [
            'label'                 => 'Status',
            'rules'                 => 'required',
            'errors'                => [
                'required'  => 'Please Choose the {field} first.'
            ]
        ],
        'expenseno'                 => [
            'label'                 => 'Expense No',
            'rules'                 => 'required',
            'errors'                => [
                'required'  => 'Please Insert the {field} first.'
            ]
        ],
        'invoiceno'                 => [
            'label'                 => 'Invoice No',
            'rules'                 => 'required',
            'errors'                => [
                'required'  => 'Please Insert the {field} first.'
            ]
        ],
        'trx_quotation_id'          => [
            'label'                 => 'Quotation',
            'rules'                 => 'required|is_unique[trx_receipt.trx_quotation_id,trx_receipt_id,{id}]',
            'errors'                => [
                'required'  => 'Please Choose the {field} first.',
                'is_unique' => 'This {field} already exists.'
            ]
        ],
        'md_supplier_id'            => [
            'label'                 => 'Suplier',
            'rules'                 => 'required',
            'errors'                => [
                'required'  => 'Please Choose the {field} first.'
            ]
        ],
        'line'                      => [
            'label'                 => 'Receipt Detail',
            'rules'                 => 'required',
            'errors'                => [
                'required' => 'Please Insert the {field} first.'
            ]
        ],
        // 'detail.table.*.assetcode'  => [
        //     'label'                 => 'Asset Code',
        //     'rules'                 => 'required|is_exists|is_unique[trx_inventory.assetcode]',
        //     'errors'                => [
        //         'required' => 'Please Insert the {field} Line',
        //         'is_exists' => 'The {field} duplicate value',
        //         'is_unique' => '{value}|The {field} ({value}) already exists'
        //     ]
        // ],
        'detail.table.*.unitprice' => [
            'label'                 => 'Unitprice',
            'rules'                 => 'required',
            'errors'                => [
                'required' => 'Please Insert the {field} Line'
            ]
        ],
        'detail.table.*.employee_id' => [
            'label'                 => 'Employee',
            'rules'                 => 'required',
            'errors'                => [
                'required' => 'Please Choose the {field} Line'
            ]
        ],
        'detail.table.*.branch_id'  => [
            'label'                 => 'Branch',
            'rules'                 => 'required',
            'errors'                => [
                'required' => 'Please Choose the {field} Line'
            ]
        ],
        'detail.table.*.division_id' => [
            'label'                 => 'Division',
            'rules'                 => 'required',
            'errors'                => [
                'required' => 'Please Choose the {field} Line'
            ]
        ],
        'detail.table.*.room_id'  => [
            'label'                 => 'Room',
            'rules'                 => 'required',
            'errors'                => [
                'required' => 'Please Choose the {field} Line'
            ]
        ]
    ];

    public $movement = [
        'documentno'                => [
            'label'                 => 'Document No',
            'rules'                 => 'required|min_length[10]|max_length[10]|is_unique[trx_movement.documentno,trx_movement_id,{id}]',
            'errors'                => [
                'is_unique' => 'This {field} already exists.',
                'required'  => 'Please Insert the {field} first'
            ]
        ],
        'movementdate'              => [
            'label'                 => 'Movement Date',
            'rules'                 => 'required',
            'errors'                => [
                'required'  => 'Please Insert the {field} first.'
            ]
        ],
        'detail.table.*.assetcode'  => [
            'label'                 => 'Asset Code',
            'rules'                 => 'required|is_exists',
            'errors'                => [
                'required' => 'Please Insert the {field} Line',
                'is_exists' => 'The {field} duplicate value'
            ]
        ],
        'detail.table.*.status_id' => [
            'label'                 => 'Status',
            'rules'                 => 'required',
            'errors'                => [
                'required' => 'Please Choose the {field} Line'
            ]
        ],
        'detail.table.*.employee_to' => [
            'label'                 => 'Employee To',
            'rules'                 => 'required',
            'errors'                => [
                'required' => 'Please Choose the {field} Line'
            ]
        ],
        'detail.table.*.room_to' => [
            'label'                 => 'Room To',
            'rules'                 => 'required',
            'errors'                => [
                'required' => 'Please Choose the {field} Line'
            ]
        ]
    ];

    public $inventory = [
        'assetcode'  => [
            'label'                 => 'Asset Code',
            'rules'                 => 'required|is_unique[trx_inventory.assetcode,trx_inventory_id,{id}]',
            'errors'                => [
                'required' => 'Please Insert the {field}',
                'is_unique' => 'This {field} already exists.'
            ]
        ],
        'md_product_id' => [
            'label'                 => 'Product',
            'rules'                 => 'required',
            'errors'                => [
                'required' => 'Please Choose the {field}'
            ]
        ],
        'md_branch_id' => [
            'label'                 => 'Branch',
            'rules'                 => 'required',
            'errors'                => [
                'required' => 'Please Choose the {field}'
            ]
        ],
        'md_room_id' => [
            'label'                 => 'Room',
            'rules'                 => 'required',
            'errors'                => [
                'required' => 'Please Choose the {field}'
            ]
        ],
        'md_employee_id' => [
            'label'                 => 'Employee',
            'rules'                 => 'required',
            'errors'                => [
                'required' => 'Please Choose the {field}'
            ]
        ]
    ];

    public $groupasset = [
        'value'  => [
            'label'                 => 'Group Asset Code',
            'rules'                 => 'required|is_unique[md_groupasset.value,md_groupasset_id,{id}]',
            'errors'                => [
                'required' => 'Please Insert the {field}',
                'is_unique' => 'This {field} already exists.'
            ]
        ],
        'name' => [
            'label'                 => 'Name',
            'rules'                 => 'required|is_unique[md_groupasset.name,md_groupasset_id,{id}]',
            'errors'                => [
                'required' => 'Please Insert the {field}',
                'is_unique' => 'This {field} already exists.'
            ]
        ],
        'initialcode' => [
            'label'                 => 'Initial Code',
            'rules'                 => 'required|min_length[2]|max_length[2]',
            'errors'                => [
                'required' => 'Please Insert the {field}'
            ]
        ],
        'usefullife' => [
            'label'                 => 'Useful Life',
            'rules'                 => 'required|is_natural_no_zero',
            'errors'                => [
                'required' => 'Please Insert the {field}'
            ]
        ],
        'md_sequence_id' => [
            'label'                 => 'Document Sequence',
            'rules'                 => 'required',
            'errors'                => [
                'required' => 'Please Choose the {field}'
            ]
        ]
    ];

    public $sequence = [
        'incrementno'               => [
            'label'                 => 'Increment',
            'rules'                 => 'required_with[isautosequence]',
            'errors'                => [
                'required_with'     => 'Please Insert the {field}'
            ]
        ],
        'maxvalue'                  => [
            'label'                 => 'Max Value',
            'rules'                 => 'required',
            'errors'                => [
                'required'          => 'Please Insert the {field}'
            ]
        ],
        'currentnext'               => [
            'label'                 => 'Current Next',
            'rules'                 => 'required_with[isautosequence]',
            'errors'                => [
                'required_with'     => 'Please Insert the {field}'
            ]
        ],
        'startno'                   => [
            'label'                 => 'Start No',
            'rules'                 => 'required_with[isautosequence]',
            'errors'                => [
                'required_with'     => 'Please Insert the {field}'
            ]
        ],
        'datecolumn'                => [
            'label'                 => 'Date Column',
            'rules'                 => 'required_with[startnewyear]',
            'errors'                => [
                'required_with'     => 'Please Insert the {field}'
            ]
        ],
        'name'                      => [
            'label'                 => 'Name',
            'rules'                 => 'required|is_unique[md_sequence.name,md_sequence_id,{id}]',
            'errors'                => [
                'required'          => 'Please Insert the {field}',
                'is_unique'         => 'This {field} already exists.'
            ]
        ],
        'isgassetlevelsequence'     => [
            'label'                 => 'Group Asset Level',
            'rules'                 => 'required_with[iscategorylevelsequence]',
            'errors'                => [
                'required_with'     => 'Please Checked the {field}'
            ]
        ]
    ];
}
