<?= $this->extend('backend/_partials/overview') ?>

<?= $this->section('content'); ?>

<?= $this->include('backend/configuration/responsible/form_responsible'); ?>
<div class="card-body card-main">
    <table class="table table-bordered table-hover table-pointer tb_display" style="width: 100%">
        <thead>
            <tr>
                <th>ID</th>
                <th>No</th>
                <th>Name</th>
                <th>Description</th>
                <th>Responsible Type</th>
                <th>Role</th>
                <th>User</th>
                <th>Active</th>
                <th>Actions</th>
            </tr>
        </thead>
    </table>
</div>
<?= $this->endSection() ?>