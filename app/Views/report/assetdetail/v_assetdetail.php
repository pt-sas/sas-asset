<?= $this->extend('backend/_partials/overview') ?>

<?= $this->section('content'); ?>
<form id="parameter_report">
    <div class="card-body">
        <div class="form-group row">
            <label for="md_groupasset_id" class="col-lg-3 col-md-3 col-sm-4 mt-sm-2 text-right">Group Asset </label>
            <div class="col-lg-6 col-md-9 col-sm-8 select2-input select2-primary">
                <select class="form-control multiple-select-groupasset" name="md_groupasset_id"></select>
            </div>
        </div>
        <div class="form-group row">
            <label for="md_brand_id" class="col-lg-3 col-md-3 col-sm-4 mt-sm-2 text-right">Brand </label>
            <div class="col-lg-6 col-md-9 col-sm-8 select2-input select2-primary">
                <select class="form-control multiple-select-brand" name="md_brand_id"></select>
            </div>
        </div>
        <div class="form-group row">
            <label for="md_category_id" class="col-lg-3 col-md-3 col-sm-4 mt-sm-2 text-right">Category </label>
            <div class="col-lg-6 col-md-9 col-sm-8 select2-input select2-primary">
                <select class="form-control multiple-select-category" name="md_category_id"></select>
            </div>
        </div>
        <div class="form-group row">
            <label for="md_subcategory_id" class="col-lg-3 col-md-3 col-sm-4 mt-sm-2 text-right">Subcategory </label>
            <div class="col-lg-6 col-md-9 col-sm-8 select2-input select2-primary">
                <select class="form-control multiple-select-subcategory" name="md_subcategory_id"></select>
            </div>
        </div>
        <div class="form-group row">
            <label for="md_type_id" class="col-lg-3 col-md-3 col-sm-4 mt-sm-2 text-right">Type </label>
            <div class="col-lg-6 col-md-9 col-sm-8 select2-input select2-primary">
                <select class="form-control multiple-select-type" name="md_type_id"></select>
            </div>
        </div>
        <div class="form-group row">
            <label for="md_product_id" class="col-lg-3 col-md-3 col-sm-4 mt-sm-2 text-right">Product </label>
            <div class="col-lg-6 col-md-9 col-sm-8">
                <select class="form-control select-product" name="md_product_id">
                    <option value="">Select Product</option>
                </select>
            </div>
        </div>
        <div class="form-group row">
            <label for="md_branch_id" class="col-lg-3 col-md-3 col-sm-4 mt-sm-2 text-right">Branch </label>
            <div class="col-lg-6 col-md-9 col-sm-8">
                <select class="form-control select-branch" name="md_branch_id">
                    <option value="">Select Branch</option>
                </select>
            </div>
        </div>
        <div class="form-group row">
            <label for="md_division_id" class="col-lg-3 col-md-3 col-sm-4 mt-sm-2 text-right">Division </label>
            <div class="col-lg-6 col-md-9 col-sm-8 select2-input select2-primary">
                <select class="form-control multiple-select-division" name="md_division_id"></select>
            </div>
        </div>
        <div class="form-group row">
            <label for="md_room_id" class="col-lg-3 col-md-3 col-sm-4 mt-sm-2 text-right">Room </label>
            <div class="col-lg-6 col-md-9 col-sm-8 select2-input select2-primary">
                <select class="form-control multiple-select" name="md_room_id"></select>
            </div>
        </div>
        <div class="form-group row">
            <label for="md_employee_id" class="col-lg-3 col-md-3 col-sm-4 mt-sm-2 text-right">Employee </label>
            <div class="col-lg-6 col-md-9 col-sm-8 select2-input select2-primary">
                <select class="form-control multiple-select-employee" name="md_employee_id"></select>
            </div>
        </div>
        <div class="form-group row">
            <label for="assetcode" class="col-lg-3 col-md-3 col-sm-4 mt-sm-2 text-right">Asset Code </label>
            <div class="col-lg-6 col-md-9 col-sm-8 select2-input select2-primary">
                <select class="form-control multiple-select-assetcode" id="assetcode" name="assetcode"></select>
            </div>
        </div>
        <div class="form-group row">
            <label for="inventorydate" class="col-lg-3 col-md-3 col-sm-4 mt-sm-2 text-right">Date Inventory </label>
            <div class="col-lg-6 col-md-9 col-sm-8">
                <div class="input-icon">
                    <input type="text" class="form-control daterange" name="inventorydate" value="<?= $date_range ?>" placeholder="Date Inventory">
                    <span class="input-icon-addon">
                        <i class="fas fa-calendar-alt"></i>
                    </span>
                </div>
            </div>
        </div>
        <div class="form-group row">
            <label for="created_at" class="col-lg-3 col-md-3 col-sm-4 mt-sm-2 text-right">Created </label>
            <div class="col-lg-6 col-md-9 col-sm-8">
                <div class="input-icon">
                    <input type="text" class="form-control daterange" name="created_at" value="<?= $date_created ?>" placeholder="Created">
                    <span class="input-icon-addon">
                        <i class="fas fa-calendar-alt"></i>
                    </span>
                </div>
            </div>
        </div>
        <div class="form-group row">
            <label for="isspare" class="col-lg-3 col-md-3 col-sm-4 mt-sm-2 text-right">Spare </label>
            <div class="col-lg-6 col-md-9 col-sm-8 select2-input select2-primary">
                <select class="form-control select2" name="isspare">
                    <option value="0">All</option>
                    <option value="Y">Yes</option>
                    <option value="N">No</option>
                </select>
            </div>
        </div>
    </div>
</form>
<?= $this->endSection() ?>