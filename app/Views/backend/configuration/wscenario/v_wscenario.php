<?= $this->extend('backend/_partials/overview') ?>

<?= $this->section('content'); ?>

<?= $this->include('backend/configuration/wscenario/form_wscenario'); ?>
<div class="card-body card-main">
    <table class="table table-striped table-hover tb_display" style="width: 100%">
        <thead>
            <tr>
                <th>ID</th>
                <th>No</th>
                <th>Name </th>
                <th>Line No</th>
                <th>Grand Total</th>
                <th>Menu</th>
                <th>Status</th>
                <th>Branch</th>
                <th>Division</th>
                <th>Scenario Type</th>
                <th>Description</th>
                <th>Active</th>
                <th>Actions</th>
            </tr>
        </thead>
    </table>
</div>
<?= $this->endSection() ?>