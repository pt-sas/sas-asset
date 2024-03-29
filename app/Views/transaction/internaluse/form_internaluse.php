<div class="card-body card-form">
    <form class="form-horizontal" id="form_internaluse">
        <?= csrf_field(); ?>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="documentno">Document No <span class="required">*</span></label>
                    <input type="text" class="form-control" id="documentno" name="documentno" placeholder="[auto]" readonly>
                    <small class="form-text text-danger" id="error_documentno"></small>
                </div>
                <div class="form-group">
                    <label for="quotationdate">Date <span class="required">*</span></label>
                    <input type="text" class="form-control datepicker" id="quotationdate" name="quotationdate" value="<?= $today ?>">
                    <small class="form-text text-danger" id="error_quotationdate"></small>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="isfrom">From <span class="required">*</span></label>
                    <div class="mt-2">
                        <?php foreach ($ref_list as $row) : ?>
                            <div class="custom-control custom-radio">
                                <input type="radio" id="<?= strtolower($row->name) ?>" name="isfrom" class="custom-control-input" value="<?= $row->value ?>" hide-field="md_supplier_id, md_employee_id">
                                <label class="custom-control-label" for="<?= strtolower($row->name) ?>"><?= $row->name ?></label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <small class="form-text text-danger" id="error_isfrom"></small>
                </div>
                <div class="form-group mt-2">
                    <label for="md_supplier_id"></label>
                    <select class="form-control select-data" id="md_supplier_id" name="md_supplier_id" data-url="supplier/getList">
                        <option value="">Select From</option>
                    </select>
                    <small class="form-text text-danger" id="error_md_supplier_id"></small>
                </div>
                <div class="form-group mt-2">
                    <label for="md_employee_id"></label>
                    <select class="form-control select-data" id="md_employee_id" name="md_employee_id" data-url="employee/getList">
                        <option value="">Select From</option>
                    </select>
                    <small class="form-text text-danger" id="error_md_employee_id"></small>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="docreference">Internal Use No <span class="required">*</span></label>
                    <input type="text" class="form-control" id="docreference" name="docreference" placeholder="Internal Use No">
                    <small class="form-text text-danger" id="error_docreference"></small>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="md_status_id">Status <span class="required">*</span></label>
                    <select class="form-control select2" id="md_status_id" name="md_status_id" style="width: 100%;" <?= isset($default_logic->condition) ? "disabled" : "" ?>>
                        <option value="">Select Status</option>
                        <?php foreach ($status as $row) : ?>
                            <option value="<?= $row->getStatusId() ?>" <?= (isset($default_logic->id) && $default_logic->id == $row->getStatusId()) && isset($default_logic->condition) ? "selected" : "" ?>><?= $row->getName() ?></option>
                        <?php endforeach; ?>
                    </select>
                    <small class="form-text text-danger" id="error_md_status_id"></small>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="description">Description </label>
                    <textarea type="text" class="form-control" id="description" name="description" rows="2"></textarea>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="grandtotal">Grand Total </label>
                    <input type="text" class="form-control rupiah" id="grandtotal" name="grandtotal" readonly>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-check">
                    <label class="form-check-label">
                        <input type="checkbox" class="form-check-input" id="isinternaluse" name="isinternaluse" checked disabled>
                        <span class="form-check-sign">Free</span>
                    </label>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <div class="text-right">
                        <button type="button" name="button" class="btn btn-primary btn-sm btn-round ml-auto create_line" title="Create Line"><i class="fa fa-plus fa-fw"></i> Create Line</button>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group table-responsive">
                    <table class="table-line table-light table-hover tb_displayline" id="table_quotation" style="width: 100%">
                        <thead>
                            <tr>
                                <th class="text-center">Product</th>
                                <th class="text-center">Qty</th>
                                <th class="text-center">Unit Price</th>
                                <th class="text-center">Line Amount</th>
                                <th>Spare</th>
                                <th class="text-center">Employee</th>
                                <th class="text-center">Specification</th>
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