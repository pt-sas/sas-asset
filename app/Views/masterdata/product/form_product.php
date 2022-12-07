<div class="card-body card-form">
    <form class="form-horizontal" id="form_product">
        <?= csrf_field(); ?>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="value">Product Code <span class="required">*</span></label>
                    <input type="text" class="form-control code" id="value" name="value" readonly>
                    <small class="form-text text-danger" id="error_value"></small>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="name">Name <span class="required">*</span></label>
                    <input type="text" class="form-control" id="name" name="name" readonly>
                    <small class="form-text text-danger" id="error_name"></small>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="md_brand_id">Brand <span class="required">*</span></label>
                    <select class="form-control select-data" id="md_brand_id" name="md_brand_id" data-url="brand/getList">
                        <option value="">Select Brand</option>
                    </select>
                    <small class="form-text text-danger" id="error_md_brand_id"></small>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="md_category_id">Category <span class="required">*</span></label>
                    <select class="form-control select-data" id="md_category_id" name="md_category_id" data-url="category/getList">
                        <option value="">Select Category</option>
                    </select>
                    <small class="form-text text-danger" id="error_md_category_id"></small>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="md_subcategory_id">Subcategory <span class="required">*</span></label>
                    <select class="form-control select2" id="md_subcategory_id" name="md_subcategory_id">
                        <option value="">Select Subcategory</option>
                    </select>
                    <small class="form-text text-danger" id="error_md_subcategory_id"></small>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="md_type_id">Type <span class="required">*</span></label>
                    <select class="form-control select2" id="md_type_id" name="md_type_id">
                        <option value="">Select Type</option>
                    </select>
                    <small class="form-text text-danger" id="error_md_type_id"></small>
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
        </div>
    </form>
</div>