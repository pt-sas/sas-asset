<?= $this->extend('backend/_partials/overview') ?>

<?= $this->section('content'); ?>

<?= $this->include('transaction/receipt/form_receipt'); ?>
<div class="card-body card-main">
    <table class="table table-striped table-hover tb_display" style="width: 100%">
        <thead>
            <tr>
                <th>ID</th>
                <th>No</th>
                <th>Doc No</th>
                <th>Reference No</th>
                <th>Date</th>
                <th>From</th>
                <th>Status</th>
                <th>Document Reference</th>
                <th>Invoice No</th>
                <th>Expense No</th>
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