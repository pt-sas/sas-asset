<div class="card-body card-form">
    <form class="form-horizontal" id="form_status">
        <?= csrf_field(); ?>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="value">Status Code <span class="required">*</span></label>
                    <input type="text" class="form-control code" id="value" name="value" readonly>
                    <small class="form-text text-danger" id="error_value"></small>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="name">Name <span class="required">*</span></label>
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
                    <label>Menu <span class="required">*</span></label>
                    <div class="select2-input select2-primary">
                        <select class="form-control multiple-select" name="menu_id" multiple="multiple" style="width: 100%;">
                            <?php foreach ($menu as $row) : ?>
                                <option value="<?= $row; ?>"><?= $row; ?></option>
                            <?php endforeach; ?>
                        </select>
                        <small class="form-text text-danger" id="error_menu_id"></small>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-check">
                    <label class="form-check-label">
                        <input type="checkbox" class="form-check-input" id="isline" name="isline">
                        <span class="form-check-sign">Line</span>
                    </label>
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