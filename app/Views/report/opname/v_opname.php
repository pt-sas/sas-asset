<?= $this->extend('backend/_partials/overview') ?>

<?= $this->section('content'); ?>
<form id="parameter_opname">
    <div class="card-body">
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
    </div>
</form>
<?= $this->endSection() ?>