<?= $this->extend('backend/_partials/overview') ?>

<?= $this->section('content'); ?>

<?= $this->include('transaction/service/form_service'); ?>
<div class="card-body card-main">
    <table class="table table-bordered table-hover table-pointer tb_display" style="width: 100%">
        <thead>
            <tr>
                <th>ID</th>
                <th>No</th>
                <th>Document No</th>
                <th>Date Service</th>
                <th>Supplier</th>
                <th>Description</th>
                <th>Actions</th>
            </tr>
        </thead>
    </table>
</div>
<?= $this->endSection() ?>