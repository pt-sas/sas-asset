<?= $this->extend('backend/_partials/overview') ?>

<?= $this->section('content'); ?>
<form id="parameter_quotation">
    <div class="card-body">
        <div class="form-group row">
            <label for="md_groupasset_id" class="col-lg-3 col-md-3 col-sm-4 mt-sm-2 text-right">Document No</label>
            <div class="col-lg-6 col-md-9 col-sm-8 select2-input select2-primary">
                <select class="form-control multiple-select-groupasset" name="md_groupasset_id"></select>
            </div>
        </div>
        <div class="form-group row">
            <label for="md_supplier_id" class="col-lg-3 col-md-3 col-sm-4 mt-sm-2 text-right">Supplier</label>
            <div class="col-lg-6 col-md-9 col-sm-8 select2-input select2-primary">
                <select class="form-control multiple-select-supplier" name="md_supplier_id"></select>
            </div>
        </div>
        <div class="form-group row">
            <label for="quotationdate" class="col-lg-3 col-md-3 col-sm-4 mt-sm-2 text-right">Quotation Date</label>
            <div class="col-lg-6 col-md-9 col-sm-8">
                <div class="input-icon">
                    <input type="text" class="form-control daterange" name="quotationdate" value="<?= $date_range ?>" placeholder="Date Quotation">
                    <span class="input-icon-addon">
                        <i class="fas fa-calendar-alt"></i>
                    </span>
                </div>
            </div>
        </div>
        <div class="form-group row">
            <label for="md_status_id" class="col-lg-3 col-md-3 col-sm-4 mt-sm-2 text-right">Doc Status</label>
            <div class="col-lg-6 col-md-9 col-sm-8 select2-input select2-primary">
                <select class="form-control multiple-select-status" name="md_status_id"></select>
            </div>
        </div>
        <div class="form-group row">
            <label for="md_product_id" class="col-lg-3 col-md-3 col-sm-4 mt-sm-2 text-right">Product</label>
            <div class="col-lg-6 col-md-9 col-sm-8">
                <select class="form-control select-product" name="md_product_id">
                    <option value="">Select Product</option>
                </select>
            </div>
        </div>
        <div class="form-check">
            <div class="row">
                <label class="col-lg-3 col-md-3 col-sm-4 mt-sm-2 text-right">Spare </label>
                <div class="col-lg-4 col-md-9 col-sm-8 d-flex align-items-center">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="isspare" name="isspare">
                        <label class="custom-control-label" for="isspare"> </label>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
<?= $this->endSection() ?>