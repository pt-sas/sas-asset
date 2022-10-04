/**
 * Proses for execute form master data dynamic element HTML
 *
 * @author Oki Permana
 * @version 1.0
 */
const ADMIN = '/backend/';

let ORI_URL = window.location.origin,
    CURRENT_URL = window.location.href,
    LAST_URL = CURRENT_URL.substr(CURRENT_URL.lastIndexOf('/') + 1), //the last url
    ADMIN_URL = ORI_URL + ADMIN,
    SITE_URL = ADMIN_URL + LAST_URL;

let ID,
    _table,
    _tableLine,
    setSave,
    ul,
    formTable,
    _tableInfo;

// Data array from option
let option = [];

// Data field array is readonly/disabled default
let fieldReadOnly = [];
// Data field array is checked default
let fieldChecked = [];

// Method default controller
const SHOWALL = '/showAll',
    CREATE = '/create',
    SHOW = '/show/',
    EDIT = '/edit',
    DELETE = '/destroy/',
    EXPORT = '/export',
    IMPORT = '/import',
    TABLE_LINE = '/tableLine',
    DELETE_LINE = '/destroyLine/';

// view page class on div
let cardMain = $('.card-main'),
    cardForm = $('.card-form'),
    cardBtn = $('.card-button'),
    cardTitle = $('.card-title');

// Modal
const modalForm = $('.modal_form');

const modalDialog = $('.modal-dialog'),
    modalTitle = $('.modal-title'),
    modalBody = $('.modal-body');

/**
 * Table Display
 */
_table = $('.tb_display').DataTable({
    // 'processing': true,
    'serverSide': true,
    'ajax': {
        'url': SITE_URL + SHOWALL,
        'type': 'POST',
        'data': function (d, setting) {
            const container = $(setting.nTable).closest('.container');
            const filter_page = container.find('.filter_page');
            const form = filter_page.find('form');
            const disabled = form.find('[disabled]');

            //! Remove attribute disabled field
            disabled.removeAttr('disabled');

            //* Serialize form array
            formTable = form.serializeArray();

            //! Set attribute disabled field
            disabled.prop('disabled', true);

            return $.extend({}, d, {
                form: formTable
            });
        }
    },
    'columnDefs': [{
            // 'targets': '_all',
            'targets': [1, -1],
            'orderable': false,
            'width': 2
        },
        {
            'targets': 0,
            'visible': false //hide column
        }
    ],
    'lengthMenu': [
        [10, 25, 50, 100, -1],
        [10, 25, 50, 100, 'All']
    ],
    'order': [],
    'autoWidth': false,
    'scrollX': checkScrollX(),
    'scrollY': checkScrollY(),
    'scrollCollapse': checkScrollX(),
    'fixedColumns': checkFixedColumns(),
});

/**
 * Table Display Line
 */
_tableLine = $('.tb_displayline').DataTable({
    'drawCallback': function (settings) {
        $(this).find('.number').on('keypress keyup blur', function (evt) {
            $(this).val($(this).val().replace(/[^\d].+/, ""));
            if ((evt.which < 48 || evt.which > 57)) {
                evt.preventDefault();
            }
        });
        $(this).find('.select2').select2({
            placeholder: 'Select an option',
            theme: "bootstrap",
            allowClear: true
        });
        $(this).find('.rupiah').autoNumeric('init', {
            aSep: '.',
            aDec: ',',
            mDec: '0'
        });
    },
    'initComplete': function (settings, json) {
        $('.tb_displayline').wrap("<div style='overflow:auto; width:100%; position:relative;'></div>");
    },
    'lengthChange': false,
    'pageLength': 5,
    'searching': false,
    'ordering': false,
    'autoWidth': false
});

/**
 * Table Tree in Role
 */
$('.tb_tree').treeFy({
    initState: 'expanded',
    treeColumn: 0,
    collapseAnimateCallback: function (row) {
        row.fadeOut();
    },
    expandAnimateCallback: function (row) {
        row.fadeIn();
    }
});

/**
 * Table Info on the modal
 */
_tableInfo = $('.table_info').DataTable({
    'processing': true,
    'drawCallback': function (settings) {
        $(this).find('.number').on('keypress keyup blur', function (evt) {
            $(this).val($(this).val().replace(/[^\d].+/, ""));
            if ((evt.which < 48 || evt.which > 57)) {
                evt.preventDefault();
            }
        });
        $(this).find('.select2').select2({
            placeholder: 'Select an option',
            theme: "bootstrap",
            allowClear: true
        });
        $(this).find('.rupiah').autoNumeric('init', {
            aSep: '.',
            aDec: ',',
            mDec: '0'
        });
    },
    'columnDefs': [{
            'targets': [0, 1],
            'orderable': false,
            'width': 2
        },
        {
            'targets': 0,
            'visible': false //hide column
        }
    ],
    'displayLength': -1,
    'lengthChange': false,
    'searching': false,
    'paging': false,
    'autoWidth': false,
    'scrollX': true,
    'scrollY': '350px',
    'scrollCollapse': true
});

/**
 * 
 * @returns check fixed column datatable
 */
function checkFixedColumns() {
    if ($('.tb_display thead th').length > 10) {
        return {
            'rightColumns': 1,
            'leftColumns': 0
        }
    } else if ($('.tb_display thead th').length > 15) {
        return {
            'rightColumns': 1,
            'leftColumns': 3
        }
    }
}

/**
 * Check length head table for scrollX
 * @returns 
 */
function checkScrollX() {
    return $('.tb_display thead th').length > 7 ? true : false;
}

/**
 * Check length head table for set scrollY
 * @returns 
 */
function checkScrollY() {
    return $('.tb_display thead th').length > 10 ? '400px' : '';
}

function reloadTable() {
    _table.ajax.reload(null, false);
}

/**
 * Button Save Form Data
 * 
 */
$('.save_form').click(function (evt) {
    const parent = $(evt.target).closest('.row');
    cardForm = parent.find('.card-form');
    const form = cardForm.find('form');
    const container = $(evt.target).closest('.container');

    let field;

    let _this = $(this);
    let oriElement = _this.html();
    let oriTitle = container.find('.page-title').text();

    let action = 'create';
    let checkAccess = isAccess(action, LAST_URL);

    let formData = new FormData();
    let url = SITE_URL + CREATE;

    if (checkAccess[0].success && checkAccess[0].message == 'Y') {
        //* Populate field form header
        $.each(form, function () {
            const formHeader = $(this).find('.row')[0];
            field = $(formHeader).find('input, select, textarea').not('.line');
        });

        //? Remove attribute disabled when submit data
        for (let i = 0; i < field.length; i++) {
            if (field[i].name !== '') {
                form.find('input:checkbox[name=' + field[i].name + '], select[name=' + field[i].name + ']').not('.line').removeAttr('disabled');
            }
        }

        //* Form Header
        for (let i = 0; i < field.length; i++) {
            if (field[i].name !== '') {
                let className = field[i].className.split(/\s+/);

                //* Set field and value to formData 
                if (field[i].type == 'text' || field[i].type == 'textarea' || field[i].type == 'select-one')
                    formData.append(field[i].name, field[i].value);

                //* Field type input radio
                if (field[i].type == 'radio') {
                    if (field[i].checked) {
                        formData.append(field[i].name, field[i].value);
                    }
                }

                //* Field type input file and containing class control-upload-image
                if (field[i].type == 'file' && className.includes('control-upload-image')) {
                    //? Check condition upload add new image or not upload
                    if (field[i].files.length > 0) {
                        formData.append(field[i].name, field[i].files[0]);
                    } else {
                        let source = form.find('.img-result').attr('src');
                        let imgSrc;

                        if (source !== '') {
                            imgSrc = source.substr(source.lastIndexOf('/') + 1);
                        } else {
                            imgSrc = source;
                        }

                        formData.append(field[i].name, imgSrc);
                    }
                }

                //* Field type textarea class summernote isEmpty to set value null
                if ((form.find('textarea.summernote[name=' + field[i].name + ']').length > 0 ||
                        form.find('textarea.summernote-product[name=' + field[i].name + ']').length > 0) &&
                    $('[name =' + field[i].name + ']').summernote('isEmpty')) {
                    formData.append(field[i].name, '');
                }

                //* Field type Multiple select
                if (field[i].type === 'select-multiple') {
                    formData.append(field[i].name, $('[name = ' + field[i].name + ']').val())
                }

                //* Field type input checkbox
                if (field[i].type == 'checkbox') {
                    if (field[i].checked) {
                        formData.append(field[i].name, 'Y');
                    } else {
                        formData.append(field[i].name, 'N');
                    }
                }

                //* Field containing class datepicker 
                if (className.includes('datepicker')) {
                    let date = field[i].value;
                    let time = "00:00:00";

                    let timeAndDate = moment(date + ' ' + time);
                    formData.append(field[i].name, timeAndDate._i);
                }

                //* Field containing class rupiah
                if (className.includes('rupiah')) {
                    formData.append(field[i].name, replaceRupiah(field[i].value))
                }
            }
        }

        //? Check in form exists Table role
        if (form.find('table.tb_tree').length > 0) {
            const table = form.find('table.tb_tree');
            const input = table.find('td input:checkbox');

            let isView = [];
            let isCreate = [];
            let isUpdate = [];
            let isDelete = [];
            let accessID = [];

            $.each(input, function () {
                let row_index = $(this).parent().parent().parent().parent().index();
                let field = $(this).attr('name');
                let menu_id = $(this).val();
                let menu = $(this).attr('data-menu');

                let access_id = typeof $(this).attr('id') !== 'undefined' ? $(this).attr('id') : 0;

                let value;

                if ($(this).is(':checked')) {
                    value = 'Y';
                } else {
                    value = 'N';
                }

                if (field == 'isview') {
                    isView.push({
                        row: row_index,
                        view: value,
                        menu_id: menu_id,
                        menu: menu
                    });
                } else if (field == 'iscreate') {
                    isCreate.push({
                        row: row_index,
                        create: value,
                        menu_id: menu_id,
                        menu: menu
                    });
                } else if (field == 'isupdate') {
                    isUpdate.push({
                        row: row_index,
                        update: value,
                        menu_id: menu_id,
                        menu: menu
                    });
                } else if (field == 'isdelete') {
                    isDelete.push({
                        row: row_index,
                        delete: value,
                        menu_id: menu_id,
                        menu: menu
                    });
                }

                if (setSave !== 'add')
                    accessID.push({
                        row: row_index,
                        access_id
                    });
            });

            accessID = removeDuplicates(accessID, item => item.row);

            let arrRole = mergeArrayObjects(isView, isCreate, isUpdate, isDelete, accessID);

            formData.append('roles', JSON.stringify(arrRole));
        }

        //? Check in form exists Table Line
        if (form.find('table.tb_displayline').length > 0) {
            const rows = _tableLine.rows().nodes().to$();

            let output = [];
            $.each(rows, function (i) {
                let tag = $(this).find('input, select, button');

                let row = {};
                $.each(tag, function () {
                    let className = this.className.split(/\s+/);
                    let name = $(this).attr('name');
                    let value = this.value;
                    let id = $(this).attr('id');

                    //* Field containing class rupiah
                    if (className.includes('rupiah'))
                        value = replaceRupiah(this.value);

                    if ($(this).attr('type') !== 'button') {
                        if ($(this).attr('type') !== 'checkbox') {
                            row[name] = value;
                        } else {
                            row[name] = $(this).is(':checked') ? 'Y' : 'N';
                        }

                    } else {
                        if (id !== '')
                            row[name] = id;
                        else
                            row[name] = '';

                        if (className.includes('reference-key'))
                            row[name] = value; // Get value reference key
                    }
                });

                output[i] = row;
            });

            formData.append('table', JSON.stringify(output));
        }

        //* Set primary key on the property "id" 
        if (setSave === 'update')
            formData.append('id', ID);

        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            // async: false,
            cache: false,
            dataType: 'JSON',
            beforeSend: function () {
                $(_this).html('<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>').prop('disabled', true);
                $('.x_form').prop('disabled', true);
                $('.close_form').prop('disabled', true);
                loadingForm(form.prop('id'), 'facebook');
            },
            complete: function () {
                $(_this).html(oriElement).prop('disabled', false);
                $('.x_form').removeAttr('disabled');
                $('.close_form').removeAttr('disabled');
                hideLoadingForm(form.prop('id'));
            },
            success: function (result) {
                if (result[0].success) {
                    Toast.fire({
                        type: 'success',
                        title: result[0].message
                    });

                    clearForm(evt);

                    if (!cardForm.prop('classList').contains('modal')) {
                        const parent = cardForm.closest('.container');
                        const cardBody = parent.find('.card-body');

                        $.each(cardBody, function (idx, elem) {
                            let className = elem.className.split(/\s+/);

                            if (className.includes('card-main')) {
                                $(this).css('display', 'block');

                                // Remove breadcrumb list
                                let li = ul.find('li');
                                $.each(li, function (idx, elem) {
                                    if (idx > 2)
                                        elem.remove();
                                });

                                if (parent.find('div.filter_page').length > 0) {
                                    parent.find('div.filter_page').css('display', 'block');
                                }
                            }

                            if (className.includes('card-form')) {
                                const cardHeader = parent.find('.card-header');
                                cardHeader.find('button').show();
                                $(this).css('display', 'none');
                            }
                        });

                        cardBtn.css('display', 'none');

                        const cardHeader = parent.find('.card-header');
                        const btnList = cardHeader.find('button').prop('classList');

                        if (btnList.contains('new_form'))
                            cardHeader.find('button').css('display', 'block');
                    } else {
                        modalForm.modal('hide');
                    }

                    cardTitle.html(oriTitle);

                    reloadTable();

                } else if (result[0].error) {
                    errorForm(form, result);
                    $('html, body').animate({
                        scrollTop: $('.container').offset().top
                    }, 1500);

                } else {
                    Toast.fire({
                        type: 'error',
                        title: result[0].message
                    });
                }
            },
            error: function (jqXHR, exception) {
                showError(jqXHR, exception);
            }
        });

        // logic after insert / update data to set attribute based on field isactive condition
        for (let i = 0; i < field.length; i++) {
            let fieldActive = form.find('input.active');

            // Check element name is not null and any field checkbox active
            if (field[i].name !== '' && fieldActive.length > 0) {
                let className = field[i].className.split(/\s+/);

                if (form.find('input.active').is(':checked')) {
                    form.find('input:checkbox[name=' + field[i].name + '], select[name=' + field[i].name + ']').removeAttr('disabled');
                } else {
                    if (!className.includes('active')) {
                        form.find('input:checkbox[name=' + field[i].name + '], select[name=' + field[i].name + ']').not('.line').prop('disabled', true);
                    }
                }
            } else {
                // Set attribute disabled based on default field
                if (fieldReadOnly.includes(field[i].name))
                    form.find('input:checkbox[name=' + field[i].name + '], select[name=' + field[i].name + ']').not('.line').prop('disabled', true);
            }
        }
    } else if (checkAccess[0].success && checkAccess[0].message == 'N') {
        Toast.fire({
            type: 'error',
            title: "You are role don't have permission, please reload !!"
        });
    } else {
        Toast.fire({
            type: 'error',
            title: checkAccess[0].message
        });
    }
});

