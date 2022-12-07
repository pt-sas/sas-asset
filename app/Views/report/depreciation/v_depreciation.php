<?= $this->extend('backend/_partials/overview') ?>

<?= $this->section('content'); ?>
<form id="parameter_depreciation">
    <div class="card-body">
        <div class="form-group row">
            <label for="assetcode" class="col-lg-3 col-md-3 col-sm-4 mt-sm-2 text-right">Asset Code </label>
            <div class="col-lg-6 col-md-9 col-sm-8 select2-input select2-primary">
                <select class="form-control multiple-select-assetcode" id="assetcode" name="assetcode"></select>
            </div>
        </div>
    </div>
</form>
<?= $this->endSection() ?>