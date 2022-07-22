<?= $this->extend('backend/_partials/overview') ?>

<?= $this->section('content'); ?>

<?= $this->include('masterdata/groupasset/form_groupasset'); ?>
<div class="card-body card-main">
    <table class="table table-bordered table-hover table-pointer tb_display" style="width: 100%">
        <thead>
            <tr>
                <th>ID</th>
                <th>No</th>
                <th>Group Code</th>
                <th>Name</th>
                <th>Description</th>
                <th>Initial Code</th>
                <th>Useful Life</th>
                <th>Active</th>
                <th>Actions</th>
            </tr>
        </thead>
    </table>
</div>
<?= $this->endSection() ?>