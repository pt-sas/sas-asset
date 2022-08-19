/**
 * Event Listener Quotation Detail
 */
_tableLine.on('change', 'input[name="isspare"]', function (evt) {
    const tr = _tableLine.$(this).closest('tr');

    if ($(this).is(':checked')) {
        tr.find('select[name="employee_id"]')
            .val(100130).change()
            .attr('disabled', true);

        if (tr.find('select[name="branch_id"]').length > 0) {
            tr.find('select[name="branch_id"]')
                .val(100001).change()
                .attr('disabled', true);
        }

        if (tr.find('select[name="division_id"]').length > 0) {
            tr.find('select[name="division_id"]')
                .val(100006).change()
                .attr('disabled', true);
        }
    } else {
        tr.find('select[name="employee_id"]')
            .val(null).change()
            .removeAttr('disabled');

        if (tr.find('select[name="branch_id"]').length > 0) {
            tr.find('select[name="branch_id"]')
                .val(null).change()
                .removeAttr('disabled');
        }

        if (tr.find('select[name="division_id"]').length > 0) {
            tr.find('select[name="division_id"]')
                .val(null).change()
                .removeAttr('disabled');
        }

        if (tr.find('select[name="room_id"]').length > 0) {
            tr.find('select[name="room_id"]').empty();
        }
    }
});

// update field input name line amount
_tableLine.on('keyup', 'input[name="qtyentered"], input[name="unitprice"]', function (evt) {
    const tr = _tableLine.$(this).closest('tr');

    let value = this.value;
    let lineamt, qty, unitprice = 0;

    const referenceField = tr.find('input[name="qtyentered"], input[name="unitprice"]');

    if (referenceField.length > 1) {
        if ($(this).attr('name') == 'unitprice') {
            qty = replaceRupiah(tr.find('input[name="qtyentered"]').val());
            value = replaceRupiah(this.value);

            lineamt = (value * qty);
        }

        if ($(this).attr('name') == 'qtyentered') {
            unitprice = replaceRupiah(tr.find('input[name="unitprice"]').val());

            lineamt = (value * unitprice);
        }

        tr.find('input[name="lineamt"]').val(formatRupiah(lineamt));
    }
});

$('#form_quotation').on('click', '#isinternaluse', function (evt) {
    const field = _tableLine.rows().nodes().to$().find('input');

    if ($(this).is(':checked')) {
        let listSup = getList('supplier/getList', 'name', 'SAS');
        let supOption = $("<option selected='selected'></option>").val(listSup[0].id).text(listSup[0].text);

        //! Set field md_supplier_id disabled
        $('#md_supplier_id').append(supOption)
            .change()
            .prop('disabled', true);
        fieldReadOnly.push('md_supplier_id');

        $.each(field, function (index, item) {
            const tr = $(this).closest('tr');

            //! Set value is zero and readonly
            tr.find('input:text[name="unitprice"]').val(0)
                .prop('readonly', true);

            tr.find('input:text[name="lineamt"]').val(0);
        });
    } else {
        //! Set field md_supplier_id remove attribute disabled
        $('#md_supplier_id').val(null)
            .change()
            .removeAttr('disabled');
        removeItems(fieldReadOnly, 'md_supplier_id');

        $.each(field, function (index, item) {
            const tr = $(this).closest('tr');

            //! Set value unitprice is null and remove attribute readonly
            tr.find('input:text[name="unitprice"]').val(null)
                .removeAttr('readonly');
        });
    }
});

/**
 * Event Listener Receipt Detail
 */
let prev;

$(document).ready(function (evt) {
    $('#trx_quotation_id').on('focus', function (e) {
        prev = this.value;
    }).change(function (evt) {
        const form = $(this).closest('form');
        const attrName = $(this).attr('name');

        let quotation_id = this.value;

        // create data
        if (quotation_id !== '' && setSave === 'add') {
            _tableLine.clear().draw(false);
            setReceiptDetail(form, attrName, quotation_id);
        }

        // update data
        $.each(option, function (idx, elem) {
            if (elem.fieldName === attrName && setSave !== 'add') {
                // Logic quotation_id is not null and current value not same value from database and datatable is not empty
                if (quotation_id !== '' && quotation_id != elem.value && _tableLine.data().any()) {
                    Swal.fire({
                        title: 'Delete?',
                        text: "Are you sure you want to change all data ? ",
                        type: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        confirmButtonText: 'Okay',
                        cancelButtonText: 'Close',
                        reverseButtons: true
                    }).then((data) => {
                        if (data.value) {
                            _tableLine.clear().draw(false);
                            setReceiptDetail(form, attrName, quotation_id, ID);
                        } else {
                            form.find('select[name=' + attrName + ']').val(elem.value).change();
                        }
                    });
                }

                // Logic quotation_id is not null and not same value from database and datatable is empty
                if (quotation_id !== '' && quotation_id != elem.value && !_tableLine.data().any()) {
                    setReceiptDetail(form, attrName, quotation_id);
                }

                // Logic prev data not same currentvalue and value from database and datatable is empty
                if (typeof prev !== 'undefined' && prev !== '' && quotation_id !== '' && prev != quotation_id && prev != elem.value && !_tableLine.data().any()) {
                    _tableLine.clear().draw(false);
                    setReceiptDetail(form, attrName, quotation_id);
                }
            }
        });

        // callback value to prev
        prev = this.value;
    });
});

_tableLine.on('change', 'select[name="employee_id"]', function (evt) {
    const tr = _tableLine.$(this).closest('tr');
    let employee_id = this.value;
    let isSpare = tr.find('input[name="isspare"]')[0];

    if (employee_id !== '') {
        // Column Branch
        getOption('branch', 'branch_id', tr, null, employee_id);
        // Column Division
        getOption('division', 'division_id', tr, null, employee_id);

        // Column Room
        if (isSpare.checked) {
            getOption('room', 'room_id', tr, null, 'IT'); // Based on division IT
        } else {
            getOption('room', 'room_id', tr, null, employee_id);
        }
    }
});

