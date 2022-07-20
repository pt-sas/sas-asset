<?= $this->extend('backend/_partials/overview') ?>

<?= $this->section('content'); ?>

<?= $this->include('transaction/inventory/form_inventory'); ?>
<div class="card-body card-main">
    <table class="table table-bordered table-hover tb_display" style="width: 100%">
        <thead>
            <tr>
                <th>ID</th>
                <th>No</th>
                <th>Asset Code</th>
                <th>Product</th>
                <th>Branch</th>
                <th>Division</th>
                <th>Room</th>
                <th>Employee</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
    </table>
</div>
<?= $this->endSection() ?>