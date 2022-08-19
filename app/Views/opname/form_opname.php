<?= $this->extend('backend/_partials/overview') ?>

<?= $this->section('content'); ?>
<div class="card-body">
    <form class="form-horizontal" id="form_opname">
        <?= csrf_field(); ?>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="documentno">Document No <span class="required">*</span></label>
                    <input type="text" class="form-control code" id="documentno" name="documentno" readonly>
                    <small class="form-text text-danger" id="error_documentno"></small>
                </div>
                <div class="form-group">
                    <label for="md_employee_id">Employee <span class="required">*</span></label>
                    <select class="form-control select-employee form-control-lg" id="md_employee_id" name="md_employee_id">
                        <option value=""></option>
                    </select>
                    <small class="form-text text-danger" id="error_md_employee_id"></small>
                </div>
                <div class="form-group">
                    <label for="md_employee_id">Employee <span class="required">*</span></label>
                    <div class="input-group mb-3">
                        <input type="text" class="form-control form-control-lg barcode" id="assetcode" name="assetcode" placeholder="Input Asset Code / Scan Barcode" autofocus>
                        <div class="input-group-append">
                            <button type="button" class="btn btn-primary">
                                <span class="fas fa-search"></span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="form-group table-responsive">
                    <table class="table table-bordered table-hover tb_opname" style="width: 100%">
                        <thead>
                            <tr>
                                <th class="text-center">Asset Code</th>
                                <th class="text-center">Employee</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </form>
</div>
<?= $this->endSection() ?>