/**
 * Button edit data
 * Show data on the form
 */
_table.on('click', '.edit', function (evt) {
    const parent = $(evt.target).closest('.container');
    const cardBody = parent.find('.card-body');
    const cardForm = parent.find('.card-form');
    const form = cardForm.find('form');
    const row = _table.row(this).data();
    let card = parent.find('.card');

    ID = $(this).attr('id');

    let _this = $(this);
    let oriElement = _this.html();

    let formList, status;
    let arrMultiSelect = [];
    let action = 'update';

    let checkAccess = isAccess(action, LAST_URL);

    if ($(this).attr('data-status'))
        status = $(this).attr('data-status');

    if (checkAccess[0].success && checkAccess[0].message == 'Y') {
        $(_this).html('<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>').prop('disabled', true);

        card.length && (card.addClass("is-loading"),
            setTimeout(function () {
                $.each(cardBody, function (idx, elem) {
                    let className = elem.className.split(/\s+/);

                    if (cardBody.length > 1) {
                        if (className.includes('card-main')) {
                            $(this).css('display', 'none');

                            const pageHeader = parent.find('.page-header');
                            ul = pageHeader.find('ul.breadcrumbs');

                            // Append list separator and text create
                            ul.find('li.nav-item > a').attr('href', CURRENT_URL);

                            let list = '<li class="separator">' +
                                '<i class="flaticon-right-arrow"></i>' +
                                '</li>';

                            if ((typeof status === 'undefined' || status === '') || status === 'DR')
                                list += '<li class="nav-item">' +
                                '<a class="text-primary font-weight-bold">Update</a>' +
                                '</li>';
                            else
                                list += '<li class="nav-item">' +
                                '<a class="text-primary font-weight-bold">Detail</a>' +
                                '</li>';

                            ul.append(list);

                            if (parent.find('div.filter_page').length > 0)
                                parent.find('div.filter_page').css('display', 'none');
                        }

                        if (className.includes('card-form')) {
                            const card = cardForm.closest('.card');
                            const cardHeader = card.find('.card-header');

                            cardHeader.find('button', 'a').css('display', 'none');
                            $(this).css('display', 'block');
                            cardBtn.css('display', 'block');
                            formList = $(this).prop('classList');
                        }
                    } else {
                        openModalForm();
                        Scrollmodal();
                        form = modalForm.find('form');
                        formList = cardForm.prop('classList');
                    }
                });

                let url = SITE_URL + SHOW + ID;

                setSave = ((typeof status === 'undefined' || status === '') || status === 'DR') ? 'update' : 'detail';

                $.ajax({
                    url: url,
                    type: 'GET',
                    // async: false,
                    cache: false,
                    dataType: 'JSON',
                    beforeSend: function () {
                        $('.save_form').attr('disabled', true);
                        $('.x_form').attr('disabled', true);
                        $('.close_form').attr('disabled', true);
                        loadingForm(form.prop('id'), 'facebook');
                    },
                    complete: function () {
                        if (setSave !== 'detail')
                            $('.save_form').removeAttr('disabled');

                        $('.x_form').removeAttr('disabled');
                        $('.close_form').removeAttr('disabled');
                        hideLoadingForm(form.prop('id'));
                    },
                    success: function (result) {
                        if (result[0].success) {
                            let arrMsg = result[0].message;

                            // Show datatable line
                            if (arrMsg.line) {
                                let arrLine = arrMsg.line;

                                if (form.find('table.tb_displayline').length > 0) {
                                    let line = JSON.parse(arrLine);

                                    $.each(line, function (idx, elem) {
                                        _tableLine.row.add(elem).draw(false);
                                    });

                                    let btnAction = _tableLine.rows().nodes().to$().find('button');

                                    const field = _tableLine.rows().nodes().to$().find('input, select');

                                    /**
                                     * Logic for set detail when status not draft
                                     */
                                    if (setSave === 'detail' && status !== 'DR') {
                                        readonly(form, true);

                                        // Button add row table line
                                        $('.add_row, .create_line').css('display', 'none');

                                        btnAction.css('display', 'none');

                                        $.each(field, function (index, item) {
                                            const tr = $(this).closest('tr');

                                            if (item.type !== 'text') {
                                                tr.find('input:checkbox[name=' + item.name + '], select[name=' + item.name + '], input:radio[name=' + item.name + ']').prop('disabled', true);
                                            } else {
                                                tr.find('input:text[name=' + item.name + '], textarea[name=' + item.name + ']').prop('readonly', true);
                                            }
                                        });
                                    } else {
                                        // Button add row table line
                                        $('.add_row, .create_line').css('display', 'block');

                                        btnAction.css('display', 'block');
                                    }
                                }

                                if (form.find('table.tb_tree').length > 0) {
                                    for (let i = 0; i < arrLine.length; i++) {
                                        const table = form.find('table.tb_tree');
                                        const input = table.find('td input:checkbox');

                                        $.each(input, function (idx, elem) {
                                            // Menu parent
                                            if ($(elem).attr('data-menu') === 'parent') {

                                                if (arrLine[i].sys_menu_id == $(elem).val() && arrLine[i].sys_submenu_id == 0) {
                                                    if ((arrLine[i].isview == 'Y' && $(elem).attr('name') === 'isview') ||
                                                        (arrLine[i].iscreate == 'Y' && $(elem).attr('name') === 'iscreate') ||
                                                        (arrLine[i].isupdate == 'Y' && $(elem).attr('name') === 'isupdate') ||
                                                        (arrLine[i].isdelete == 'Y' && $(elem).attr('name') === 'isdelete')) {
                                                        $(elem).prop('checked', true);
                                                    } else {
                                                        $(elem).prop('checked', false);
                                                    }

                                                    // Set attribute id element to value sys_access_menu_id
                                                    $(elem).attr('id', arrLine[i].sys_access_menu_id);
                                                }

                                            } else {
                                                if (arrLine[i].sys_submenu_id === $(elem).val()) {
                                                    if ((arrLine[i].isview == 'Y' && $(elem).attr('name') === 'isview') ||
                                                        (arrLine[i].iscreate == 'Y' && $(elem).attr('name') === 'iscreate') ||
                                                        (arrLine[i].isupdate == 'Y' && $(elem).attr('name') === 'isupdate') ||
                                                        (arrLine[i].isdelete == 'Y' && $(elem).attr('name') === 'isdelete')) {
                                                        $(elem).prop('checked', true);
                                                    } else {
                                                        $(elem).prop('checked', false);
                                                    }

                                                    // Set attribute id element to value sys_access_menu_id
                                                    $(elem).attr('id', arrLine[i].sys_access_menu_id);
                                                }
                                            }
                                        });
                                    }
                                }
                            }

                            if (arrMsg.header) {
                                let header = arrMsg.header;

                                const field = form.find('input, textarea, select').not('.line');

                                if (form.find('select.select-data').length > 0) {
                                    let select = form.find('select.select-data');
                                    initSelectData(select, header[1].field, header[1].label);
                                }

                                for (let i = 0; i < header.length; i++) {
                                    let fieldInput = header[i].field;
                                    let label = header[i].label;

                                    if (formList.contains('modal') && fieldInput === 'title') {
                                        modalTitle.html(capitalize(label));
                                    } else if (fieldInput === 'title') {
                                        cardTitle.html(capitalize(label));
                                    }

                                    for (let i = 0; i < field.length; i++) {
                                        let fields = [];
                                        let fieldName = field[i].name;

                                        if (fieldName !== '' && fieldName === fieldInput) {
                                            let className = field[i].className.split(/\s+/);

                                            if (className.includes('datepicker')) {
                                                form.find('input:text[name=' + fieldName + ']').not('.line').val(moment(label).format('Y-MM-DD'));
                                            } else if (className.includes('rupiah')) {
                                                form.find('input:text[name=' + fieldName + ']').not('.line').val(formatRupiah(label));
                                            } else {
                                                form.find('input:text[name=' + fieldName + ']').not('.line').val(label);
                                            }

                                            form.find('textarea[name=' + fieldName + '], input:password[name=' + fieldName + ']').not('.line').val(label);

                                            if (form.find('textarea.summernote[name=' + fieldName + ']').length > 0 ||
                                                form.find('textarea.summernote-product[name=' + fieldName + ']').length > 0) {
                                                $('[name =' + fieldName + ']').not('.line').summernote('code', label);
                                            }

                                            if (field[i].type === 'select-one') {
                                                if (typeof label === 'object' && label !== null) {
                                                    let option_ID = label.id;
                                                    let option_Txt = label.name;

                                                    option.push({
                                                        fieldName,
                                                        option_ID,
                                                        option_Txt
                                                    });

                                                    let newOption = $("<option selected='selected'></option>").val(option_ID).text(option_Txt);
                                                    form.find('select[name=' + fieldName + ']').not('.line').append(newOption).change();
                                                } else if (typeof label === 'string' && (label !== null || label != 0)) {
                                                    option.push({
                                                        fieldName,
                                                        label
                                                    });

                                                    form.find('select[name=' + fieldName + ']').not('.line').val(label).change();
                                                }
                                            }

                                            if (field[i].type === 'select-multiple' && label !== null) {
                                                // array label explode into array
                                                let arrLabel = label.split(',');

                                                // Condition data length more than 1
                                                if (arrLabel.length > 1) {
                                                    form.find('select[name=' + fieldName + ']').not('.line').val(arrLabel).change();
                                                } else {
                                                    arrMultiSelect.push(label);
                                                    form.find('select[name=' + fieldName + ']').not('.line').val(arrMultiSelect).change();
                                                }
                                            }

                                            // Populate checked field default set on the attribute field
                                            if (field[i].type === 'checkbox' && field[i].checked)
                                                fieldChecked.push(fieldName);

                                            // Set condition value checked for field type Checkbox based on database
                                            if (field[i].type === 'checkbox' && label === 'Y') {
                                                form.find('input:checkbox[name=' + fieldName + ']').not('.line').prop('checked', true);

                                                if (className.includes('active'))
                                                    readonly(form, false);

                                                //TODO: Populate field checbox default disabled
                                                if (field[i].disabled)
                                                    fieldReadOnly.push(fieldName);
                                            } else {
                                                form.find('input:checkbox[name=' + fieldName + ']').not('.line').removeAttr('checked');

                                                if (className.includes('active'))
                                                    readonly(form, true);

                                                let fieldActive = form.find('input.active');

                                                // set field is readonly/disabled by default condition not field active and when detail content
                                                if (fieldActive.length == 0 && setSave !== 'detail' && (field[i].readOnly || field[i].disabled)) {
                                                    fieldReadOnly.push(fieldName);
                                                }

                                                if ($(field[i]).attr('edit-disabled')) {
                                                    form.find('input:checkbox[name=' + fieldName + '], select[name=' + fieldName + '], input:radio[name=' + fieldName + ']')
                                                        .not('.line')
                                                        .prop('disabled', true);
                                                }
                                            }
                                            // Set value checked for field type Radio Button
                                            if (field[i].type == 'radio') {
                                                if (field[i].value == label) {
                                                    field[i].checked = true;
                                                }
                                            }

                                            // Pass data form input file to function previewImage
                                            if (field[i].type === 'file') {
                                                if (className.includes('control-upload-image')) {
                                                    previewImage(form.find('input[name=' + fieldName + ']')[0], '', label);
                                                }
                                            }
                                        }

                                        //? Condition field and contain attribute hide-field
                                        if ($(field[i]).attr('hide-field') && fieldName !== '') {
                                            fields = $(field[i]).attr('hide-field').split(',').map(element => element.trim());

                                            //TODO: Checkbox
                                            if (field[i].type === 'checkbox') {
                                                if (field[i].checked) {
                                                    for (let i = 0; i < fields.length; i++) {
                                                        let formGroup = form.find('input[name=' + fields[i] + '], textarea[name=' + fields[i] + '], select[name=' + fields[i] + ']').not('.line').closest('.form-group, .form-check');
                                                        formGroup.hide();
                                                    }
                                                } else {
                                                    for (let i = 0; i < fields.length; i++) {
                                                        let formGroup = form.find('input[name=' + fields[i] + '], textarea[name=' + fields[i] + '], select[name=' + fields[i] + ']').not('.line').closest('.form-group, .form-check');
                                                        formGroup.show();
                                                    }
                                                }
                                            }

                                            //TODO: Dropdown select
                                            if (field[i].type === 'select-one') {
                                                for (let i = 0; i < fields.length; i++) {
                                                    const select = form.find('select[name=' + fields[i] + ']').not('.line');
                                                    let formGroup = [];

                                                    if ($(select).val() === null) {
                                                        formGroup = $(select).closest('.form-group');
                                                        formGroup.hide();
                                                    } else {
                                                        formGroup = $(select).closest('.form-group');
                                                        formGroup.show();
                                                    }
                                                }
                                            }
                                        }

                                        //? Condition field and contain attribute show-field
                                        if ($(field[i]).attr('show-field') && fieldName !== '') {
                                            fields = $(field[i]).attr('show-field').split(',').map(element => element.trim());

                                            //TODO: Checkbox
                                            if (field[i].type === 'checkbox') {
                                                if (field[i].checked) {
                                                    for (let i = 0; i < fields.length; i++) {
                                                        let formGroup = form.find('input[name=' + fields[i] + '], textarea[name=' + fields[i] + '], select[name=' + fields[i] + ']').not('.line').closest('.form-group, .form-check');
                                                        formGroup.show();
                                                    }
                                                } else if (field[i].type === 'checkbox') {
                                                    for (let i = 0; i < fields.length; i++) {
                                                        let formGroup = form.find('input[name=' + fields[i] + '], textarea[name=' + fields[i] + '], select[name=' + fields[i] + ']').not('.line').closest('.form-group, .form-check');
                                                        formGroup.hide();
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }

                            $('html, body').animate({
                                scrollTop: $('.main-panel').offset().top
                            }, 500);
                        } else {
                            Toast.fire({
                                type: 'error',
                                title: result[0].message
                            });
                        }
                    },
                    error: function (jqXHR, exception) {
                        showError(jqXHR, exception);
                    }
                });

                $(_this).html(oriElement).prop('disabled', false);
                card.removeClass("is-loading");
            }, 200));
    } else if (checkAccess[0].success && checkAccess[0].message == 'N') {
        Toast.fire({
            type: 'error',
            title: "You are role don't have permission, please reload !!"
        });
    } else {
        Toast.fire({
            type: 'error',
            title: checkAccess[0].message
        });
    }
});

/**
 * Button delete data
 */
function Destroy(id) {
    let url = SITE_URL + DELETE + id;
    let action = 'delete';

    let checkAccess = isAccess(action, LAST_URL);

    if (checkAccess[0].success && checkAccess[0].message == 'Y') {
        Swal.fire({
            title: 'Delete?',
            text: "Are you sure you wish to delete the selected data ? ",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Okay',
            cancelButtonText: 'Close',
            reverseButtons: true
        }).then((data) => {
            if (data.value) //value is true

                $.getJSON(url, function (result) {
                    if (result[0].success) {
                        Swal.fire({
                            title: 'Deleted!',
                            text: 'Your data has been deleted.',
                            type: 'success',
                            showConfirmButton: false,
                            timer: 1000,
                        });

                        reloadTable();
                    } else if (!result[0].error) {
                        Swal.fire({
                            title: 'Error!',
                            text: result[0].message,
                            type: 'error',
                            showConfirmButton: true
                        });

                        reloadTable();
                    } else {
                        console.info(result)
                    }
                })
                .fail(function (jqXHR, textStatus, errorThrown) {
                    console.info(errorThrown)
                    reloadTable();
                });
        });
    } else if (checkAccess[0].success && checkAccess[0].message == 'N') {
        Toast.fire({
            type: 'error',
            title: "You are role don't have permission, please reload !!"
        });
    } else {
        Toast.fire({
            type: 'error',
            title: checkAccess[0].message
        });
    }
}

_tableLine.on('click', '.btn_delete', function (evt) {
    evt.preventDefault();
    const form = $(evt.currentTarget).closest('form');
    const tr = _tableLine.$(this).closest('tr');
    const row = _tableLine.row(tr);
    let id = this.id;

    let url = SITE_URL + DELETE_LINE + id;

    let _this = $(this);
    let oriElement = _this.html();

    $(_this).html('<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>').prop('disabled', true);

    if (id === '') {
        setTimeout(function () {
            row.remove().draw(false);
            $(_this).html(oriElement).prop('disabled', false);
        }, 100);
    } else {
        Swal.fire({
            title: 'Delete?',
            text: "Are you sure to delete the selected data line ? ",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Okay',
            cancelButtonText: 'Close',
            reverseButtons: true
        }).then((data) => {
            if (data.value)
                $.getJSON(url, function (result) {
                    if (result[0].success) {
                        Swal.fire({
                            title: 'Deleted!',
                            text: 'Your data has been deleted.',
                            type: 'success',
                            showConfirmButton: false,
                            timer: 1000,
                        });

                        // Update field grand total
                        if (form.find('input[name="grandtotal"]').length > 0)
                            form.find('input[name="grandtotal"]').val(formatRupiah(result[0].message));

                        row.remove().draw(false);
                    } else {
                        Toast.fire({
                            type: 'error',
                            title: result[0].message
                        });
                    }
                }).fail(function (jqXHR, exception) {
                    showError(jqXHR, exception);
                });
        });

        $(_this).html(oriElement).prop('disabled', false);
    }
});

/**
 * Process Document Action
 * @param {*} id 
 * @param {*} status 
 */
function docProcess(id, status) {
    let action = 'update';
    let checkAccess = isAccess(action, LAST_URL);

    let html = '<div class="d-flex justify-content-center">' +
        '<select id="docaction">';

    if (status === 'DR') {
        html += '<option value=""></option>' +
            '<option value="CO">Complete</option>' +
            '<option value="VO">Void</option>';
    } else {
        html += '<option value=""></option>' +
            '<option value="VO">Void</option>';
    }

    html += '</select>' +
        '</div>';

    if (checkAccess[0].success && checkAccess[0].message == 'Y') {

        Swal.fire({
            title: 'Document Action',
            html: html,
            showCancelButton: true,
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ok',
            cancelButtonText: 'Close',
            showLoaderOnConfirm: true,
            reverseButtons: true,
            onOpen: () => {
                $('#docaction').select2({
                    placeholder: 'Select an option',
                    width: '40%',
                    theme: "bootstrap"
                })
            },
            preConfirm: (generate) => {
                return new Promise(function (resolve) {
                    let docAction = $('#docaction option:selected').val();

                    let url = SITE_URL + '/processIt?id=' + id + '&docaction=' + docAction;

                    $.getJSON(url, function (result) {
                            if (result[0].success) {
                                if (result[0].message == true) {
                                    Swal.fire({
                                        title: 'Success !!',
                                        text: 'Your data has been process',
                                        type: 'success',
                                        showConfirmButton: false,
                                        timer: 1000,
                                    });
                                } else {
                                    Swal.fire({
                                        title: 'Error!',
                                        text: result[0].message,
                                        type: 'error',
                                        showConfirmButton: false,
                                        timer: 1000,
                                    });
                                }

                                reloadTable();
                            }

                            if ((typeof result[0].error !== 'undefined' && result[0].error) || (typeof result[0].error !== 'undefined' && !result[0].error)) {
                                Swal.showValidationMessage(result[0].message);
                                resolve(false);
                            }
                        })
                        .fail(function (jqXHR, textStatus, errorThrown) {
                            Swal.showValidationMessage(errorThrown);
                            resolve(false);
                            reloadTable();
                        });
                });
            },
            allowOutsideClick: () => !Swal.isLoading()
        });
    } else if (checkAccess[0].success && checkAccess[0].message == 'N') {
        Toast.fire({
            type: 'error',
            title: "You are role don't have permission, please reload !!"
        });
    } else {
        Toast.fire({
            type: 'error',
            title: checkAccess[0].message
        });
    }
}

/**
 * Button close form
 * @x_form button only in modal
 * @close_form button in card-action
 */
$(document).on('click', '.x_form, .close_form', function (evt) {
    let target = $(evt.currentTarget);
    const container = target.closest('.container');

    let oriTitle = container.find('.page-title').text();

    setSave = 'close';

    if (target.attr('data-dismiss') !== 'modal') {
        const parent = target.closest('.container');
        const cardBody = parent.find('.card-body');

        $.each(cardBody, function (idx, elem) {
            let className = elem.className.split(/\s+/);

            if (className.includes('card-main')) {
                $(this).css('display', 'block');

                // Remove breadcrumb list
                let li = ul.find('li');
                $.each(li, function (idx, elem) {
                    if (idx > 2)
                        elem.remove();
                });

                if (parent.find('div.filter_page').length > 0) {
                    parent.find('div.filter_page').css('display', 'block');
                }
            }

            if (className.includes('card-form')) {
                $(this).css('display', 'none');
            }
        });

        cardBtn.css('display', 'none');

        const cardHeader = parent.find('.card-header');
        cardHeader.find('button').show();
    }

    clearForm(evt);
    cardTitle.html(oriTitle);

    // Clear button attribute disable 
    $(this).removeAttr('disabled');
    $(this).removeAttr('disabled');
    $('.save_form').removeAttr('disabled');

    $('html, body').animate({
        scrollTop: $('.main-panel').offset().top
    }, 500);
});

/**
 * Button new data
 */
$('.new_form').click(function (evt) {
    const parent = $(evt.target).closest('.container');
    const cardBody = parent.find('.card-body');
    let card = $(this).parents('.card');

    let form;
    let action = 'create';
    let oriTitle = parent.find('.page-title').text();
    let checkAccess = isAccess(action, LAST_URL);
    let _this = $(this);
    let oriElement = _this.html();
    let textElement = _this.text().trim();

    $(this).tooltip('hide');

    if (checkAccess[0].success && checkAccess[0].message == 'Y') {
        $(_this).html('<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>' + textElement).prop('disabled', true);

        card.length && (card.addClass("is-loading"),
            setTimeout(function () {
                $.each(cardBody, function (idx, elem) {
                    let className = elem.className.split(/\s+/);

                    if (cardBody.length > 1) {
                        if (className.includes('card-main')) {
                            $(this).css('display', 'none');

                            const pageHeader = parent.find('.page-header');
                            ul = pageHeader.find('ul.breadcrumbs');

                            // Append list separator and text create
                            ul.find('li.nav-item > a').attr('href', CURRENT_URL);

                            let list = '<li class="separator">' +
                                '<i class="flaticon-right-arrow"></i>' +
                                '</li>';

                            list += '<li class="nav-item">' +
                                '<a class="text-primary font-weight-bold">Create</a>' +
                                '</li>';

                            ul.append(list);

                            if (parent.find('div.filter_page').length > 0) {
                                parent.find('div.filter_page').css('display', 'none');
                            }
                        }

                        if (className.includes('card-form')) {
                            const cardHeader = $(evt.target).closest('.card-header');
                            cardHeader.find('button').css('display', 'none');
                            $(this).css('display', 'block');
                            cardBtn.css('display', 'block');

                            cardTitle.html('New ' + oriTitle);

                            form = $(this).find('form');

                            if (form.find('input:file.control-upload-image').length > 0) {
                                form.find('.img-result').attr('src', '');
                            }

                            const field = parent.find('input, textarea, select');

                            for (let i = 0; i < field.length; i++) {
                                let fields = [];

                                if (field[i].name !== '') {

                                    // set field is readonly or disabled by default
                                    if (field[i].readOnly || field[i].disabled)
                                        fieldReadOnly.push(field[i].name);

                                    // set field is checked by default from set attribute on the field
                                    if (field[i].type == 'checkbox' && fieldChecked.includes(field[i].name))
                                        form.find('input:checkbox[name=' + field[i].name + ']').prop('checked', true);

                                    //? Condition field and contain attribute hide-field
                                    if ($(field[i]).attr('hide-field')) {
                                        fields = $(field[i]).attr('hide-field').split(',').map(element => element.trim());

                                        if (field[i].type === 'checkbox') {
                                            if (field[i].checked) {
                                                for (let i = 0; i < fields.length; i++) {
                                                    let formGroup = form.find('input[name=' + fields[i] + '], textarea[name=' + fields[i] + '], select[name=' + fields[i] + ']').closest('.form-group, .form-check');
                                                    formGroup.hide();
                                                }
                                            } else {
                                                for (let i = 0; i < fields.length; i++) {
                                                    let formGroup = form.find('input[name=' + fields[i] + '], textarea[name=' + fields[i] + '], select[name=' + fields[i] + ']').closest('.form-group, .form-check');
                                                    formGroup.show();
                                                }
                                            }
                                        } else {
                                            for (let i = 0; i < fields.length; i++) {
                                                let formGroup = form.find('input[name=' + fields[i] + '], textarea[name=' + fields[i] + '], select[name=' + fields[i] + ']').closest('.form-group, .form-check');
                                                formGroup.hide();
                                            }
                                        }
                                    }

                                    //? Condition field and contain attribute show-field
                                    if ($(field[i]).attr('show-field')) {
                                        fields = $(field[i]).attr('show-field').split(',').map(element => element.trim());

                                        if (field[i].type === 'checkbox') {
                                            if (field[i].checked) {
                                                for (let i = 0; i < fields.length; i++) {
                                                    let formGroup = form.find('input[name=' + fields[i] + '], textarea[name=' + fields[i] + '], select[name=' + fields[i] + ']').closest('.form-group, .form-check');
                                                    formGroup.show();
                                                }
                                            } else if (field[i].type === 'checkbox') {
                                                for (let i = 0; i < fields.length; i++) {
                                                    let formGroup = form.find('input[name=' + fields[i] + '], textarea[name=' + fields[i] + '], select[name=' + fields[i] + ']').closest('.form-group, .form-check');
                                                    formGroup.hide();
                                                }
                                            }
                                        } else {
                                            for (let i = 0; i < fields.length; i++) {
                                                let formGroup = form.find('input[name=' + fields[i] + '], textarea[name=' + fields[i] + '], select[name=' + fields[i] + ']').closest('.form-group, .form-check');
                                                formGroup.show();
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    } else {
                        openModalForm();
                        Scrollmodal();
                        modalTitle.html('New1 ' + capitalize(LAST_URL));

                        form = modalForm.find('form');

                        if (form.find('input:file.control-upload-image').length > 0) {
                            form.find('.img-result').attr('src', '');
                        }
                    }
                });

                if (form.find('input.code').length > 0) {
                    setSeqCode(form);
                }

                if (form.find('select.select-data').length > 0) {
                    let select = form.find('select.select-data');
                    initSelectData(select);
                }

                form.find('input[type="checkbox"].active').prop('checked', true);

                setSave = 'add';

                $(_this).html(oriElement).prop('disabled', false);
                card.removeClass("is-loading");
            }, 200));
    } else if (checkAccess[0].success && checkAccess[0].message == 'N') {
        Toast.fire({
            type: 'warning',
            title: "You are role don't have permission, please reload !!"
        });
    } else {
        Toast.fire({
            type: 'error',
            title: checkAccess[0].message
        });
    }
});

/**
 * Process for Export based on filter form
 */
$('.btn_export').click(function (evt) {
    const container = $(evt.target).closest('.container');
    const cardFilter = container.find('.card-filter');
    let form = cardFilter.find('form');

    let _this = $(this);
    let oriElement = _this.html();

    form.attr('action', SITE_URL + EXPORT);
    form.attr('method', 'POST');

    // form submit to export data
    form.submit();

    $(_this).html('<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>').prop('disabled', true);

    setTimeout(function () {
        $(_this).html(oriElement).prop('disabled', false);
    }, 700);
});

/**
 * Process for filter datatable form filter
 */
$('.btn_filter').click(function (evt) {
    let _this = $(this);
    const container = _this.parents('.container');
    const main_page = container.find('.main_page');
    const form = container.find('form');
    let oriElement = _this.html();
    let textElement = _this.text().trim();
    let s = main_page.find('.card');

    formTable = form.serializeArray();

    $(_this).html('<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>' + textElement).prop('disabled', true);

    s.length && (s.addClass("is-loading"),
        reloadTable(),
        setTimeout(function () {
            s.removeClass("is-loading");
            $(_this).html(oriElement).prop('disabled', false);
        }, 700));
});

/**
 * Button ReQuery DataTable
 */
$('.btn_requery').click(function () {
    let _this = $(this);
    let s = _this.parents(".card");

    $(this).tooltip('hide');

    s.length && (s.addClass("is-loading"),
        reloadTable(),
        setTimeout(function () {
            s.removeClass("is-loading");
        }, 500));
});

/**
 * Event add row table line
 */
$('.add_row').click(function (evt) {
    let form = $(evt.target).closest('form');

    let url = SITE_URL + TABLE_LINE;

    let action = 'create';

    let checkAccess = isAccess(action, LAST_URL);

    if (checkAccess[0].success && checkAccess[0].message == 'Y') {
        let _this = $(this);
        let oriElement = _this.html();
        let textElement = _this.text().trim();

        let formData = new FormData(form[0]);

        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            cache: false,
            dataType: 'JSON',
            beforeSend: function () {
                $('.close_form').attr('disabled', true);
                $('.save_form').attr('disabled', true);
                $(_this).html('<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>' + textElement).prop('disabled', true);
            },
            complete: function () {
                $('.close_form').removeAttr('disabled');
                $('.save_form').removeAttr('disabled');
                $(_this).html(oriElement).prop('disabled', false);
            },
            success: function (result) {
                _tableLine.row.add(result).draw(false);
            },
            error: function (jqXHR, exception) {
                showError(jqXHR, exception);
            }
        });
    } else if (checkAccess[0].success && checkAccess[0].message == 'N') {
        Toast.fire({
            type: 'warning',
            title: "You are role don't have permission !!"
        });
    } else {
        Toast.fire({
            type: 'error',
            title: checkAccess[0].message
        });
    }
});

/**
 * Event create table line
 */
$('.create_line').click(function (evt) {
    let action = 'create';
    let checkAccess = isAccess(action, LAST_URL);
    let formData = $(this).closest('form');

    if (checkAccess[0].success && checkAccess[0].message == 'Y') {
        let _this = $(this);
        let oriElement = _this.html();
        let textElement = _this.text().trim();

        let isFree = 'N';
        if (formData.find('input:checkbox[name="isinternaluse"]').is(':checked'))
            isFree = 'Y';

        $(_this).html('<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>' + textElement).prop('disabled', true);

        setTimeout(function () {
            $(_this).html(oriElement).prop('disabled', false);

            $('#modal_product_info').modal({
                backdrop: 'static',
                keyboard: false
            });

            loadingForm('product_info', 'ios');

            $("#modal_product_info").on('shown.bs.modal', function (e) {
                const target = $(e.target);
                const form = target.find('form');

                let url = ADMIN_URL + 'product' + '/showProductInfo/?data=null';

                form[0].reset();

                setTimeout(function () {
                    hideLoadingForm('product_info');

                    if (form.find('select.select-data').length > 0) {
                        let select = form.find('select.select-data');
                        initSelectData(select);
                    }

                    if (form.find('input:hidden[name="isfree"]'))
                        form.find('input:hidden[name="isfree"]').val(isFree);

                    _tableInfo.ajax.url(url).load().columns.adjust();

                }, 50);
            });
        }, 100);


    } else if (checkAccess[0].success && checkAccess[0].message == 'N') {
        Toast.fire({
            type: 'warning',
            title: "You are role don't have permission !!"
        });
    } else {
        Toast.fire({
            type: 'error',
            title: checkAccess[0].message
        });
    }
});

/**
 * Clear content modal product info
 */
$("#modal_product_info").on('hidden.bs.modal', function (evt) {
    const target = $(evt.target);
    const form = target.find('form');

    //TODO: Clear form content
    form[0].reset();

    //TODO: Clear datatable
    _tableInfo.clear().draw();
});

/**
 * Refresh data table info
 */
$('.btn_requery_info').click(function (evt) {
    const target = $(evt.target);
    const modalContent = target.closest('.modal-content');
    const form = modalContent.find('form');

    let url = ADMIN_URL + 'product' + '/showProductInfo/?';
    let formData = form.serialize();

    $(this).tooltip('hide');

    _tableInfo.ajax.url(url + formData).load().columns.adjust();
});

/**
 * Btn save info for set data from table info to table line
 */
$('.btn_save_info').click(function (evt) {
    const modal = $(this).closest('.modal');
    const modalBody = modal.find('.modal-body');

    const checkbox = _tableInfo.rows().nodes().to$().find('input:checkbox[name="check_data"]:checked');

    let _this = $(this);
    let oriElement = _this.html();

    if (checkbox.length > 0) {
        let url = SITE_URL + TABLE_LINE + '/create';
        let output = [];

        $.each(checkbox, function (i) {
            let tr = $(this).closest('tr');
            let tag = tr.find('input, select');

            let data = [];

            $.each(tag, function (index, element) {
                let row = [];
                let name = $(element).attr('name');
                let value = $(element).val();

                if ($(element).attr('type') !== 'checkbox') {
                    row = {
                        [name]: value
                    };
                } else {
                    if (name === 'check_data')
                        row = {
                            product_id: value
                        };
                    else
                        row = {
                            [name]: $(element).is(':checked')
                        }
                }

                data.push(row);
            });

            output[i] = data;
        });

        let jsonString = JSON.stringify(output);

        $.ajax({
            url: url,
            type: 'POST',
            data: {
                data: jsonString
            },
            cache: false,
            dataType: 'JSON',
            beforeSend: function () {
                $(_this).html('<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>').prop('disabled', true);
                $('.btn_requery_info').attr('disabled', true);
                $('.btn_close_info').attr('disabled', true);
                loadingForm(modalBody.attr('id'), 'ios');
            },
            complete: function () {
                $(_this).html(oriElement).prop('disabled', false);
                $('.btn_requery_info').removeAttr('disabled');
                $('.btn_close_info').removeAttr('disabled');
                hideLoadingForm(modalBody.attr('id'));
            },
            success: function (result) {
                $('#' + modal.attr('id')).modal('hide');
                _tableLine.rows.add(result).draw(false);
            },
            error: function (jqXHR, exception) {
                showError(jqXHR, exception);
            }
        });
    } else {
        Toast.fire({
            type: 'warning',
            title: 'Please selected data !!'
        });
    }

});


/**
 * Process login
 */
$('.btn_login').click(function () {
    let _this = $(this);
    let oriElement = _this.html();

    const form = $(this).closest('form');

    let url = ADMIN_URL + 'auth/login';

    $.ajax({
        url: url,
        type: 'POST',
        data: form.serialize(),
        cache: false,
        dataType: 'JSON',
        beforeSend: function () {
            $(this).prop('disabled', true);
            $(_this).html('<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>').prop('disabled', true);
        },
        complete: function () {
            $(this).removeAttr('disabled');
            $(_this).html(oriElement).prop('disabled', false);
        },
        success: function (result) {
            if (result[0].success) {
                Toast.fire({
                    type: 'success',
                    title: result[0].message
                });

                window.open(ORI_URL + '/sas', '_self');

            } else if (result[0].error) {
                errorForm(form, result);
            } else {
                Toast.fire({
                    type: 'error',
                    title: result[0].message
                });
            }
        },
        error: function (jqXHR, exception) {
            showError(jqXHR, exception);
        }
    });
});

/**
 * Enter key press button login form
 */
$('.login-form input').keypress(function (evt) {
    let key = evt.which;

    if (key == 13)
        $('.btn_login').click();
});

/**
 * Anchor change password on the navbar admin
 */
$('.change-password').click(function (evt) {
    ID = $(this).attr('id');
    openModalForm();
});

/**
 * Save modal password
 */
$('.save_form_pass').click(function (evt) {
    const parent = $(evt.target).closest('.modal');
    const form = parent.find('form');

    let _this = $(this);
    let oriElement = _this.html();

    let url = ADMIN_URL + 'auth/' + 'changePassword';

    let formData = new FormData(form[0]);

    if (typeof ID !== 'undefined' && ID !== '')
        formData.append('id', ID);

    $.ajax({
        url: url,
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        cache: false,
        dataType: 'JSON',
        beforeSend: function () {
            $('.close').prop('disabled', true);
            loadingForm(form.prop('id'), 'facebook');
            $(_this).html('<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>').prop('disabled', true);
        },
        complete: function () {
            $('.close').prop('disabled', false);
            hideLoadingForm(form.prop('id'));
            $(_this).html(oriElement).prop('disabled', false);
        },
        success: function (result) {
            if (result[0].success) {
                Toast.fire({
                    type: 'success',
                    title: result[0].message
                });

                clearForm(evt);

                $('.modal_form').modal('hide');

            } else if (result[0].error) {
                let fields = result[0].message;

                $.each(fields, function (idx, elem) {
                    if (elem !== '') {
                        form.find('input:password[name="' + idx + '"]')
                            .closest('.form-group')
                            .addClass('has-error');

                        form.find('small[id=error_' + idx + ']').html(elem);
                    } else {
                        form.find('input:password[name="' + idx + '"]')
                            .closest('.form-group')
                            .removeClass('has-error');

                        form.find('small[id=error_' + idx + ']').html('');
                    }
                });
            } else {
                Toast.fire({
                    type: 'error',
                    title: result[0].message
                });
            }
        },
        error: function (jqXHR, exception) {
            showError(jqXHR, exception);
        }
    });
});

/**
 * Process for active non-active field in the form using checkbox class active
 */
$('input.active:checkbox').change(function (evt) {
    const parent = $(this).closest('form');
    const field = parent.find('input, textarea, select');
    let className;

    if ($(this).is(':checked')) {
        for (let i = 0; i < field.length; i++) {
            if (field[i].name !== '') {
                className = field[i].className.split(/\s+/);

                // field is not readonly by default
                if (!fieldReadOnly.includes(field[i].name))
                    parent.find('input:text[name=' + field[i].name + '], textarea[name=' + field[i].name + '], input:password[name=' + field[i].name + ']').removeAttr('readonly');

                if (field[i].type !== 'text' && !className.includes('active') && !fieldReadOnly.includes(field[i].name)) {
                    parent.find('input[name=' + field[i].name + '], select[name=' + field[i].name + ']').removeAttr('disabled');

                    if (field[i].type === 'file') {
                        parent.find('input[name=' + field[i].name + ']').removeAttr('disabled');
                        parent.find('button.close-img')
                            .removeAttr('disabled')
                            .css('display', 'block');
                    }
                }

                if (parent.find('textarea.summernote[name=' + field[i].name + ']').length > 0 ||
                    parent.find('textarea.summernote-product[name=' + field[i].name + ']').length > 0) {
                    $('[name =' + field[i].name + ']').summernote('enable');
                }
            }
        }
    } else {
        for (let i = 0; i < field.length; i++) {
            if (field[i].name !== '') {
                className = field[i].className.split(/\s+/);

                // set field is readonly by default
                if ((field[i].readOnly || field[i].disabled) && field[i].type !== 'radio')
                    fieldReadOnly.push(field[i].name);

                // field is not readonly by default
                if (!fieldReadOnly.includes(field[i].name))
                    parent.find('input:text[name=' + field[i].name + '], textarea[name=' + field[i].name + '], input:password[name=' + field[i].name + ']').not('.line').prop('readonly', true);

                if (field[i].type !== 'text' && !className.includes('active') && !fieldReadOnly.includes(field[i].name)) {
                    parent.find('input[name=' + field[i].name + '], select[name=' + field[i].name + ']').not('.line').prop('disabled', true);

                    if (field[i].type === 'file') {
                        parent.find('input[name=' + field[i].name + ']').prop('disabled', true);
                        parent.find('button.close-img')
                            .prop('disabled', true)
                            .css('display', 'none');
                    }
                }

                if (parent.find('textarea.summernote[name=' + field[i].name + ']').length > 0 ||
                    parent.find('textarea.summernote-product[name=' + field[i].name + ']').length > 0) {
                    $('[name =' + field[i].name + ']').summernote('disable');
                }
            }
        }
    }
});


/**
 * Button close image
 */
$('.close-img').click(function (evt) {
    const parent = $(evt.currentTarget).closest('div');
    const formGroup = parent.closest('.form-group');
    const formUpload = formGroup.find('.form-upload');
    const form = $(evt.currentTarget).closest('form');
    const field = form.find('input');

    let className = parent.find('label').prop('className');

    // set condition when add to clear all
    if (className.includes('form-result')) {
        formUpload.find('label').css('display', 'block');
        parent.find('label').css('display', 'none');
        formUpload.find('input:file').val('');
        parent.find('.img-result').attr('src', '');

        for (let i = 0; i < field.length; i++) {
            if (field[i].name !== '') {
                if (field[i].type === 'file') {
                    form.find('input[name=' + field[i].name + ']').removeAttr('disabled');
                    parent.find('button.close-img')
                        .removeAttr('disabled')
                        .css('display', 'block');
                }
            }
        }
    }
});

/**
 * Function to search exist value data
 * @param {*} value to search exist value
 * @param {*} arr array data
 * @returns
 */
function arrContains(value, arr) {
    var result = null;

    for (let i = 0; i < arr.length; i++) {
        var fieldName = arr[i];
        if (fieldName.toString().toLowerCase() === value.toString().toLowerCase()) {
            result = fieldName;
            break;
        }
    }
    return result;
}

/**
 * Function to show Error Validation on the field
 * @param {*} parent selector html
 * @param {*} data from database
 */
function errorForm(parent, data) {
    const errorInput = parent.find('input, select, textarea');
    const errorText = parent.find('small');

    let arrInput = [];
    let arrText = [];

    for (let i = 0; i < errorText.length; i++) {
        if (errorText[i].id !== '')
            arrText.push(errorText[i].id);
    }

    for (let k = 0; k < errorInput.length; k++) {
        arrInput.push(errorInput[k].name);
    }

    for (let j = 0; j < data.length; j++) {
        let error = data[j].error;
        let field = data[j].field;
        let labelMsg = data[j].label;

        let textName = arrContains(error, arrText);
        let inputName = arrContains(field, arrInput);

        if (labelMsg !== '' && j > 0) {
            parent.find('small[id=' + textName + ']:not(.line)').html(labelMsg);
            parent.find('input:text[name=' + inputName + ']:not(.line), select[name=' + inputName + ']:not(.line), textarea[name=' + inputName + ']:not(.line), input:password[name=' + inputName + ']:not(.line)').closest('.form-group').addClass('has-error');

            // Check datatable line for get validation
            if (parent.find('table.tb_displayline').length > 0) {

                // Error validation for datatable line
                if (field === 'line')
                    Toast.fire({
                        type: 'error',
                        title: labelMsg
                    });

                const tdInput = _tableLine.rows().nodes().to$().find('input, select');

                let arrValue = [];

                $.each(tdInput, function (i) {
                    let value = this.value;
                    let name = $(this).attr('name');
                    let className = $(this)[0].className.split(/\s+/);

                    let index = $(this).closest('tr')[0]._DT_RowIndex;

                    if ($(this).attr('required')) {
                        let row = Number(index + 1);

                        // Error validation for every line
                        if (typeof error !== 'undefined' && error === 'error_table' && labelMsg !== '') {
                            if (name === field && (value === '' || value == 0)) {
                                $(this).closest('.form-group').addClass('has-error');
                                Toast.fire({
                                    type: 'error',
                                    title: labelMsg + ' : ' + row
                                });
                            } else if (name === field && value !== '') {
                                arrValue.push(value);

                                let duplicateValue = findArrDuplicate(arrValue);
                                let existsValue = labelMsg.split('|')[0].trim();

                                // Duplicate value every line
                                if (duplicateValue.length > 0 && duplicateValue.includes(value) && className.includes('unique')) {
                                    $(this).closest('.form-group').addClass('has-error');
                                    Toast.fire({
                                        type: 'error',
                                        title: labelMsg
                                    });
                                } else if (existsValue === value) { // Value already exists from database
                                    labelMsg = labelMsg.split('|')[1].trim();

                                    $(this).closest('.form-group').addClass('has-error');
                                    Toast.fire({
                                        type: 'error',
                                        title: labelMsg
                                    });
                                } else {
                                    $(this).closest('.form-group').removeClass('has-error');
                                }
                            } else if (!className.includes('unique') && value !== '') {
                                $(this).closest('.form-group').removeClass('has-error');
                            }
                        } else if (typeof error !== 'undefined' && error !== 'error_table') {
                            $(this).closest('.form-group').removeClass('has-error');
                        }
                    }
                });
            }
        } else {
            parent.find('small[id=' + textName + ']:not(.line)').html('');
            parent.find('input:text[name=' + inputName + ']:not(.line), select[name=' + inputName + ']:not(.line), textarea[name=' + inputName + ']:not(.line), input:password[name=' + inputName + ']:not(.line)').closest('.form-group').removeClass('has-error');
        }
    }
}

function findArrDuplicate(array) {
    return array.filter(function (item, pos, self) {
        return self.indexOf(item) != pos;
    });
}

/**
 * Function to clear value and attribute on the field
 * @param {*} evt selector html
 */
function clearForm(evt) {
    const container = $(evt.target).closest('.container');
    let parent = $(evt.target).closest('.row');
    const cardForm = parent.find('.card-form');
    let form = cardForm.find('form');

    if ($(evt.target).closest('.row').length == 0) {
        parent = $(evt.target).closest('.modal');
        form = parent.find('form');
    }

    const field = form.find('input, textarea, select');
    const errorText = form.find('small');

    // clear field data on the form
    form[0].reset();

    // Get data default logic
    let urlDefault = '/quotation/defaultLogic';

    let defaultLogic = getLogic(urlDefault);

    // clear data, attribute readonly, attribute disabled on the field and remove class invalid
    for (let i = 0; i < field.length; i++) {
        if (field[i].name !== '') {
            if (fieldReadOnly.length == 0) {
                form.find('input[name=' + field[i].name + '], textarea[name=' + field[i].name + ']')
                    .removeAttr('readonly')
                    .closest('.form-group')
                    .removeClass('has-error');
            } else if (fieldReadOnly.length > 0) { // field is not readonly by default
                if (!fieldReadOnly.includes(field[i].name)) {
                    form.find('input[name=' + field[i].name + '], textarea[name=' + field[i].name + ']')
                        .removeAttr('readonly')
                        .closest('.form-group')
                        .removeClass('has-error');
                } else {
                    form.find('input[name=' + field[i].name + '], textarea[name=' + field[i].name + ']')
                        .closest('.form-group')
                        .removeClass('has-error');
                }
            }

            if (!fieldReadOnly.includes(field[i].name) && field[i].type === 'checkbox')
                form.find('input:checkbox[name=' + field[i].name + ']')
                .removeAttr('disabled');

            //logic clear data dropdown if not selected from the beginning
            if (defaultLogic.length > 0 && field[i].name === defaultLogic[0].field && defaultLogic[0].condition) {
                if (fieldReadOnly.length == 0) {
                    form.find('select[name=' + field[i].name + ']')
                        .val(defaultLogic[0].id).change()
                        .removeAttr('disabled')
                        .closest('.form-group').removeClass('has-error');
                } else if (fieldReadOnly.length > 0) { // field is not readonly by default
                    if (!fieldReadOnly.includes(field[i].name)) {
                        form.find('select[name=' + field[i].name + ']')
                            .val(defaultLogic[0].id).change()
                            .removeAttr('disabled')
                            .closest('.form-group').removeClass('has-error');
                    } else {
                        form.find('select[name=' + field[i].name + ']')
                            .val(defaultLogic[0].id).change()
                            .closest('.form-group').removeClass('has-error');
                    }
                }
            } else {
                if (fieldReadOnly.length == 0) {
                    form.find('select[name=' + field[i].name + ']')
                        .val(null).change()
                        .removeAttr('disabled')
                        .closest('.form-group').removeClass('has-error');
                } else if (fieldReadOnly.length > 0) { // field is not readonly by default
                    if (!fieldReadOnly.includes(field[i].name)) {
                        form.find('select[name=' + field[i].name + ']')
                            .val(null).change()
                            .removeAttr('disabled')
                            .closest('.form-group').removeClass('has-error');
                    } else {
                        form.find('select[name=' + field[i].name + ']')
                            .val(null).change()
                            .closest('.form-group').removeClass('has-error');
                    }
                }
            }

            // Type input file
            if (field[i].type == 'file') {
                $('.close-img').click();
            }

            // Textarea class summernote
            if (form.find('textarea.summernote[name=' + field[i].name + ']').length > 0 ||
                form.find('textarea.summernote-product[name=' + field[i].name + ']').length > 0) {
                $('[name =' + field[i].name + ']').summernote('reset');
                $('[name =' + field[i].name + ']').summernote('enable');
            }

            // Exist table display line
            if (form.find('table.tb_displayline').length > 0) {
                _tableLine.clear().draw();

                const btnAction = _tableLine.rows().to$().find('button');
                // Button add row table line
                $('.add_row, .create_line').css('display', 'block');

                // button remove data line
                btnAction.css('display', 'block');
            }

            if ($(field[i]).attr('edit-disabled'))
                form.find('input:checkbox[name=' + field[i].name + '], select[name=' + field[i].name + '], input:radio[name=' + field[i].name + ']')
                .removeAttr('disabled');

            form.find('input:radio[name=' + field[i].name + ']')
                .removeAttr('disabled');
        }
    }

    // clear text error element small
    for (let l = 0; l < errorText.length; l++) {
        if (errorText[l].id !== '')
            form.find('small[id=' + errorText[l].id + ']').html('');
    }

    // Set to empty array option
    option = [];
}

/**
 * Function to set field condition readonly true/false
 * @param {*} parent selector html
 * @param {*} value based on passing data (true/false)
 */
function readonly(parent, value) {
    const field = parent.find('input, textarea, select');

    for (let i = 0; i < field.length; i++) {
        if (field[i].name !== '') {
            let className = field[i].className.split(/\s+/);

            // set field is readonly by default
            if ((field[i].readOnly || field[i].disabled) && field[i].type !== 'radio')
                fieldReadOnly.push(field[i].name);

            // field is not readonly by default
            if (!fieldReadOnly.includes(field[i].name))
                parent.find('input:text[name=' + field[i].name + '], textarea[name=' + field[i].name + '], input:password[name=' + field[i].name + ']').not('.line').prop('readonly', value);

            if (field[i].type !== 'text' && !className.includes('active') && !fieldReadOnly.includes(field[i].name)) {
                parent.find('input:checkbox[name=' + field[i].name + '], select[name=' + field[i].name + '], input:radio[name=' + field[i].name + ']')
                    .not('.line')
                    .prop('disabled', value);
            }

            if (field[i].type === 'file') {
                parent.find('input[name=' + field[i].name + ']').not('.line').prop('disabled', value);
            }

            if (parent.find('textarea.summernote[name=' + field[i].name + ']').length > 0 ||
                parent.find('textarea.summernote-product[name=' + field[i].name + ']').length > 0) {
                if (value) {
                    $('[name =' + field[i].name + ']').not('.line').summernote('disable');
                } else {
                    $('[name =' + field[i].name + ']').not('.line').summernote('enable');
                }
            }
        }
    }

    // check button close image based on value
    if (parent.find('button.close-img').length > 0) {
        parent.find('button.close-img').not('.line').prop('disabled', value)

        if (value) {
            parent.find('button.close-img').not('.line').css('display', 'none');
        } else {
            parent.find('button.close-img').not('.line').css('display', 'block');
        }
    }
}

/**
 *
 * @param {*} input selector element html
 * @param {*} id
 * @param {*} src source image
 */
function previewImage(input, id, src) {
    let labelUpload = input.closest('label');
    id = id || '.img-result';

    src = src == null ? '' : src;

    if (input.files && input.files[0]) {
        let reader = new FileReader();

        reader.onload = function (e) {
            loadingForm(labelUpload.id, 'pulse');
            $('.save_form').attr('disabled', true);
            $('.x_form').attr('disabled', true);
            $('.close_form').attr('disabled', true);

            setTimeout(function () {
                $(id)
                    .attr('src', e.target.result)
                    .width('auto')
                    .height(150);

                $('.form-upload-foto').css('display', 'none');
                $('.form-result').css('display', 'block');

                hideLoadingForm(labelUpload.id);

                $('.save_form').removeAttr('disabled');
                $('.x_form').removeAttr('disabled');
                $('.close_form').removeAttr('disabled');
            }, 2500);
        };

        reader.readAsDataURL(input.files[0]);
    } else if (src !== '') {
        src = ORI_URL + '/' + src;

        $.ajax({
            url: src,
            type: 'HEAD',
            error: function () {
                $(id)
                    .attr('src', '')
                    .width('auto')
                    .height(150);
                $('.form-upload-foto').css('display', 'block');
                $('.form-result').css('display', 'none');
            },
            success: function () {
                loadingForm(labelUpload.id, 'pulse');
                $('.save_form').attr('disabled', true);
                $('.x_form').attr('disabled', true);
                $('.close_form').attr('disabled', true);

                setTimeout(function () {
                    $(id)
                        .attr('src', src)
                        .width('auto')
                        .height(150);
                    $('.form-upload-foto').css('display', 'none');
                    $('.form-result').css('display', 'block');

                    hideLoadingForm(labelUpload.id);

                    $('.save_form').removeAttr('disabled');
                    $('.x_form').removeAttr('disabled');
                    $('.close_form').removeAttr('disabled');
                }, 500);
            }
        });
    } else {
        $(id)
            .attr('src', '')
            .width('auto')
            .height(150);
        $('.form-upload-foto').css('display', 'block');
        $('.form-result').css('display', 'none');
    }
}

/**
 * 
 * @param {*} input action "create, update, delete"
 * @param {*} last_url get the last url
 * @returns 
 */
function isAccess(input, last_url) {
    let url = ADMIN_URL + 'accessmenu/' + 'getAccess';
    let value;

    $.ajax({
        url: url,
        type: 'POST',
        data: {
            last_url: last_url,
            action: input
        },
        async: false,
        dataType: 'JSON',
        success: function (result) {
            value = result;
        },
        error: function (jqXHR, exception) {
            showError(jqXHR, exception);
        }
    });

    return value;
}

/**
 * Function for show code numbering based on class code
 * @param {*} form 
 */
function setSeqCode(form) {
    let url = SITE_URL + '/getSeqCode';

    $.getJSON(url, function (result) {
        form.find('input.code').val(result[0].message);
    }).fail(function (jqXHR, exception) {
        showError(jqXHR, exception);
    });
}

/**
 * Function to show error logic when process ajax
 * @param {*} xhr
 * @param {*} exception
 */
function showError(xhr, exception) {
    let msg = '';

    if (xhr.status === 0)
        msg = 'Not connect.\n Verify Network.';
    else if (xhr.status == 404)
        msg = 'Requested page not found. [404]';
    else if (xhr.status == 500)
        msg = 'Internal Server Error [500].';
    else if (exception === 'parsererror')
        msg = 'Requested JSON parse failed.';
    else if (exception === 'timeout')
        msg = 'Time out error.';
    else if (exception === 'abort')
        msg = 'Ajax request aborted.';
    else
        msg = 'Uncaught Error.\n' + xhr.responseText;

    Toast.fire({
        type: 'error',
        title: msg
    });
}

/**
 * Function to show wait Loading
 * @param {*} selectorID form html
 * @param {*} effect
 */
function loadingForm(selectorID, effect) {
    $('#' + selectorID + '').waitMe({
        effect: effect,
        text: 'Please wait...',
        bg: 'rgba(255,255,255,0.7)',
        color: '#000',
        maxSize: '',
        waitTime: -1,
        textPos: 'vertical',
        fontSize: '100%',
        source: '',
        onClose: function () {}
    });
}

/**
 * Function to hide wait Loading
 * @param {*} selectorID form html
 */
function hideLoadingForm(selectorID) {
    $('#' + selectorID + '').waitMe('hide');
}

/**
 * Function to set text to Capitalize
 * @param {*} s string value
 * @returns
 */
const capitalize = (s) => {
    if (typeof s !== 'string') return ''
    return s.charAt(0).toUpperCase() + s.slice(1)
}

/**
 * Funtion to show modal form
 */
function openModalForm() {
    return $('.modal_form').modal({
        backdrop: 'static',
        keyboard: false
    });
}

/**
 * Return call class scrollable in modal
 */
function Scrollmodal() {
    return modalDialog.addClass('modal-dialog-scrollable');
}

/**
 * Function for convert numeric to rupiah format
 * @param {*} numeric 
 * @returns 
 */
function formatRupiah(numeric) {
    let number_string = numeric.toString(),
        split = number_string.split(','),
        sisa = split[0].length % 3,
        rupiah = split[0].substr(0, sisa),
        ribuan = split[0].substr(sisa).match(/\d{3}/gi);

    // tambahkan titik jika yang di input sudah menjadi angka ribuan
    if (ribuan) {
        separator = sisa ? '.' : '';
        rupiah += separator + ribuan.join('.'); //penambahan separator titik setelah bilangan satuan
    }

    return rupiah ? rupiah : '';
}

/**
 * Function for convert rupiah to numeric
 * @param {*} numeric 
 * @returns 
 */
function replaceRupiah(numeric) {
    return numeric.replace(/\./g, "");
}

/**
 * Function initialize select2 dropdown based on url on the element html
 * @param {*} select 
 */
function initSelectData(select, field = null, id = null) {
    $.each(select, function (i, item) {
        let url = $(item).attr('data-url');
        let defaultID = $(item).attr('default-id');
        let defaultText = $(item).attr('default-text');

        if (typeof url !== 'undefined' && url !== '') {

            if (field !== null && id !== null)
                url = ADMIN_URL + url + '?' + field + '=' + id;
            else
                url = ADMIN_URL + url;

            $(this).select2({
                placeholder: 'Select an option',
                width: '100%',
                theme: 'bootstrap',
                allowClear: true,
                // minimumInputLength: 3,
                ajax: {
                    dataType: 'JSON',
                    url: function () {
                        return url;
                    },
                    delay: 250,
                    data: function (params) {
                        return {
                            search: params.term
                        }
                    },
                    processResults: function (data, page) {
                        return {
                            results: data
                        };
                    },
                    cache: true
                }
            });

            if ((typeof defaultID !== 'undefined' && defaultID !== '') && (typeof defaultText !== 'undefined' && defaultText !== '')) {
                let optionSelected = $("<option selected='selected'></option>").val(defaultID).text(defaultText);
                $(this).append(optionSelected).change();
            }
        }

    });
}

/**
 * Function for get data from database
 * @param {*} url 
 * @param {*} field 
 * @param {*} reference 
 * @returns 
 */
function getList(url, field, reference) {
    let value;

    $.ajax({
        url: ADMIN_URL + url,
        type: 'POST',
        data: {
            field: field,
            reference: reference
        },
        async: false,
        dataType: 'JSON',
        success: function (response) {
            value = response;
        }
    });

    return value;
}

/**
 * Remove element from array
 * @param {*} array 
 * @param {*} itemsToRemove 
 * @returns 
 */
function removeItems(array, itemsToRemove) {
    const index = array.indexOf(itemsToRemove);

    if (index > -1)
        return array.splice(index, 1);
}

/**
 * Function for get logic from controller
 * @param {*} url
 * @returns 
 */
function getLogic(url) {
    let value = [];

    $.ajax({
        url: ADMIN_URL + url,
        type: 'POST',
        async: false,
        dataType: 'JSON',
        success: function (response) {
            value.push(response);
        }
    });

    return value;
}

$(document).ready(function (e) {
    $('.select2').select2({
        placeholder: 'Select an option',
        width: '100%',
        theme: "bootstrap",
        allowClear: true
    });

    $('.multiple-select').select2({
        theme: "bootstrap"
    });

    $('.number').on('keypress keyup blur', function (evt) {
        $(this).val($(this).val().replace(/[^\d].+/, ""));
        if ((evt.which < 48 || evt.which > 57)) {
            evt.preventDefault();
        }
    });

    Toast = Swal.mixin({
        toast: true,
        position: 'top',
        showConfirmButton: false,
        timer: 4000
    });

    $('.datepicker').datetimepicker({
        format: 'YYYY-MM-DD',
    });

    $('.timepicker').datetimepicker({
        format: 'H:mm:ss',
    });

    $('.summernote').summernote({
        fontNames: ['Arial', 'Arial Black', 'Comic Sans MS', 'Courier New', 'Times New Roman'],
        tabsize: 2,
        height: 200,
        toolbar: [
            ['style', ['style', 'bold', 'italic', 'underline', 'clear']],
            ['fontname', ['fontname']],
            ['fontsize', ['fontsize']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['table', ['table']],
            // ['insert', ['link', 'picture']],
            ['view', ['fullscreen', 'codeview', 'help']],
            ['height', ['height']]
        ],
        placeholder: 'write here...'
    });

    $('.summernote-product').summernote({
        fontNames: ['Arial', 'Arial Black', 'Comic Sans MS', 'Courier New', 'Times New Roman'],
        tabsize: 2,
        height: 200,
        toolbar: [
            ['style', ['style', 'bold', 'italic', 'underline', 'clear']],
            ['fontname', ['fontname']],
            ['fontsize', ['fontsize']],
            ['color', ['color']],
            ['height', ['height']]
        ],
        placeholder: 'write here...'
    });

    $('.float-number').autoNumeric('init', {
        aSep: ',',
        mDec: '0'
    });

    window.setTimeout(function () {
        $('.alert ').fadeTo(500, 0).slideUp(500, function () {
            $(this).remove();
        });
    }, 4000);

    if ($('.tb_display').length > 0) {
        /**
         * Button Table Display
         */
        new $.fn.dataTable.Buttons(_table, {
            buttons: [{
                extend: 'collection',
                className: 'btn btn-warning btn-sm btn-round ml-auto text-white',
                text: '<i class="fas fa-download fa-fw"></i> Export',
                autoClose: true,
                buttons: [{
                        extend: 'pdfHtml5',
                        text: '<i class="fas fa-file-pdf"></i> PDF',
                        titleAttr: 'Export to PDF',
                        title: '',
                        pageSize: 'A4',
                        exportOptions: {
                            columns: ':visible:not(:last-child)',
                        },
                    },
                    {
                        extend: 'csvHtml5',
                        text: '<i class="fas fa-file"></i> CSV',
                        titleAttr: 'Export to CSV',
                        title: '', //Set null value first row in file
                        exportOptions: {
                            columns: ':visible:not(:last-child)'
                        }
                    },
                    {
                        extend: 'excelHtml5',
                        text: '<i class="fas fa-file-excel"></i> Excel',
                        titleAttr: 'Export to Excel',
                        title: '', //Set null value first row in file
                        exportOptions: {
                            columns: ':visible:not(:last-child)'
                        }
                    }
                ]
            }]
        });

        _table.buttons().container()
            .appendTo($('#dt-button'));
    }

    $('.daterange').daterangepicker({
        autoUpdateInput: false,
        locale: {
            format: 'YYYY-MM-DD',
            cancelLabel: 'Clear'
        }
    });

    $('.daterange').on('apply.daterangepicker', function (ev, picker) {
        $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
    });

    $('.daterange').on('cancel.daterangepicker', function (ev, picker) {
        $(this).val('');
    });
});


$(document).ready(function (evt) {
    const form = $('.form');

    if (form.length > 0) {
        const formList = form.prop('classList');

        if (!arrContains('form_page', formList)) {
            let hidden_ID = form.find('input.id:hidden');
            ID = hidden_ID.val();

            const field = form.find('input, textarea, select');

            let url = SITE_URL + SHOW + ID;

            setSave = 'update';

            $.ajax({
                url: url,
                type: 'GET',
                async: false,
                cache: false,
                dataType: 'JSON',
                beforeSend: function () {
                    $('.save_form').attr('disabled', true);
                    loadingForm(form.find('form').attr('id'), 'facebook');
                },
                complete: function () {
                    $('.save_form').removeAttr('disabled');
                    hideLoadingForm(form.find('form').attr('id'));
                },
                success: function (result) {
                    cardTitle.html(capitalize(LAST_URL));

                    for (let i = 0; i < result.length; i++) {
                        let fieldInput = result[i].field;
                        let label = result[i].label;

                        for (let i = 0; i < formList.length; i++) {
                            if (formList[i].toLowerCase() === 'show' && fieldInput === 'title') {
                                modalTitle.html(capitalize(label));
                            } else if (fieldInput === 'title') {
                                cardTitle.html(capitalize(label));
                            }
                        }

                        for (let i = 0; i < field.length; i++) {
                            if (field[i].name === fieldInput) {
                                form.find('input:text[name=' + field[i].name + '], textarea[name=' + field[i].name + ']').val(label);

                                form.find('select[name=' + field[i].name + ']').val(label).change();
                            }
                        }
                    }
                }
            });
        }
    }
});

/**
 * Dropdown select for change data
 */
$('select').change(function (evt) {
    let target = $(evt.target);
    let value = '';

    const form = $(this).closest('form');
    let lengthFilter = $(this).closest('.card-filter').length;

    if (option.length == 0) {
        if (target.attr('id') === 'md_principal_id' || target.attr('name') === 'md_principal_id') {
            value = target.val();
            url = SITE_URL + '/getCategory';

            for (let i = 1; i <= 3; i++) {
                if (value != 0) {
                    form.find('select[name = "category' + i + '"]').empty();

                    $.ajax({
                        url: url,
                        type: 'POST',
                        data: {
                            principal: value,
                            level: i
                        },
                        cache: false,
                        dataType: 'JSON',
                        success: function (result) {
                            if (lengthFilter > 0) {
                                form.find('select[name = "category' + i + '"]').append('<option value="0">All Categories ' + (i > 1 ? i : '') + '</option>');
                            } else {
                                form.find('select[name = "category' + i + '"]').append('<option value="0">&nbsp;</option>');
                            }

                            if (result[0].success) {
                                let data = result[0].message;

                                $.each(data, function (idx, elem) {
                                    let category_id = elem.md_category_id;
                                    let category = elem.category;
                                    let category_en = elem.category_en;

                                    form.find('select[name = "category' + i + '"]').append('<option value="' + category_id + '">' + category_en + '</option>');
                                });
                            } else {
                                Swal.fire({
                                    type: 'error',
                                    title: result[0].message,
                                    showConfirmButton: false,
                                    timer: 1500
                                });
                            }
                        },
                        error: function (jqXHR, exception) {
                            showError(jqXHR, exception);
                        }
                    });
                } else {
                    form.find('select[name = "category' + i + '"]').empty();
                }
            }
        }
    } else {
        url = SITE_URL + '/getCategory';
        let data = option[option.length - 1];
        let field = data.fieldName;
        let id_category = data.value;

        value = $('.main-select').val();

        if (field.slice(0, -1) === 'category') {
            let index = field[field.length - 1];

            form.find('select[name =' + field + ']').empty();

            $.ajax({
                url: url,
                type: 'POST',
                data: {
                    principal: value,
                    level: index
                },
                cache: false,
                dataType: 'JSON',
                success: function (result) {
                    form.find('select[name =' + field + ']').append('<option value="0">&nbsp;</option>');

                    if (result[0].success) {
                        let data = result[0].message;

                        $.each(data, function (idx, elem) {
                            let category_id = elem.md_category_id;
                            let category_en = elem.category_en;

                            if (id_category == category_id) {
                                form.find('select[name =' + field + ']').append('<option value="' + category_id + '" selected>' + category_en + '</option>');
                            } else {
                                form.find('select[name =' + field + ']').append('<option value="' + category_id + '">' + category_en + '</option>');
                            }

                        });

                        // Set to empty array option
                        option = [];
                    } else {
                        Swal.fire({
                            type: 'error',
                            title: result[0].message,
                            showConfirmButton: false,
                            timer: 1500
                        });
                    }
                },
                error: function (jqXHR, exception) {
                    showError(jqXHR, exception);
                }
            });
        }
    }
});

/**
 * Function to merge Array Object
 * @param {*} arr1 
 * @param {*} arr2 
 * @param {*} arr3 
 * @param {*} arr4 
 * @param {*} arrID Access ID to retrieve edit data 
 * @returns 
 */
function mergeArrayObjects(arr1, arr2, arr3, arr4, arrID) {
    return arr1.map((item, i) => {
        if (item.row === arr2[i].row || item.row === arr3[i].row || item.row === arr4[i].row || item.row === arrID[i].row)
            return Object.assign({}, item, arr2[i], arr3[i], arr4[i], arrID[i]);
    })
}

/**
 * Remove duplicate array object
 * @param {*} arr 
 * @param {*} key object key to define when call function
 * @returns 
 */
function removeDuplicates(arr, key) {
    return [
        ...new Map(arr.map(item => [key(item), item])).values()
    ]
}

/**
 * Event checked checkbox table role
 */
$(document).on('click', 'input:checkbox', function () {
    const table = $(this).closest('table');
    const tr = $(this).closest('tr');
    let th = $(this).closest('th').index();
    let cell = $(this).parent().parent().parent().index()

    // Row start from 0
    let index = cell + 1;

    let dataNode;

    if ($(this).is(':checked')) {
        // Checked all checkbox based on index header
        if (th > 0)
            table.find('td:nth-child(' + index + ') input:checkbox').prop('checked', true);

        // Checked checkbox based on parent
        if (tr.hasClass('treetable-expanded') || tr.hasClass('treetable-collapsed')) {
            // Substring attribute data-node
            dataNode = tr.attr('data-node').substring(10);

            table.find('tr[data-pnode=treetable-parent-' + dataNode + '] td:nth-child(' + index + ') input:checkbox').prop('checked', true);
        }
    } else {
        // Unchecked all checkbox based on index header
        if (th > 0)
            table.find('td:nth-child(' + index + ') input:checkbox').prop('checked', false);

        // Unchecked checkbox based on parent
        if (tr.hasClass('treetable-expanded') || tr.hasClass('treetable-collapsed')) {
            // Substring attribute data-node
            dataNode = tr.attr('data-node').substring(10);

            table.find('tr[data-pnode=treetable-parent-' + dataNode + '] td:nth-child(' + index + ') input:checkbox').prop('checked', false);
        }
    }
});

/**
 * Function check exist role on the user based on session user
 * 
 * @param {*} role name
 * @returns 
 */
function checkExistUserRole(role) {
    let url = ADMIN_URL + 'role/' + 'getUserRoleName';
    let value;

    $.ajax({
        url: url,
        type: 'POST',
        data: {
            role_name: role
        },
        async: false,
        cache: false,
        dataType: 'JSON',
        success: function (result) {
            value = result;
        },
        error: function (jqXHR, exception) {
            showError(jqXHR, exception);
        }
    });

    return value;
}

_tableLine.on('change', 'input.active:checkbox', function (evt) {
    const tr = $(this).closest('tr');
    const field = tr.find('input, select');
    let className;

    if ($(this).is(':checked')) {
        for (let i = 0; i < field.length; i++) {
            if (field[i].name !== '') {
                className = field[i].className.split(/\s+/);

                tr.find('input:text[name=' + field[i].name + ']').removeAttr('readonly');

                if (field[i].type !== 'text' && !className.includes('active')) {
                    tr.find('input[name=' + field[i].name + '], select[name=' + field[i].name + ']').removeAttr('disabled');
                }
            }
        }
    } else {
        for (let i = 0; i < field.length; i++) {
            if (field[i].name !== '') {
                className = field[i].className.split(/\s+/);

                tr.find('input:text[name=' + field[i].name + ']').prop('readonly', true);

                if (field[i].type !== 'text' && !className.includes('active')) {
                    tr.find('input[name=' + field[i].name + '], select[name=' + field[i].name + ']').prop('disabled', true);
                }
            }
        }
    }
});