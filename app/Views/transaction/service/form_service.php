<div class="card-body card-form">
    <form class="form-horizontal" id="form_service">
        <?= csrf_field(); ?>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="documentno">Document No <span class="required">*</span></label>
                    <input type="text" class="form-control" id="documentno" name="documentno">
                    <small class="form-text text-danger" id="error_documentno"></small>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="md_supplier_id">Supplier <span class="required">*</span></label>
                    <select class="form-control select2" id="md_supplier_id" name="md_supplier_id" style="width: 100%">
                        <option value="">Select Supplier</option>
                        <?php foreach ($supplier as $row) : ?>
                            <option value="<?= $row->md_supplier_id ?>"><?= $row->name ?></option>
                        <?php endforeach; ?>
                    </select>
                    <small class="form-text text-danger" id="error_md_supplier_id"></small>
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
                    <label for="servicedate">Date Service <span class="required">*</span></label>
                    <input type="text" class="form-control datepicker col-md-4" id="servicedate" name="servicedate" value="<?= $today ?>">
                    <small class="form-text text-danger" id="error_servicedate"></small>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <div class="text-right">
                        <button type="button" name="button" class="btn btn-primary btn-sm btn-round ml-auto add_row" title="Add New"><i class="fa fa-plus"> Add New</i></button>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group table-responsive">
                    <table class="table table-striped table-hover table-pointer tb_displayline" style="width: 100%">
                        <thead>
                            <tr>
                                <th>Code Asset</th>
                                <th>Product</th>
                                <th>Unit Price</th>
                                <th>Status</th>
                                <th>Description</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </form>
</div>