<?= $this->extend('backend/_partials/overview') ?>

<?= $this->section('content'); ?>

<?= $this->include('masterdata/supplier/form_supplier'); ?>
<div class="card-body card-main">
    <table class="table table-striped table-hover tb_display">
        <thead>
            <tr>
                <th>ID</th>
                <th>No</th>
                <th>Supplier Code</th>
                <th>Name</th>
                <th>Email</th>
                <th>Address</th>
                <th>Owner</th>
                <th>Telephone</th>
                <th>Vendor</th>
                <th>Service</th>
                <th>Active</th>
                <th>Actions</th>
            </tr>
        </thead>
    </table>
</div>
<?= $this->endSection() ?>