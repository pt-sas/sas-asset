<div class="card-body card-form">
    <form class="form-horizontal" id="form_disposal">
        <?= csrf_field(); ?>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="documentno">Document No <span class="required">*</span></label>
                    <input type="text" class="form-control code" id="documentno" name="documentno" readonly>
                    <small class="form-text text-danger" id="error_documentno"></small>
                </div>
                <div class="form-group">
                    <label for="disposaltype">Disposal Type <span class="required">*</span></label>
                    <select class="form-control select2" id="disposaltype" name="disposaltype">
                        <option value="">Select Status</option>
                        <?php foreach ($ref_list as $row) : ?>
                            <option value="<?= $row->value ?>"><?= $row->name ?></option>
                        <?php endforeach; ?>
                    </select>
                    <small class="form-text text-danger" id="error_disposaltype"></small>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="disposaldate">Disposal Date <span class="required">*</span></label>
                    <input type="text" class="form-control datepicker" id="disposaldate" name="disposaldate" value="<?= $today; ?>">
                    <small class="form-text text-danger" id="error_disposaldate"></small>
                </div>
                <div class="form-group">
                    <label for="md_supplier_id">Supplier <span class="required">*</span></label>
                    <select class="form-control select-data" id="md_supplier_id" name="md_supplier_id" data-url="supplier/getList">
                        <option value="">Select Supplier</option>
                    </select>
                    <small class="form-text text-danger" id="error_md_supplier_id"></small>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="description">Description </label>
                    <textarea type="text" class="form-control" id="description" name="description" rows="4"></textarea>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="grandtotal">Invoice Sale Amount <span class="required">*</span></label>
                    <input type="text" class="form-control rupiah" id="grandtotal" name="grandtotal" readonly>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <div class="text-right">
                        <button type="button" name="button" class="btn btn-primary btn-sm btn-round ml-auto add_row" title="Add New"><i class="fa fa-plus fa-fw"></i> Add New</button>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group table-responsive">
                    <table class="table table-hover tb_displayline" id="table_disposal" style="width: 100%">
                        <thead>
                            <tr>
                                <th>Asset Code</th>
                                <th>Product</th>
                                <th>Unit Price</th>
                                <th>Condition</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </form>
</div>