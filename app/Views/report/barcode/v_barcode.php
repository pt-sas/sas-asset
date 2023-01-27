<?= $this->extend('backend/_partials/overview') ?>

<?= $this->section('content'); ?>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="float-left">
                    <h4 class="card-title"><?= $title; ?></h4>
                </div>
            </div>
            <form action="<?= site_url('sas/rpt_barcode/showAll') ?> " method="post" target="_blank" id="parameter_barcode">
                <div class="card-body">
                    <div class="form-group row">
                        <label for="md_groupasset_id" class="col-lg-3 col-md-3 col-sm-4 mt-sm-2 text-right">Group Asset </label>
                        <div class="col-lg-6 col-md-9 col-sm-8 select2-input select2-primary">
                            <select class="form-control multiple-select-groupasset" id="md_groupasset_id" name="md_groupasset_id[]"></select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="assetcode" class="col-lg-3 col-md-3 col-sm-4 mt-sm-2 text-right">Asset Code </label>
                        <div class="col-lg-6 col-md-9 col-sm-8 select2-input select2-primary">
                            <select class="form-control multiple-select-assetcode" id="assetcode" name="assetcode[]"></select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="md_branch_id" class="col-lg-3 col-md-3 col-sm-4 mt-sm-2 text-right">Branch </label>
                        <div class="col-lg-6 col-md-9 col-sm-8">
                            <select class="form-control select-branch" id="md_branch_id" name="md_branch_id">
                                <option value="">Select Branch</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="md_room_id" class="col-lg-3 col-md-3 col-sm-4 mt-sm-2 text-right">Room </label>
                        <div class="col-lg-6 col-md-9 col-sm-8 select2-input select2-primary">
                            <select class="form-control multiple-select" id="md_room_id" name="md_room_id[]"></select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="md_employee_id" class="col-lg-3 col-md-3 col-sm-4 mt-sm-2 text-right">Employee </label>
                        <div class="col-lg-6 col-md-9 col-sm-8 select2-input select2-primary">
                            <select class="form-control multiple-select-employee" id="md_employee_id" name="md_employee_id[]"></select>
                        </div>
                    </div>
                </div>
                <div class="card-action d-flex justify-content-center">
                    <div>
                        <button type="button" class="btn btn-danger btn-sm btn-round ml-auto btn_reset_form"><i class="fas fa-undo-alt fa-fw"></i> Reset</button>
                        <button type="submit" class="btn btn-success btn-sm btn-round ml-auto"><i class="fas fa-check fa-fw"></i> OK</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>