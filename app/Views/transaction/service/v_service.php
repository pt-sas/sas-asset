<?= $this->extend('backend/_partials/overview') ?>

<?= $this->section('content'); ?>

<?= $this->include('transaction/service/form_service'); ?>
<div class="card-body card-main">
    <table class="table table-striped table-hover tb_display">
        <thead>
            <tr>
                <th>ID</th>
                <th>No</th>
                <th>Document No</th>
                <th>Date</th>
                <th>Supplier</th>
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