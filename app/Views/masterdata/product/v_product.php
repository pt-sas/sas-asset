<?= $this->extend('backend/_partials/overview') ?>

<?= $this->section('content'); ?>

<?= $this->include('masterdata/product/form_product'); ?>
<div class="card-body card-main">
    <table class="table table-striped table-hover tb_display">
        <thead>
            <tr>
                <th>ID</th>
                <th>No</th>
                <th>Product Code</th>
                <th>Name</th>
                <th>Brand</th>
                <th>Category</th>
                <th>Sub-Category</th>
                <th>Type</th>
                <th>Variant</th>
                <th>Description</th>
                <th>Active</th>
                <th>Actions</th>
            </tr>
        </thead>
    </table>
</div>
<?= $this->endSection() ?>