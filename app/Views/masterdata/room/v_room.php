<?= $this->extend('backend/_partials/overview') ?>

<?= $this->section('content'); ?>

<?= $this->include('masterdata/room/form_room'); ?>
<div class="card-body card-main">
    <table class="table table-striped table-hover tb_display">
        <thead>
            <tr>
                <th>ID</th>
                <th>No</th>
                <th>Room Code</th>
                <th>Name</th>
                <th>Branch</th>
                <th>Room Agent</th>
                <th>Description</th>
                <th>Active</th>
                <th>Actions</th>
            </tr>
        </thead>
    </table>
</div>
<?= $this->endSection() ?>