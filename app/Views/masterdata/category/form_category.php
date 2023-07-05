<div class="card-body card-form">
    <form class="form-horizontal" id="form_category">
        <?= csrf_field(); ?>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="value">Category Code <span class="required">*</span></label>
                    <input type="text" class="form-control code" id="value" name="value" readonly>
                    <small class="form-text text-danger" id="error_value"></small>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="name">Category Name<span class="required">*</span></label>
                    <input type="text" class="form-control" id="name" name="name">
                    <small class="form-text text-danger" id="error_name"></small>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="initialcode">Initial Code <span class="required">*</span></label>
                    <input type="text" class="form-control" id="initialcode" name="initialcode">
                    <small class="form-text text-danger" id="error_initialcode"></small>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="md_groupasset_id">Group Asset <span class="required">*</span></label>
                    <select class="form-control select-data" id="md_groupasset_id" name="md_groupasset_id" data-url="groupasset/getList">
                        <option value="">Select Group Asset</option>
                    </select>
                    <small class="form-text text-danger" id="error_md_groupasset_id"></small>
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
                    <label for="pic">PIC </label>
                    <select class="form-control select-data" id="pic" name="pic" data-url="employee/getList">
                        <option value="">Select PIC</option>
                    </select>
                </div>
            </div>
            <div class="col-md-2 mt-4">
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