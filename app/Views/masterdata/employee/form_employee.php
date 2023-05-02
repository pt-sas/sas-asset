<div class="card-body card-form">
    <form class="form-horizontal" id="form_employee">
        <?= csrf_field(); ?>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="value">Employee Code <span class="required">*</span></label>
                    <input type="text" class="form-control code" id="value" name="value" readonly>
                    <small class="form-text text-danger" id="error_value"></small>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="name">Full Name <span class="required">*</span></label>
                    <input type="text" class="form-control" id="employee_name" name="name">
                    <small class="form-text text-danger" id="error_name"></small>
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
                    <label for="md_division_id">Division <span class="required">*</span></label>
                    <select class="form-control select-data" id="md_division_id" name="md_division_id" data-url="division/getList">
                        <option value="">Select Division</option>
                    </select>
                    <small class="form-text text-danger" id="error_md_division_id"></small>
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
                    <label for="sys_user_id">User </label>
                    <select class="form-control select-data" id="sys_user_id" name="sys_user_id" data-url="user/getList">
                        <option value="">Select User</option>
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="description">Description </label>
                    <textarea type="text" class="form-control" id="description" name="description" rows="2"></textarea>
                </div>
                <div class="form-check">
                    <label class="form-check-label">
                        <input type="checkbox" class="form-check-input active" id="isactive" name="isactive">
                        <span class="form-check-sign">Active</span>
                    </label>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Alert Recipient </label>
                    <div class="select2-input select2-primary">
                        <select class="form-control multiple-select" name="alert" multiple="multiple" style="width: 100%;">
                            <?php foreach ($user as $row) : ?>
                                <option value="<?= $row->sys_user_id; ?>"><?= $row->name; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>