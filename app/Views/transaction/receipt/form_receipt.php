<div class="card-body card-form">
    <form class="form-horizontal" id="form_receipt">
        <?= csrf_field(); ?>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="documentno">Document No <span class="required">*</span></label>
                    <input type="text" class="form-control code" id="documentno" name="documentno" readonly>
                    <small class="form-text text-danger" id="error_documentno"></small>
                </div>
                <div class="form-group">
                    <label for="receiptdate">Receipt Date <span class="required">*</span></label>
                    <input type="text" class="form-control datepicker" id="receiptdate" name="receiptdate" value="<?= $today; ?>">
                    <small class="form-text text-danger" id="error_receiptdate"></small>
                </div>
                <div class="form-group">
                    <label for="docreference">Document Reference <span class="required">*</span></label>
                    <input type="text" class="form-control" id="docreference" name="docreference" placeholder="Document Reference">
                    <small class="form-text text-danger" id="error_docreference"></small>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="trx_quotation_id">Quotation <span class="required">*</span></label>
                    <select class="form-control select-data" id="trx_quotation_id" name="trx_quotation_id" data-url="quotation/getList" hide-field="md_supplier_id, md_employee_id">
                        <option value="">Select Quotation</option>
                    </select>
                    <small class="form-text text-danger" id="error_trx_quotation_id"></small>
                </div>
                <div class="form-group">
                    <label for="md_supplier_id">Supplier <span class="required">*</span></label>
                    <select class="form-control select-data" id="md_supplier_id" name="md_supplier_id" data-url="supplier/getList" disabled>
                        <option value="">Select Supplier</option>
                    </select>
                    <small class="form-text text-danger" id="error_md_supplier_id"></small>
                </div>
                <div class="form-group">
                    <label for="md_employee_id">From <span class="required">*</span></label>
                    <select class="form-control select-data" id="md_employee_id" name="md_employee_id" data-url="employee/getList" disabled>
                        <option value="">Select From</option>
                    </select>
                    <small class="form-text text-danger" id="error_md_employee_id"></small>
                </div>
                <div class="form-group">
                    <label for="md_status_id">Status <span class="required">*</span></label>
                    <select class="form-control select2" id="md_status_id" name="md_status_id" style="width: 100%;" disabled>
                        <option value="">Select Status</option>
                        <?php foreach ($status as $row) : ?>
                            <option value="<?= $row->getStatusId() ?>"><?= $row->getName() ?></option>
                        <?php endforeach; ?>
                    </select>
                    <small class="form-text text-danger" id="error_md_status_id"></small>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="invoiceno">Invoice No <span class="required">*</span></label>
                    <input type="text" class="form-control" id="invoiceno" name="invoiceno" placeholder="Invoice No">
                    <small class="form-text text-danger" id="error_invoiceno"></small>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="grandtotal">Grand Total </label>
                    <input type="text" class="form-control rupiah" id="grandtotal" name="grandtotal" readonly>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="description">Description </label>
                    <textarea type="text" class="form-control" id="description" name="description" rows="2"></textarea>
                </div>
            </div>
            <div class="col-md-6 mt-4">
                <div class="form-check">
                    <label class="form-check-label">
                        <input type="checkbox" class="form-check-input" id="isinternaluse" name="isinternaluse" disabled>
                        <span class="form-check-sign">Free</span>
                    </label>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group table-responsive">
                    <table class="table-line table-striped table-hover table-pointer tb_displayline" style="width: 100%">
                        <thead>
                            <tr>
                                <th class="text-center">Asset Code</th>
                                <th class="text-center">Product</th>
                                <th class="text-center">Qty</th>
                                <th class="text-center">Residual Value</th>
                                <th class="text-center">Unit Price</th>
                                <th>Spare</th>
                                <th class="text-center">Employee</th>
                                <th class="text-center">Branch</th>
                                <th class="text-center">Division</th>
                                <th class="text-center">Room</th>
                                <th class="text-center">Description</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </form>
</div>