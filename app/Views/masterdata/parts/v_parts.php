<?= $this->extend('backend/_partials/overview') ?>

<?= $this->section('content'); ?>
<?= $this->include('masterdata/parts/form_parts'); ?>

<div class="card-body card-main">
    <table class="table table-striped table-hover tb_display">
        <thead>
            <tr>
                <th>ID</th>
                <th>No</th>
                <th>Part Code</th>
                <th>Name</th>
                <th>Product</th>
                <th>Active</th>
                <th>Actions</th>
            </tr>
        </thead>
    </table>
</div>
<?= $this->endSection() ?>