<?= $this->extend('backend/_partials/overview') ?>

<?= $this->section('content'); ?>

<?= $this->include('backend/modal/product_info'); ?>
<?= $this->include('transaction/internaluse/form_internaluse'); ?>
<div class="card-body card-main">
    <table class="table table-striped table-hover tb_display" style="width: 100%">
        <thead>
            <tr>
                <th>ID</th>
                <th>No</th>
                <th>Doc No</th>
                <th>Date</th>
                <th>Employee</th>
                <th>Status</th>
                <th>Grand Total</th>
                <th>Doc Status</th>
                <th>Createdby</th>
                <th>Description</th>
                <th>Actions</th>
            </tr>
        </thead>
    </table>
</div>
<?= $this->endSection() ?>