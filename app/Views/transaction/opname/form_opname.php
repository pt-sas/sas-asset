<div class="card-body card-form">
    <form class="form-horizontal" id="form_opname">
        <?= csrf_field(); ?>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="documentno">Document No <span class="required">*</span></label>
                    <input type="text" class="form-control code" id="documentno" name="documentno" readonly>
                    <small class="form-text text-danger" id="error_documentno"></small>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="opnamedate">Opname Date <span class="required">*</span></label>
                    <input type="text" class="form-control datepicker" id="opnamedate" name="opnamedate" value="<?= $today; ?>">
                    <small class="form-text text-danger" id="error_opnamedate"></small>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="md_branch_id">Branch <span class="required">*</span></label>
                    <select class="form-control select-data" id="md_branch_id" name="md_branch_id" data-url="branch/getList">
                        <option value="">Select Branch</option>
                    </select>
                    <small class="form-text text-danger" id="error_md_branch_id"></small>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="md_room_id">Room <span class="required">*</span></label>
                    <select class="form-control select2" id="md_room_id" name="md_room_id">
                        <option value="">Select Room</option>
                    </select>
                    <small class="form-text text-danger" id="error_md_room_id"></small>
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
                    <label for="md_employee_id">Employee <span class="required">*</span></label>
                    <select class="form-control select-data" id="md_employee_id" name="md_employee_id" data-url="employee/getList">
                        <option value="">Select Employee</option>
                    </select>
                    <small class="form-text text-danger" id="error_md_employee_id"></small>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="assetcode"></label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="assetcode" name="assetcode" placeholder="scan">
                        <div class="input-group-prepend">
                            <button type="button" class="btn btn-primary btn_scan">Scan</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group table-responsive">
                    <table class="table table-striped table-hover table-pointer tb_displayline" style="width: 100%">
                        <thead>
                            <tr>
                                <th class="text-center">Asset Code</th>
                                <th class="text-center">Product</th>
                                <th class="text-center">Branch</th>
                                <th class="text-center">Room</th>
                                <th class="text-center">Employee</th>
                                <th class="text-center">Status</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </form>
</div>