// Function for getter datatable from quotation
function setReceiptDetail(form, fieldName, id, receipt_id = 0) {
    const field = form.find('select, textarea');
    let url = SITE_URL + '/getDetailQuotation';

    $.ajax({
        url: url,
        type: 'POST',
        data: {
            id: id,
            receipt_id: receipt_id
        },
        cache: false,
        dataType: 'JSON',
        beforeSend: function () {
            $('.x_form').prop('disabled', true);
            $('.close_form').prop('disabled', true);
            loadingForm(form.prop('id'), 'facebook');
        },
        complete: function () {
            $('.x_form').removeAttr('disabled');
            $('.close_form').removeAttr('disabled');
            hideLoadingForm(form.prop('id'));
        },
        success: function (result) {
            if (result[0].success) {
                let arrMsg = result[0].message;

                if (arrMsg.header) {
                    let header = arrMsg.header;

                    for (let i = 0; i < header.length; i++) {
                        let fieldInput = header[i].field;
                        let label = header[i].label;

                        for (let i = 0; i < field.length; i++) {
                            // To set value on the field from quotation
                            if (field[i].name !== '' && field[i].name === fieldInput) {
                                if (field[i].type === 'select-one' && fieldName !== fieldInput) {
                                    form.find('select[name=' + field[i].name + ']').val(label).change();
                                }

                                form.find('textarea[name=' + field[i].name + ']').val(label);
                            }
                        }
                    }
                }

                if (arrMsg.line) {
                    if (form.find('table.tb_displayline').length > 0) {
                        let line = JSON.parse(arrMsg.line);

                        $.each(line, function (idx, elem) {
                            _tableLine.row.add(elem).draw(false);
                        });

                        const input = _tableLine.rows().nodes().to$().find('input, select');

                        $.each(input, function (idx, item) {
                            const tr = $(item).closest('tr');

                            if ($(item).attr('name') === 'isspare') {
                                if (item.checked) {
                                    // Column Branch
                                    getOption('branch', 'branch_id', tr, 100001);
                                    // Column Division
                                    getOption('division', 'division_id', tr, 100006);
                                    // Column Room
                                    getOption('room', 'room_id', tr, null, 'IT');
                                } else {
                                    let employee_id = tr.find('select[name="employee_id"]').val();
                                    // Column Branch
                                    getOption('branch', 'branch_id', tr, null, employee_id);
                                    // Column Division
                                    getOption('division', 'division_id', tr, null, employee_id);
                                    // Column Room
                                    getOption('room', 'room_id', tr, null, employee_id);
                                }
                            }
                        });
                    }
                }
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
}

function getOption(controller, field, tr, selected_id, ref_id = null) {
    let url = ADMIN_URL + controller + '/getList';

    tr.find('select[name =' + field + ']').empty();

    $.ajax({
        url: url,
        type: 'POST',
        cache: false,
        data: {
            reference: ref_id
        },
        dataType: 'JSON',
        success: function (result) {
            tr.find('select[name =' + field + ']').append('<option value=""></option>');

            if (!result[0].error) {
                $.each(result, function (idx, item) {
                    // Check property key isset and key equal id or set selected equal id
                    if (typeof item.key !== 'undefined' && item.key == item.id || selected_id == item.id) {
                        tr.find('select[name =' + field + ']').append('<option value="' + item.id + '" selected>' + item.text + '</option>')
                        tr.find('select[name =' + field + ']').attr('value', item.id);
                    } else {
                        tr.find('select[name =' + field + ']').append('<option value="' + item.id + '">' + item.text + '</option>');
                    }
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
}

/**
 * MASTER DATA EMPLOYEE
 */
$('#form_employee').on('change', '#md_branch_id', function (evt) {
    let url = ADMIN_URL + 'room' + '/getList';
    let value = this.value;

    $('#md_room_id').empty();

    if (value !== '') {
        $.ajax({
            url: url,
            type: 'POST',
            cache: false,
            data: {
                reference: value,
                key: 'branch'
            },
            dataType: 'JSON',
            success: function (result) {
                $('#md_room_id').append('<option value=""></option>');

                let md_room_id = 0;

                $.each(option, function (i, item) {
                    if (item.fieldName == 'md_room_id')
                        md_room_id = item.value;
                });

                if (!result[0].error) {
                    $.each(result, function (idx, item) {
                        if (md_room_id == item.id) {
                            $('#md_room_id').append('<option value="' + item.id + '" selected>' + item.text + '</option>');
                        } else {
                            $('#md_room_id').append('<option value="' + item.id + '">' + item.text + '</option>');
                        }
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
    }
});

/**
 * Event Listener Movement Detail
 */
_tableLine.on('change', 'select[name="assetcode"]', function (evt) {
    const tr = _tableLine.$(this).closest('tr');

    let url = ADMIN_URL + 'inventory' + '/getAssetDetail';
    let value = this.value;

    $.ajax({
        url: url,
        type: 'POST',
        cache: false,
        data: {
            assetcode: value
        },
        dataType: 'JSON',
        success: function (result) {
            if (result[0].success) {
                $.each(result[0].message, function (idx, item) {
                    if (tr.find('select[name="product_id"]').length > 0) {
                        tr.find('select[name="product_id"]').val(item.md_product_id).change();
                    }

                    if (tr.find('select[name="employee_from"]').length > 0) {
                        tr.find('select[name="employee_from"]').val(item.md_employee_id).change();
                    }

                    if (tr.find('select[name="branch_from"]').length > 0) {
                        tr.find('select[name="branch_from"]').val(item.md_branch_id).change();
                    }

                    if (tr.find('select[name="division_from"]').length > 0) {
                        tr.find('select[name="division_from"]').val(item.md_division_id).change();
                    }

                    if (tr.find('select[name="room_from"]').length > 0) {
                        tr.find('select[name="room_from"]').val(item.md_room_id).change();
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

// Event change field Status
_tableLine.on('change', 'select[name="status_id"]', function (evt) {
    const tr = _tableLine.$(this).closest('tr');
    let value = $(this).find('option:selected').text();

    if (value === 'RUSAK') {
        tr.find('select[name="employee_to"]')
            .val(100130).change()
            .attr('disabled', true);
    } else {
        tr.find('select[name="employee_to"]')
            .val(null).change()
            .removeAttr('disabled');
    }
});

// Event change field Employee To
_tableLine.on('change', 'select[name="employee_to"]', function (evt) {
    const tr = _tableLine.$(this).closest('tr');
    let value = $(this).find('option:selected').text();
    let status = tr.find('select[name="status_id"] option:selected').text();
    let employee_id = this.value;

    if (value === 'IT') {
        // Column Branch
        getOption('branch', 'branch_to', tr, 100001);
        // Column Division
        getOption('division', 'division_to', tr, 100006);

        if (status === 'RUSAK') {
            // Column Room
            getOption('room', 'room_to', tr, 100041);
            tr.find('select[name="room_to"]').attr('disabled', true);
        }

        if (status === 'BAGUS') {
            // Column Room
            getOption('room', 'room_to', tr, null, 'IT');
            tr.find('select[name="room_to"]').removeAttr('disabled');
        }
    } else {
        // Column Branch
        getOption('branch', 'branch_to', tr, null, employee_id);
        // Column Division
        getOption('division', 'division_to', tr, null, employee_id);
        // Column Room
        getOption('room', 'room_to', tr, null, employee_id);
        tr.find('select[name="room_to"]').removeAttr('disabled');
        // tr.find('select[name="room_to"]').attr('disabled', true);
    }

    if (value === '') {
        tr.find('select[name="branch_to"]').val(null).change();
        tr.find('select[name="division_to"]').val(null).change();
        tr.find('select[name="room_to"]').val(null).change();
    }
});

/**
 * Event Menu Inventory
 */
// Form Inventory
$('#form_inventory').on('change', '#md_branch_id', function (evt) {
    let url = ADMIN_URL + 'room' + '/getList';
    let value = this.value;

    $('#md_room_id').empty();

    if (value !== '') {
        $.ajax({
            url: url,
            type: 'POST',
            cache: false,
            data: {
                reference: value,
                key: 'all'
            },
            beforeSend: function () {
                $('.save_form').attr('disabled', true);
                $('.close_form').attr('disabled', true);
                loadingForm('form_inventory', 'pulse');
            },
            complete: function () {
                $('.save_form').removeAttr('disabled');
                $('.close_form').removeAttr('disabled');
                hideLoadingForm('form_inventory');
            },
            dataType: 'JSON',
            success: function (result) {
                $('#md_room_id').append('<option value=""></option>');

                let md_room_id = 0;

                $.each(option, function (i, item) {
                    if (item.fieldName == 'md_room_id')
                        md_room_id = item.value;
                });

                if (!result[0].error) {
                    $.each(result, function (idx, item) {
                        if (md_room_id == item.id) {
                            $('#md_room_id').append('<option value="' + item.id + '" selected>' + item.text + '</option>');
                        } else {
                            $('#md_room_id').append('<option value="' + item.id + '">' + item.text + '</option>');
                        }
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
    }
});

$('#form_inventory').on('change', '#md_room_id', function (evt) {
    let url = ADMIN_URL + 'employee' + '/getList';
    let value = this.value;
    let md_room_id = 0;
    let md_branch_id = $('#md_branch_id option:selected').val();

    $.each(option, function (i, item) {
        if (item.fieldName == 'md_room_id') {
            md_room_id = item.value;
        }
    });

    $('#md_employee_id').empty();

    if (value !== '' || md_room_id !== '') {
        let refValue = value !== '' ? value : md_room_id;

        $.ajax({
            url: url,
            type: 'POST',
            cache: false,
            data: {
                reference: refValue,
                branch: md_branch_id
            },
            beforeSend: function () {
                $('.save_form').attr('disabled', true);
                $('.close_form').attr('disabled', true);
                loadingForm('form_inventory', 'pulse');
            },
            complete: function () {
                $('.save_form').removeAttr('disabled');
                $('.close_form').removeAttr('disabled');
                hideLoadingForm('form_inventory');
            },
            dataType: 'JSON',
            success: function (result) {
                $('#md_employee_id').append('<option value=""></option>');

                let md_employee_id = 0;

                $.each(option, function (i, item) {
                    if (item.fieldName == 'md_employee_id') {
                        md_employee_id = item.value;
                    }
                });

                if (!result[0].error) {
                    $.each(result, function (idx, item) {
                        // Check employee from db and event first change edit is null value or event change get value
                        if (md_employee_id == item.id && value == '' || md_employee_id == item.id && value == md_room_id) {
                            $('#md_employee_id').append('<option value="' + item.id + '" selected>' + item.text + '</option>');
                        } else {
                            $('#md_employee_id').append('<option value="' + item.id + '">' + item.text + '</option>');
                        }
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
    }
});

// Form Filter
$(document).ready(function (e) {
    $('.select-product').select2({
        placeholder: 'Select an option',
        width: '100%',
        theme: 'bootstrap',
        allowClear: true,
        minimumInputLength: 3,
        ajax: {
            dataType: 'JSON',
            url: ADMIN_URL + 'product/getList',
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

    $('.select-branch').select2({
        placeholder: 'Select an option',
        width: '100%',
        theme: 'bootstrap',
        allowClear: true,
        ajax: {
            dataType: 'JSON',
            url: ADMIN_URL + 'branch/getList',
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

    $('.select-employee').select2({
        placeholder: 'Select an option',
        width: '100%',
        theme: 'bootstrap',
        allowClear: true,
        minimumInputLength: 3,
        ajax: {
            dataType: 'JSON',
            url: ADMIN_URL + 'employee/getList',
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
});

$('#filter_inventory').on('change', '[name="md_branch_id"]', function (evt) {
    let url = ADMIN_URL + 'room' + '/getList';
    let value = this.value;

    $('[name="md_room_id"]').empty();

    // Set condition when clear or value zero
    if (value !== '' && value !== '0') {
        $.ajax({
            url: url,
            type: 'POST',
            cache: false,
            data: {
                reference: value,
                key: 'all'
            },
            dataType: 'JSON',
            success: function (result) {
                $('[name="md_room_id"]').append('<option value=""></option>');

                if (!result[0].error) {
                    $.each(result, function (idx, item) {
                        $('[name="md_room_id"]').append('<option value="' + item.id + '">' + item.text + '</option>');
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
    }
});

$('#form_sequence').on('click', '#isautosequence, #isgassetlevelsequence, #iscategorylevelsequence, #startnewyear', function (evt) {
    const target = $(evt.target);
    const form = target.closest('form');

    //? Condition field checked and contain attribute checked-hide-field
    if ($(this).attr('checked-hide-field')) {
        let fields = $(this).attr('checked-hide-field').split(',').map(element => element.trim());

        if ($(this).is(':checked')) {
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
    }

    //? Condition field checked and contain attribute checked-show-field
    if ($(this).attr('checked-show-field')) {
        let fields = $(this).attr('checked-show-field').split(',').map(element => element.trim());

        if ($(this).is(':checked')) {
            for (let i = 0; i < fields.length; i++) {
                let formGroup = form.find('input[name=' + fields[i] + '], textarea[name=' + fields[i] + '], select[name=' + fields[i] + ']').closest('.form-group, .form-check');
                formGroup.show();
            }
        } else {
            for (let i = 0; i < fields.length; i++) {
                let formGroup = form.find('input[name=' + fields[i] + '], textarea[name=' + fields[i] + '], select[name=' + fields[i] + ']').closest('.form-group, .form-check');
                formGroup.hide();
            }
        }
    }
});

$('.upload_form').click(function (evt) {
    console.log(evt)
    $('.modal_upload').modal({
        backdrop: 'static',
        keyboard: false
    });
    Scrollmodal();
})

$('.custom-file-input').change(function (e) {
    var name = document.getElementById("customFileInput").files[0].name;
    var nextSibling = e.target.nextElementSibling
    nextSibling.innerText = name
});

$('.save_upload').click(function (evt) {
    var fd = new FormData();

    fd.append('file', $('#customFileInput')[0].files[0])
    $.ajax({
        url: SITE_URL + '/import',
        type: "POST",
        data: fd,
        processData: false, // important
        contentType: false, // important
        dataType: "JSON",
        success: function (response) {
            console.log(response)
        }
    });

    // console.log(fd)
})

_tableOpname = $('.tb_opname').DataTable({
    'ordering': false,
    'autoWidth': true,
    'lengthChange': false,
    'searching': false,
    'paging': false,
    'info': false
});

function showTable(url) {
    _tableOpname.ajax.url(url).load().draw(true);
    _tableOpname.on('xhr', function (r) {
        var json = _tableOpname.ajax.json();
        // var total = json['total'];
        // arrCart.push(json);

        // if (total > 0) {
        //     totalAmount = total;
        //     msgGrandTotal.html(formatRupiah(totalAmount));
        // } else {
        //     totalAmount = 0;
        //     msgGrandTotal.html(formatRupiah(totalAmount));
        // }
    });
}

$(document).ready(function () {
    // $('#form_opname').on('focus', '#assetcode')

    // let url = SITE_URL + '/cek';
    // _tableOpname.ajax.url(url).load().draw(true);
    // _tableOpname.on('xhr', function (r) {
    //     var json = _tableOpname.ajax.json();
    //     // var total = json['total'];
    //     // arrCart.push(json);

    //     // if (total > 0) {
    //     //     totalAmount = total;
    //     //     msgGrandTotal.html(formatRupiah(totalAmount));
    //     // } else {
    //     //     totalAmount = 0;
    //     //     msgGrandTotal.html(formatRupiah(totalAmount));
    //     // }
    // });
    // _tableOpname.ajax.url(SITE_URL + '/cek').load().draw(true);


    var availableTags = [
        'KM/MO/1908/06',
        'KM/MO/1908/05',
        'KM/MO/1908/08',
        'KM/MO/1908/07',
        'KM/MO/1908/03',
        'KM/MO/1908/09',
        'MN/CT/1910/01',
        'KM/UP/1911/01',
        'KM/UP/1911/03',
        'KM/UP/1911/02',
        'KM/UP/1511/01',
        'KM/UP/2008/01',
        'KM/CP/2101/01',
        'KM/CP/2202/01',
        'KM/MD/1409/01',
        'KM/MD/1508/01',
        'KM/WR/2007/01',
        'KM/PT/1207/01',
        'KM/SC/0903/01',
        'KM/PT/1012/03',
        'KM/PT/1012/02',
        'KM/SC/1010/01',
        'KM/PT/1211/02',
        'KM/PT/1211/01',
        'KM/PT/1312/01',
        'KM/PT/1401/03',
        'KM/SW/1410/02',
        'KM/SW/1410/01',
        'KM/SW/1508/01',
        'KM/NB/1207/01',
        'KM/NB/1010/03',
        'KM/NB/0912/03',
        'KM/NB/1010/01',
        'KM/CP/1404/02',
        'KM/CP/1307/01',
        'KM/CP/1612/03',
        'KM/MT/1011/02',
        'KM/CP/1611/02',
        'KM/CP/1109/02',
        'KM/CP/1107/03',
        'KM/CP/0903/01',
        'KM/CP/1002/07',
        'KM/CP/1407/02',
        'KM/CP/2103/01',
        'KM/CP/1107/02',
        'KM/CP/1411/01',
        'KM/CP/1304/01',
        'KM/CP/1701/01',
        'KM/CP/1502/01',
        'Unidentified_001',
        'KM/CP/1611/01',
        'KM/CP/1609/01',
        'KM/AL/0408/02',
        'KM/CP/1011/01',
        'KM/CP/1401/01',
        'KM/CP/1409/01',
        'KM/NB/1402/02',
        'KM/KB/1002/09',
        'KM/CP/1105/01',
        'KM/MT/1212/01',
        'KM/MT/1105/01',
        'KM/SW/1104/01',
        'KM/SW/1107/01',
        'KM/SW/0310/01',
        'KM/CP/2202/03',
        'KM/SW/1204/01',
        'Unidentified_002',
        'KM/SW/1509/02',
        'KM/UH/1002/01',
        'KM/PT/1910/10',
        'KM/PT/1503/09',
        'KM/PT/2003/02',
        'KM/PT/1812/06',
        'KM/PT/1209/01',
        'KM/PT/1809/02',
        'KM/PT/2004/03',
        'KM/PT/1812/04',
        'KM/PT/2109/03',
        'KM/PT/2012/02',
        'KM/PT/1205/01',
        'KM/PT/1812/01',
        'KM/PT/1907/03',
        'KM/PT/1911/01',
        'KM/PT/1910/03',
        'KM/PT/2002/01',
        'KM/PT/1212/01',
        'KM/PT/1910/04',
        'KM/PT/1910/05',
        'KM/PT/2110/03',
        'KM/PT/1907/02',
        'KM/PT/2001/03',
        'Unidentified_003',
        'KM/PT/1812/03',
        'KM/PJ/2112/02',
        'MN/PJ/0506/01',
        'KM/PJ/2112/01',
        'KM/PT/1503/11',
        'KM/PT/2003/01',
        'KM/PT/2004/01',
        'KM/PT/1503/07',
        'KM/PT/2001/04',
        'KM/PT/2001/01',
        'KM/PT/2004/02',
        'KM/PT/2110/01',
        'KM/PT/2012/01',
        'KM/PT/1910/01',
        'KM/PT/2009/01',
        'KM/PT/1812/05',
        'KM/PT/1807/01',
        'KM/PT/1408/03',
        'KM/PT/1910/06',
        'KM/PT/2101/02',
        'KM/PT/1704/01',
        'KM/PT/1812/02',
        'KM/PT/1907/01',
        'KM/PT/2109/01',
        'KM/PT/1904/02',
        'KM/PT/1910/02',
        'KM/PT/2001/02',
        'KM/PT/1410/01',
        'KM/MO/1703/01',
        'KM/NP/0509/01',
        'MN/CT/2003/02',
        'MN/CT/2001/06',
        'KM/NB/1901/01',
        'KM/NB/2011/02',
        'KM/NB/2105/01',
        'KM/PT/0802/01',
        'KM/NB/1803/10',
        'KM/NB/1511/01',
        'KM/NB/1606/01',
        'KM/NB/1412/01',
        'KM/NB/1710/02',
        'KM/NB/1803/02',
        'KM/NB/1708/01',
        'KM/KB/1011/01',
        'KM/NB/1209/02',
        'KM/NB/1803/09',
        'KM/PT/1910/07',
        'KM/NB/1803/06',
        'KM/NB/1803/08',
        'KM/PT/0803/01',
        'KM/NB/2008/01',
        'KM/NB/1803/01',
        'KM/PT/2011/01',
        'KM/NB/1101/01',
        'KM/NB/1803/12',
        'KM/NB/1803/04',
        'KM/NB/1305/01',
        'KM/PT/2104/01',
        'KM/NB/1608/01',
        'KM/PT/1507/01',
        'KM/PT/1505/01',
        'KM/NB/1803/11',
        'KM/PT/2012/03',
        'KM/PT/2110/02',
        'KM/NB/1905/01',
        'KM/PT/1512/01',
        'KM/NB/2002/01',
        'KM/PT/1910/08',
        'KM/NB/1803/07',
        'KM/PT/1204/01',
        'KM/PT/1910/09',
        'KM/PT/1012/05',
        'KM/NB/1408/03',
        'KM/NB/2001/02',
        'KM/NB/1203/02',
        'KM/NB/1710/01',
        'KM/NB/2003/01',
        'KM/PT/1001/01',
        'KM/NB/2011/01',
        'KM/PT/1104/01',
        'KM/NB/2001/01',
        'KM/PT/1001/03',
        'KM/NB/1803/03',
        'KM/NB/1804/01',
        'KM/PT/1404/01',
        'KM/NB/1402/01',
        'KM/NB/2103/01',
        'KM/PT/1206/01',
        'KM/NB/1910/01',
        'KM/NB/2103/02',
        'KM/CP/1003/03',
        'KM/CP/1606/01',
        'KM/MO/1010/02',
        'KM/NB/1004/02',
        'KM/NB/1408/02',
        'KM/SW/1810/01',
        'KM/SW/1810/02',
        'KM/SW/1810/03',
        'KM/SW/1810/04',
        'KM/PT/2109/02',
        'Unidentified_004',
        'KM/NB/1203/01',
        'KM/NB/1203/03',
        'KM/SW/1910/01',
        'KM/SW/2007/01',
        'KM/SW/2104/01',
        'KM/MD/2107/01',
        'KM/UP/1908/01',
        'KM/PJ/1910/01',
        'KM/PJ/1908/02',
        'KM/PJ/1908/01',
        'MN/PJ/1409/01',
        'MN/PJ/1604/01',
        'MN/PJ/2101/01',
        'MN/PJ/1608/01',
        'KM/LT/1812/01',
        'KM/UP/2110/01',
        'KM/UP/2110/02',
        'KM/UP/2110/03',
        'KM/UP/2110/06',
        'KM/UP/2110/04',
        'KM/UP/2110/05',
        'KM/UP/2110/07',
        'KM/UP/2110/08',
        'KM/NB/2012/01',
        'KM/NB/2107/01',
        'KM/NB/2107/02',
        'KM/MO/0801/01',
        'KM/MT/1910/03',
        'KM/MT/1910/02',
        'KM/MT/2007/01',
        'KM/MT/1910/04',
        'KM/MT/1904/02',
        'KM/MT/1410/03',
        'KM/MT/2007/02',
        'KM/MT/1905/02',
        'KM/MT/2006/03',
        'KM/MT/1911/01',
        'KM/MT/1905/01',
        'KM/MT/1910/09',
        'KM/MT/1910/06',
        'KM/MT/1911/02',
        'KM/MT/1910/07',
        'KM/MT/1906/01',
        'KM/MT/2012/02',
        'KM/MT/0603/01',
        'KM/MT/2006/01',
        'KM/MT/1910/05',
        'KM/MT/1910/08',
        'Unidentified_005',
        'KM/MT/2006/02',
        'KM/MT/0805/01',
        'KM/MT/2003/02',
        'KM/MT/1910/01',
        'KM/WR/1910/01',
        'KM/WR/1910/02',
        'KM/WR/1606/02',
        'KM/SW/0610/01',
        'KM/WR/1703/01',
        'KM/SW/1205/01',
        'KM/WR/1606/01',
        'KM/WR/1001/01',
        'KM/MO/1002/07',
        'KM/MO/1611/06',
        'KM/MO/2004/04',
        'KM/KB/1910/02',
        'KM/MO/1910/02',
        'KM/KB/2004/06',
        'KM/MO/2007/03',
        'KM/KB/1408/03',
        'KM/MO/1908/02',
        'KM/KB/2109/01',
        'KM/MO/2108/09',
        'KM/KB/2109/02',
        'KM/KB/0604/01',
        'KM/MO/2002/04',
        'KM/KB/1003/05',
        'KM/KB/1102/01',
        'KM/MO/1810/05',
        'KM/KB/1910/04',
        'KM/MO/1910/04',
        'KM/KB/1201/01',
        'KM/MO/1110/04',
        'KM/MO/1606/02',
        'KM/KB/2011/04',
        'KM/KB/1509/01',
        'KM/MO/1209/03',
        'KM/KB/1302/01',
        'KM/MO/2001/01',
        'KM/KB/0501/01',
        'KM/MO/2001/04',
        'KM/MO/2106/02',
        'KM/KB/1509/29',
        'KM/KB/0806/01',
        'KM/KB/1209/01',
        'KM/MO/1803/01',
        'KM/KB/1001/02',
        'Unidentified_006',
        'KM/MO/1302/05',
        'KM/MO/1708/01',
        'KM/KB/1107/02',
        'KM/MO/1505/02',
        'KM/KB/1408/01',
        'KM/KB/1002/06',
        'KM/KB/2011/02',
        'KM/MO/1709/01',
        'KM/MO/1911/04',
        'KM/MO/1912/02',
        'KM/MO/1902/02',
        'KM/KB/1509/13',
        'KM/KB/1002/04',
        'KM/MO/1911/05',
        'KM/KB/0801/01',
        'KM/MO/1012/02',
        'KM/KB/0503/01',
        'KM/MO/1705/04',
        'KM/KB/1302/03',
        'KM/MO/1902/01',
        'KM/KB/2004/07',
        'KM/MO/1909/02',
        'KM/MO/1911/13',
        'KM/KB/1910/01',
        'KM/MO/1302/06',
        'KM/KB/1110/02',
        'KM/MO/1005/01',
        'KM/MO/1911/08',
        'KM/KB/0710/01',
        'KM/MO/2011/03',
        'KM/KB/2004/04',
        'KM/KB/1910/09',
        'KM/MO/1910/09',
        'KM/KB/1111/01',
        'KM/MO/1705/03',
        'KM/KB/1709/02',
        'KM/MO/1909/01',
        'KM/KB/2004/08',
        'KM/KB/1910/06',
        'KM/MO/1910/06',
        'KM/KB/1911/03',
        'KM/MO/1712/01',
        'KM/MO/1910/01',
        'KM/KB/1408/02',
        'KM/KB/2004/02',
        'KM/MO/1604/02',
        'KM/MO/2109/01',
        'KM/KB/2002/01',
        'KM/KB/0611/01',
        'KM/KB/1211/01',
        'KM/KB/2002/02',
        'KM/MO/1104/01',
        'KM/MO/1107/03',
        'KM/MO/1509/18',
        'KM/KB/1001/03',
        'KM/MO/1803/05',
        'Unidentified_007',
        'Unidentified_008',
        'KM/MO/1911/12',
        'KM/MO/1408/01',
        'KM/KB/1408/02.V1',
        'KM/KB/1910/07',
        'KM/MO/1910/07',
        'KM/KB/2002/03',
        'KM/MO/1907/04',
        'KM/KB/1906/01',
        'KM/MO/1906/01',
        'KM/MO/1909/05',
        'KM/KB/2011/03',
        'KM/MO/2012/03',
        'KM/MK/2104/01',
        'KM/MO/2004/01',
        'KM/MO/2101/01',
        'KM/KB/1003/02',
        'KM/KB/1003/06',
        'KM/MO/1807/05',
        'KM/MO/2103/01',
        'KM/KB/2103/01',
        'KM/KB/1010/05',
        'KM/MO/2004/03',
        'Unidentified_009',
        'KM/KB/1911/02',
        'KM/KB/0603/02',
        'KM/KB/0701/02',
        'KM/KB/1107/03',
        'KM/KB/1107/05',
        'KM/KB/1107/06',
        'KM/MO/1911/03',
        'KM/MO/0603/02',
        'KM/KB/1012/02',
        'KM/MO/1001/04',
        'KM/KB/1911/01',
        'KM/MO/1908/04',
        'KM/KB/2004/05',
        'KM/MO/1806/01',
        'KM/MO/1806/02',
        'KM/KB/2109/03',
        'KM/MO/1908/10',
        'KM/KB/1202/03',
        'KM/KB/1003/03',
        'KM/KB/1010/02',
        'KM/KB/1109/01',
        'KM/MO/1905/04',
        'KM/NP/1209/01',
        'KM/KB/0604/02',
        'KM/KB/1107/01',
        'KM/MO/1905/02',
        'KM/KB/1008/03',
        'KM/MO/1505/03',
        'KM/KB/1011/08',
        'KM/MO/1807/04',
        'KM/KB/1910/03',
        'KM/MO/1910/03',
        'KM/KB/1003/01',
        'KM/MO/2004/02',
        'KM/KB/1206/01',
        'KM/MO/1902/05',
        'KM/MO/1807/01',
        'KM/KB/1010/04',
        'KM/MO/1606/02.V1',
        'KM/KB/0601/02',
        'KM/KB/1001/01',
        'KM/MO/1909/04',
        'KM/KB/1107/04',
        'KM/MO/1911/02',
        'KM/MO/1902/03',
        'KM/MO/1505/01',
        'KM/KB/1910/05',
        'KM/MO/1910/05',
        'KM/MO/2007/01',
        'KM/MO/2011/04',
        'KM/KB/2011/01',
        'KM/KB/0509/01',
        'KM/KB/1111/02',
        'KM/MO/1807/02',
        'KM/KB/1909/01',
        'KM/MO/2106/01',
        'KM/KB/1012/03',
        'KM/MO/1810/04',
        'KM/KB/1910/08',
        'KM/MO/1910/08',
        'KM/MO/1606/03',
        'KM/KB/1010/03',
        'KM/KB/1010/01',
        'KM/MO/1505/04',
        'KM/MO/1709/03',
        'KM/KB/2103/03',
        'KM/MO/1611/07',
        'KM/MO/2109/02',
        'KM/KB/2004/01',
        'KM/MO/1608/03',
        'KM/KB/0806/04',
        'KM/MO/2011/01',
        'KM/MO/1307/03',
        'KM/CP/2202/02',
        'KM/KB/0904/01',
        'KM/MO/2002/05',
        'Unidentified_019',
        'KM/MO/2011/05',
        'KM/KB/2004/03',
        'KM/MO/1907/05',
        'KM/MO/2007/04',
        'KM/KB/2104/03',
        'KM/MO/2104/03',
        'KM/KB/0812/02',
        'KM/MO/0611/01',
        'KM/MO/2001/02',
        'KM/KB/0505/01',
        'KM/KB/1709/01',
        'KM/MO/2109/03',
        'KM/MO/2008/03',
        'KM/KB/1202/02',
        'KM/MO/1211/02',
        'KM/KB/1005/01',
        'KM/MO/1501/04',
        'KM/KB/1810/01',
        'KM/MO/1905/05',
        'KM/KB/1302/07',
        'KM/MO/2008/01',
        'KM/KB/2001/02',
        'KM/MO/2007/02',
        'KM/KB/1911/04',
        'KM/MO/1911/14',
        'KM/KB/2106/01',
        'KM/MO/0412/03',
        'KM/KB/1302/02',
        'KM/MO/1909/03',
        'Unidentified_010',
        'KM/KB/0708/01',
        'KM/KB/2111/01',
        'KM/KB/2111/02',
        'KM/KB/2112/01',
        'KM/KB/2112/02',
        'KM/KB/2112/03',
        'KM/MO/2111/01',
        'KM/MO/2111/02',
        'KM/MO/2111/03',
        'KM/MO/2111/04',
        'KM/MO/2112/01',
        'MN/CT/2110/01',
        'MN/CT/2110/02',
        'KM/WR/2006/01',
        'KM/MR/1503/01',
        'KM/MR/1403/02',
        'KM/MR/1403/01',
        'KM/MR/1408/01',
        'KM/MR/1804/01',
        'KM/WR/1307/01',
        'KM/WR/1404/01',
        'KM/WR/1404/02',
        'KM/MO/2012/04',
        'KM/KB/1102/04',
        'KM/NC/1011/01.2',
        'KM/NC/1109/01.4',
        'KM/NC/1102/02.2',
        'KM/NC/1108/02',
        'KM/NC/1007/01.3',
        'KM/MD/1404/01',
        'MN/PJ/1001/02',
        'KM/NP/1010/01',
        'KM/UH/2009/02',
        'KM/UH/2010/02',
        'KM/UH/2009/01',
        'KM/UH/2010/01',
        'KM/UH/2109/02',
        'KM/UH/2109/03',
        'KM/UH/2109/04',
        'KM/MT/1107/01',
        'KM/MT/1011/04',
        'KM/MT/1110/01',
        'KM/MT/1112/01',
        'KM/MT/1012/01',
        'KM/MT/1101/02',
        'KM/MT/1110/02',
        'KM/MT/1103/02',
        'KM/MT/1101/03',
        'KM/MT/1202/05',
        'KM/MT/1012/04',
        'KM/MT/1012/02',
        'KM/MT/1202/02',
        'KM/MT/1102/01',
        'KM/MT/1202/06',
        'KM/MT/1110/07',
        'KM/MT/1102/05',
        'KM/MT/1110/03',
        'KM/MT/1301/01',
        'KM/MT/1011/03',
        'KM/MT/1102/02',
        'KM/MT/1110/05',
        'KM/MT/1509/14',
        'KM/MT/1111/02',
        'KM/MT/1011/01',
        'KM/MT/1110/06',
        'KM/FD/1603/01',
        'KM/MT/1107/02',
        'KM/MT/1110/11',
        'KM/MT/1012/03',
        'KM/MT/1202/04',
        'KM/MT/1102/03',
        'KM/MT/1110/10',
        'KM/MT/1102/07',
        'KM/MT/1509/15',
        'KM/MT/1202/03',
        'KM/MT/1007/01',
        'KM/MT/1002/01',
        'KM/MT/0912/01',
        'KM/MT/1503/02',
        'KM/MT/1504/01',
        'KM/MT/1001/01',
        'KM/MT/1302/02',
        'KM/MT/1004/03',
        'KM/MT/1002/04',
        'KM/MT/1004/02',
        'KM/MT/1209/01',
        'KM/MT/1010/01',
        'KM/MT/1010/02',
        'Unidentified_011',
        'KM/MT/1006/01',
        'KM/MT/1008/03',
        'KM/MT/1001/06',
        'KM/MO/1001/08',
        'KM/MT/1001/08',
        'KM/MT/1703/03',
        'KM/MT/1002/05',
        'KM/MT/0911/01',
        'KM/MO/0701/01',
        'KM/MT/1001/03',
        'KM/MT/1001/05',
        'KM/MT/1410/02',
        'KM/MT/1101/01',
        'KM/MT/1001/07',
        'KM/MT/1004/01',
        'KM/MT/1003/02',
        'KM/MT/1503/01',
        'KM/MT/1302/01',
        'KM/MT/1002/06',
        'KM/MT/0903/01',
        'KM/MT/1708/01',
        'KM/MT/1703/02',
        'KM/MT/1010/03',
        'KM/MT/1703/01',
        'KM/MT/1402/01',
        'KM/MT/1002/02',
        'KM/MT/1010/01.V1',
        'KM/MT/1410/01',
        'KM/MT/2011/01',
        'KM/MT/1001/02',
        'KM/MT/1002/07',
        'KM/MT/0412/01',
        'Unidentified_012',
        'KM/MT/1003/01',
        'KM/MT/0801/01',
        'KM/FD/2001/01',
        'KM/FD/2001/02',
        'KM/HD/0802/01',
        'KM/CP/1904/02',
        'KM/CP/1910/02',
        'KM/CP/2008/01',
        'KM/CP/2001/02',
        'KM/CP/2101/02',
        'KM/CP/0804/01',
        'KM/CP/2011/03',
        'KM/CP/1910/04',
        'KM/CP/1104/01',
        'KM/CP/2011/02',
        'KM/CP/1002/04',
        'Unidentified_013',
        'KM/CP/2001/08',
        'KM/CP/2003/04',
        'KM/CP/2003/01',
        'KM/CP/2003/02',
        'KM/CP/2004/01',
        'KM/CP/2007/02',
        'KM/CP/2109/03',
        'KM/CP/0812/03',
        'KM/CP/1909/02',
        'KM/CP/1910/01',
        'KM/CP/1911/03',
        'KM/CP/2001/05',
        'KM/CP/2001/04',
        'Unidentified_014',
        'KM/CP/1910/09',
        'KM/CP/0911/01',
        'KM/CP/2008/02',
        'KM/CP/2103/02',
        'KM/CP/1910/06',
        'KM/CP/1906/02',
        'KM/CP/2007/01',
        'KM/CP/2001/03',
        'KM/CP/1109/06',
        'Unidentified_015',
        'KM/CP/1002/04.V1',
        'Unidentified_016',
        'KM/CP/2103/03',
        'KM/CP/1912/01',
        'KM/CP/1912/02',
        'KM/CP/1910/07',
        'KM/CP/1908/05',
        'KM/CP/2001/06',
        'KM/CP/1906/01',
        'KM/CP/2003/03',
        'KM/CP/0805/04',
        'KM/CP/0901/02',
        'KM/CP/1911/02',
        'KM/CP/2101/03',
        'KM/CP/1012/01',
        'KM/CP/0804/02',
        'KM/CP/2109/02',
        'KM/CP/2001/07',
        'KM/CP/2109/01',
        'KM/CP/1101/01',
        'KM/CP/1906/03',
        'KM/CP/0609/02',
        'KM/CP/1407/03',
        'KM/CP/1108/01',
        'KM/CP/1107/01',
        'KM/CP/1910/03',
        'KM/CP/2001/11',
        'KM/KB/2001/10',
        'KM/CP/1903/01',
        'KM/CP/1910/05',
        'KM/CP/2004/02',
        'KM/CP/2002/02',
        'KM/CP/2001/09',
        'KM/CP/2104/02',
        'KM/CP/2001/01',
        'Unidentified_017',
        'KM/CP/1004/03',
        'KM/CP/1910/08',
        'KM/CP/1503/01',
        'KM/CP/1109/05',
        'KM/CP/1102/01',
        'KM/CP/2003/05',
        'KM/CP/2011/01',
        'KM/CP/0603/02',
        'KM/CP/1908/01',
        'KM/CP/0806/01',
        'KM/CP/1003/01',
        'KM/CP/2002/01',
        'KM/CP/1908/02',
        'KM/CP/2008/03',
        'KM/CP/2002/03',
        'KM/CP/1911/02.V1',
        'KM/CP/1612/02',
        'Unidentified_018',
        'KM/CP/0905/02',
        'KM/CP/1002/03',
        'KM/CP/1003/06',
        'KM/CP/1002/02',
        'KM/CP/1911/01',
        'KM/MD/1911/01',
        'KM/MD/2012/03',
        'KM/MD/2004/03',
        'KM/MD/2012/01',
        'KM/MD/2012/02',
        'KM/MD/2004/01',
        'KM/MD/2110/01',
        'KM/SW/1606/01',
        'KM/SW/1910/02',
        'KM/SW/1910/03',
        'KM/SW/1910/04',
        'KM/SW/1910/05',
        'KM/SW/2012/01',
        'KM/SW/2012/02',
        'KM/SW/2012/03',
        'KM/SW/2012/04',
        'KM/SW/2012/05',
        'KM/SW/2012/06',
        'KM/SW/2012/07',
        'KM/SW/1508/02',
        'KM/WR/1512/01',
        'KM/SW/2006/01',
        'KM/SW/2006/02',
        'KM/SW/2012/12',
        'KM/SW/2012/13',
        'KM/SW/2012/14',
        'KM/SW/2012/15',
        'KM/SW/2012/16',
        'KM/SW/2012/17',
        'KM/SW/2012/18',
        'KM/SW/1509/09',
        'KM/SW/1508/02.V1',
        'KM/SW/1508/05',
        'KM/SW/2012/08',
        'KM/SW/2012/09',
        'KM/SW/2012/10',
        'KM/SW/2012/11',
        'KM/WR/1409/01',
        'KM/WR/2106/01',
        'KM/SW/1606/03',
        'KM/SW/1010/01',
        'KM/SW/1211/01',
        'KM/SW/1212/01',
        'KM/FD/0909/01',
        'KM/FD/1001/01',
        'KM/UH/2109/01',
        'KM/MT/1005/01',
        'KM/HD/1802/09',
        'KM/HD/1409/01',
        'KM/HD/1910/01',
        'KM/HD/1802/02',
        'KM/HD/1802/06',
        'KM/HD/1802/01',
        'KM/HD/1704/01',
        'KM/HD/2010/01',
        'KM/HD/2012/01',
        'KM/HD/2001/02',
        'KM/HD/1802/07',
        'KM/HD/2001/01',
        'KM/HD/1403/01',
        'KM/HD/1802/04',
        'KM/HD/1802/08',
        'KM/HD/1802/03',
        'KM/HD/1802/10',
        'KM/HD/1004/01',
        'MN/WC/2103/01',
        'KM/PJ/0308/01',
        'KM/MD/2004/02',
        'KM/MD/2004/04',
        'KM/MD/2004/05',
        'KM/MD/2004/06',
        'KM/MD/2108/01',
        'KM/MD/2108/02',
        'KM/MD/1508/02',
        'KM/MD/1508/03',
        'KM/MD/1312/02',
        'KM/MD/1401/01',
        'KM/MD/1312/01',
        'KM/KB/1102/02',
        'KM/MO/1208/01',
        'KM/CP/1904/01',
        'KM/MT/2012/01',
        'KM/CP/2107/01',
        'KM/MO/1911/10',
        'KM/PJ/2202/01',
        'KM/MT/2202/01',
        'KM/SW/1009/01',
        'KM/MT/2202/02',
        'KM/PT/1809/01',
        'KM/KB/1909/02',
        'KM/MO/1907/01',
        'KM/PT/1908/02',
        'KM/MT/1210/01',
        'KM/PT/2101/01',
        'KM/MO/2203/01',
        'KM/PJ/1611/01',
        'MN/PJ/1001/01',
        'KM/MO/2203/02',
        'KM/MO/2012/05',
        'KM/WR/2204/02',
        'KM/WR/2204/01',
        'KM/WR/2204/03',
        'KM/KB/2203/01',
        'BY/CR/2204/01',
        'BY/CR/2204/02',
        'BY/CR/2204/03',
        'BY/CR/2204/04',
        'BY/CR/2204/05',
        'BY/NB/2204/01',
        'BY/ST/2204/01',
        'BY/ST/2204/02',
        'BY/ST/2204/03',
        'BY/ST/2204/04',
        'BY/ST/2204/05',
        'BY/ST/2204/06',
        'BY/ST/2204/07',
        'BY/ST/2204/08',
        'BY/ST/2204/09',
        'BY/ST/2204/10',
        'KM/MO/2204/01',
        'KM/KB/2203/03',
        'KM/MO/2203/03',
        'KM/MO/2108/02',
        'KM/MO/2203/04',
        'KM/KB/2203/02',
        'KM/KB/2103/02',
        'KM/MO/2104/02',
        'KM/KB/2203/04',
        'BY/PS/2205/01',
        'BY/PS/2205/02',
        'BY/MM/2205/01',
        'BY/MM/2205/02',
        'BY/ST/2205/01',
        'KM/CP/2205/01',
        'KM/CP/2205/02',
        'KM/CP/2205/03',
        'KM/MO/2205/01',
        'KM/MO/2205/02',
        'KM/MO/2205/03',
        'KM/MO/2205/04',
        'KM/MO/2205/05',
        'KM/KB/2203/05',
        'KM/CP/2206/01',
        'KM/CP/2206/02',
        'KM/MT/2206/01',
        'KM/MT/2206/02',
        'KM/MT/2206/03',
        'KM/MO/2206/01',
        'KM/MO/2206/02',
        'KM/MO/2206/03',
        'KM/MO/2206/04',
        'KM/MO/2206/05',
        'KM/KB/2206/01',
        'KM/KB/2206/02',
        'KM/KB/2206/03',
        'KM/KB/2206/04',
        'KM/KB/2206/05',
        'KM/MO/1302/16',
        'KM/MO/2103/02',
        'KM/CP/2207/01',
        'KM/CP/2207/02',
        'KM/CP/2207/03',
        'KM/CP/2207/04',
        'KM/CP/2207/05',
        'KM/CP/2207/06',
        'KM/KB/2207/01',
        'KM/KB/2207/02',
        'KM/KB/2207/03',
        'KM/KB/2207/04',
        'KM/KB/2207/05',
        'KM/MO/2207/01',
        'KM/MO/2207/02',
        'KM/MO/2207/03',
        'KM/MO/2207/04',
        'KM/MO/2207/05',
        'KM/KB/1001/04',
        'KM/MO/1302/01',
        'BY/ST/2207/01',
        'BY/ST/2207/02',
        'BY/ST/2207/03',
        'BY/ST/2207/04',
        'BY/ST/2207/05',
        'BY/ST/2207/06',
        'BY/CY/2207/01',
        'BY/CY/2207/02',
        'BY/CY/2207/03',
        'BY/CY/2207/04',
        'BY/CY/2207/05',
        'BY/CY/2207/06',
        'BY/CY/2207/07',
        'KM/MO/1008/03',
        'KM/LT/1505/01',
        'KM/LT/1505/02',
        'KM/NC/1005/01.2',
        'KM/NC/1005/01.3',
        'KM/NC/1006/01.2',
        'KM/NC/1006/01.1',
        'KM/NC/1109/01.5',
        'KM/NC/1011/01.5',
        'KM/NC/1009/01.2',
        'KM/NC/1009/01.1',
        'KM/NC/1101/02.3',
        'KM/NC/1005/01.5',
        'KM/NC/1101/02.4',
        'KM/NC/1009/01.4',
        'KM/NC/1011/01.4',
        'KM/NC/1005/01.4',
        'KM/NC/1005/01.52',
        'Unidentified_020',
        'KM/NC/1109/01.1',
        'KM/NC/1109/01.3',
        'KM/NC/1101/02.1',
        'KM/NC/1206/01',
        'KM/NC/1102/02.4',
        'KM/NC/1006/01.4',
        'KM/NC/1309/09',
        'KM/NC/1005/01.1',
        'KM/NC/1007/01.2',
        'KM/NC/1509/05',
        'KM/NC/1011/01.1',
        'KM/NC/1107/01.1',
        'KM/NC/1011/01.12',
        'KM/NC/1011/01.13',
        'KM/NC/1102/02.3',
        'KM/NC/1011/01.3',
        'KM/NC/1102/01.3',
        'KM/NC/1102/01.2',
        'KM/NC/1102/01.5',
        'KM/NC/1102/01.1',
        'KM/NC/1102/01.4',
        'KM/NC/1107/01.6',
        'KM/NC/1108/01',
        'KM/NC/1108/02.1',
        'KM/CP/0609/01',
        'KM/MT/0601/02',
        'KM/DR/1003/01',
        'KM/DR/1107/01',
        'KM/DR/0912/01',
        'Unidentified_021',
        'MN/DR/1711/01',
        'KM/DR/1201/01',
        'KM/NB/2202/01',
        'KM/NB/2202/02',
        'KM/SW/1606/02',
        'KM/SW/1903/01',
        'KM/SW/2001/02',
    ];

    $(".barcode").autocomplete({
        source: availableTags,
        select: function (event, ui) {
            const form = $(this).closest('form');
            $(this).val(ui.item.value);
            var pro_code = $(this).val();
            var employee = form.find('select[name="md_employee_id"]').val();
            if (pro_code !== '') {
                // setQty = 1;
                insertCart(pro_code, employee);
            }
        },
        close: function () {
            $(this).val('');
            autoFocus();
        }
        // }).keyup(function (e) {
        //     if (e.keyCode === 13) { //keycode enter
        //         e.preventDefault();
        //         $(this).val('');
        //     }
    });
    // .data('ui-autocomplete')._renderItem = function (ul, item) {
    //     return $("<li class='ui-autocomplete-row'></li>")
    //         .data("item.autocomplete", item)
    //         .append(item.label)
    //         .appendTo(ul);
    // };
})

function insertCart(code, employee) {
    url = SITE_URL + '/insert_cart?assetcode=' + code + '&employee=' + employee;
    showTable(url);
}

function autoFocus() {
    document.getElementById('assetcode').focus();
}

// $('.barcode').autocomplete({
//     source: ADMIN_URL 
//     minLength: 1,
//     scroll: true,
//     select: function (event, ui) {
//         $(this).val(ui.item.value);
//         var pro_code = $(this).val();
//         if (pro_code !== '') {
//             setQty = 1;
//             insertCart(pro_code, setQty);
//         }
//     },
//     close: function () {
//         $(this).val('');
//         autoFocus();
//     }
// }).keyup(function (e) {
//     if (e.keyCode === 13) { //keycode enter
//         e.preventDefault();
//         $(this).val('');
//     }
// }).data('ui-autocomplete')._renderItem = function (ul, item) {
//     return $("<li class='ui-autocomplete-row'></li>")
//         .data("item.autocomplete", item)
//         .append(item.label)
//         .appendTo(ul);
// };
// });