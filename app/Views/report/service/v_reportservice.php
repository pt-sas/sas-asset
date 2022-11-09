<?= $this->extend('backend/_partials/overview') ?>

<?= $this->section('content'); ?>
<form id="parameter_reportservice">
    <div class="card-body">

        <div class="form-group row">
            <label for="md_supplier_id" class="col-lg-3 col-md-3 col-sm-4 mt-sm-2 text-right">Supplier </label>
            <div class="col-lg-6 col-md-9 col-sm-8 select2-input select2-primary">
                <select class="form-control multiple-select-supplier" name="md_supplier_id"></select>
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
            <label for="md_status_id" class="col-lg-3 col-md-3 col-sm-4 mt-sm-2 text-right">Status </label>
            <div class="col-lg-6 col-md-9 col-sm-8">
                <select class="form-control select2" name="md_status_id">
                    <option value="">Select Status</option>
                    <?php foreach ($status as $row) : ?>
                        <option value="<?= $row->getStatusId() ?>"><?= $row->getName() ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="form-group row">
            <label for="servicedate" class="col-lg-3 col-md-3 col-sm-4 mt-sm-2 text-right">Date Service </label>
            <div class="col-lg-6 col-md-9 col-sm-8 ">
                <div class="input-icon">
                    <input type="text" class="form-control daterange" name="servicedate" value="<?= $date_range ?>" placeholder="Date Inventory">
                    <span class="input-icon-addon">
                        <i class="fas fa-calendar-alt"></i>
                    </span>
                </div>
            </div>
        </div>

    </div>
</form>
<?= $this->endSection() ?>