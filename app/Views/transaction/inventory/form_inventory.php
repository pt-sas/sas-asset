<div class="card-body card-form">
    <form class="form-horizontal" id="form_inventory">
        <?= csrf_field(); ?>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="assetcode">Asset Code <span class="required">*</span></label>
                    <input type="text" class="form-control" id="assetcode" name="assetcode">
                    <small class="form-text text-danger" id="error_assetcode"></small>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="md_product_id">Product <span class="required">*</span></label>
                    <select class="form-control select-data" id="md_product_id" name="md_product_id" data-url="product/getList">
                        <option value="">Select Product</option>
                    </select>
                    <small class="form-text text-danger" id="error_md_product_id"></small>
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
                    <label for="md_employee_id">Employee <span class="required">*</span></label>
                    <select class="form-control select2" id="md_employee_id" name="md_employee_id">
                        <option value="">Select Employee</option>
                    </select>
                    <small class="form-text text-danger" id="error_md_employee_id"></small>
                </div>
                <div class="form-check">
                    <label class="form-check-label">
                        <input type="checkbox" class="form-check-input active" id="isactive" name="isactive">
                        <span class="form-check-sign">Active</span>
                    </label>
                </div>
            </div>
        </div>
    </form>
</div>