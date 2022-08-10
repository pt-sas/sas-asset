<?= $this->extend('backend/_partials/overview') ?>

<?= $this->section('content'); ?>

<?= $this->include('masterdata/sequence/form_sequence'); ?>
<div class="card-body card-main">
    <table class="table table-bordered table-hover table-pointer tb_display" style="width: 100%">
        <thead>
            <tr>
                <th>ID</th>
                <th>No</th>
                <th>Name</th>
                <th>Description</th>
                <th>Auto Numbering</th>
                <th>Value Format</th>
                <th>Increment</th>
                <th>Max Value</th>
                <th>Current Next</th>
                <th>Decimal Pattern</th>
                <!-- <th>Prefix</th> -->
                <!-- <th>Suffix</th> -->
                <th>Group Asset Level</th>
                <th>Group Asset Column</th>
                <th>Category Level</th>
                <th>Category Column</th>
                <th>Restart sequence every Year</th>
                <th>Date Column</th>
                <th>Restart sequence every Month</th>
                <th>Start No</th>
                <th>Active</th>
                <th>Actions</th>
            </tr>
        </thead>
    </table>
</div>
<?= $this->endSection() ?>