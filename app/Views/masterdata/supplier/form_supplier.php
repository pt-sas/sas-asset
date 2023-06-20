<div class="card-body card-form">
    <form class="form-horizontal" id="form_supplier">
        <?= csrf_field(); ?>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="value">Supplier Code <span class="required">*</span></label>
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
                    <label for="owner">Owner <span class="required">*</span></label>
                    <input type="text" class="form-control" id="owner" name="owner">
                    <small class="form-text text-danger" id="error_owner"></small>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="phone">Telephone <span class="required">*</span></label>
                    <input type="text" class="form-control" id="phone" name="phone">
                    <small class="form-text text-danger" id="error_phone"></small>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="address">Address <span class="required">*</span></label>
                    <textarea type="text" class="form-control" id="address" name="address" rows="3"></textarea>
                    <small class="form-text text-danger" id="error_address"></small>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="email">Email </label>
                    <input type="text" class="form-control" id="email" name="email">
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-check">
                    <label class="form-check-label">
                        <input type="checkbox" class="form-check-input active" id="isactive" name="isactive">
                        <span class="form-check-sign">Active</span>
                    </label>
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-check">
                    <label class="form-check-label">
                        <input type="checkbox" class="form-check-input" id="isvendor" name="isvendor" checked>
                        <span class="form-check-sign">Vendor</span>
                    </label>
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-check">
                    <label class="form-check-label">
                        <input type="checkbox" class="form-check-input" id="isservice" name="isservice">
                        <span class="form-check-sign">Service</span>
                    </label>
                </div>
            </div>
        </div>
    </form>
</div>