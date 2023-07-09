<div class="card-body card-form">
    <form class="form-horizontal" id="form_room">
        <?= csrf_field(); ?>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="value">Room Code <span class="required">*</span></label>
                    <input type="text" class="form-control code" id="value" name="value" readonly>
                    <small class="form-text text-danger" id="error_value"></small>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="name">Room Name <span class="required">*</span></label>
                    <input type="text" class="form-control" id="name" name="name">
                    <small class="form-text text-danger" id="error_name"></small>
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
                    <label for="md_branch_id">Branch <span class="required">*</span></label>
                    <select class="form-control select-data" id="md_branch_id" name="md_branch_id" data-url="branch/getList">
                        <option value="">Select Branch</option>
                    </select>
                    <small class="form-text text-danger" id="error_md_branch_id"></small>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="userrep_id">Room PIC </label>
                    <select class="form-control select-data" id="userrep_id" name="userrep_id" data-url="user/getList">
                        <option value="">Select Room PIC</option>
                    </select>
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