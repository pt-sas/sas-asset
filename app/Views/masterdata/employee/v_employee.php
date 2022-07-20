<?= $this->extend('backend/_partials/overview') ?>

<?= $this->section('content'); ?>

<?= $this->include('masterdata/employee/form_employee'); ?>
<div class="card-body card-main">
    <table class="table table-bordered table-hover table-pointer tb_display" style="width: 100%">
        <thead>
            <tr>
                <th>ID</th>
                <th>No</th>
                <th>Employee Code</th>
                <th>Employee Name</th>
                <th>Branch</th>
                <th>Division</th>
                <th>Room</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
    </table>
</div>
<?= $this->endSection() ?>