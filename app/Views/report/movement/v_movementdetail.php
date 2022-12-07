<?= $this->extend('backend/_partials/overview') ?>

<?= $this->section('content'); ?>
<form id="parameter_movementdetail">
    <div class="card-body">

        <div class="form-group row">
            <label for="md_employee_id" class="col-lg-3 col-md-3 col-sm-4 mt-sm-2 text-right">Employee From </label>
            <div class="col-lg-6 col-md-9 col-sm-8 select2-input select2-primary">
                <select class="form-control select-employee" name="employee_from"></select>
            </div>
        </div>

        <div class="form-group row">
            <label for="md_employee_id" class="col-lg-3 col-md-3 col-sm-4 mt-sm-2 text-right">Employee To </label>
            <div class="col-lg-6 col-md-9 col-sm-8 select2-input select2-primary">
                <select class="form-control select-employee" name="employee_to"></select>
            </div>
        </div>

        <div class="form-group row">
            <label for="md_division_id" class="col-lg-3 col-md-3 col-sm-4 mt-sm-2 text-right">Division From </label>
            <div class="col-lg-6 col-md-9 col-sm-8 select2-input select2-primary">
                <select class="form-control select-division" name="division_from"></select>
            </div>
        </div>

        <div class="form-group row">
            <label for="md_division_id" class="col-lg-3 col-md-3 col-sm-4 mt-sm-2 text-right">Division To </label>
            <div class="col-lg-6 col-md-9 col-sm-8 select2-input select2-primary">
                <select class="form-control select-division" name="division_to"></select>
            </div>
        </div>

        <div class="form-group row">
            <label for="md_branch_id" class="col-lg-3 col-md-3 col-sm-4 mt-sm-2 text-right">Branch From</label>
            <div class="col-lg-6 col-md-9 col-sm-8 select2-input select2-primary">
                <select class="form-control select-branch" name="branch_from"></select>
            </div>
        </div>

        <div class="form-group row">
            <label for="md_branch_id" class="col-lg-3 col-md-3 col-sm-4 mt-sm-2 text-right">Branch To</label>
            <div class="col-lg-6 col-md-9 col-sm-8 select2-input select2-primary">
                <select class="form-control select-branch" name="branch_to"></select>
            </div>
        </div>

        <div class="form-group row">
            <label for="md_room_id" class="col-lg-3 col-md-3 col-sm-4 mt-sm-2 text-right">Room From</label>
            <div class="col-lg-6 col-md-9 col-sm-8 select2-input select2-primary">
                <select class="form-control select-room" name="room_from"></select>
            </div>
        </div>

        <div class="form-group row">
            <label for="md_room_id" class="col-lg-3 col-md-3 col-sm-4 mt-sm-2 text-right">Room To</label>
            <div class="col-lg-6 col-md-9 col-sm-8 select2-input select2-primary">
                <select class="form-control select-room" name="room_to"></select>
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
            <label for="movementdate" class="col-lg-3 col-md-3 col-sm-4 mt-sm-2 text-right">Movement Date</label>
            <div class="col-lg-6 col-md-9 col-sm-8 ">
                <div class="input-icon">
                    <input type="text" class="form-control daterange" name="movementdate" value="<?= $date_range ?>" placeholder="Movement Date">
                    <span class="input-icon-addon">
                        <i class="fas fa-calendar-alt"></i>
                    </span>
                </div>
            </div>
        </div>
    </div>

</form>
<?= $this->endSection() ?>