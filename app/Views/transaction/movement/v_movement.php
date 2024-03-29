<?= $this->extend('backend/_partials/overview') ?>

<?= $this->section('content'); ?>

<?= $this->include('transaction/movement/form_movement'); ?>
<div class="card-body card-main">
    <table class="table table-striped table-hover tb_display" style="width: 100%">
        <thead>
            <tr>
                <th>ID</th>
                <th>No</th>
                <th>Doc No</th>
                <th>Date</th>
                <th>Type</th>
                <th>Reference No</th>
                <th>Branch</th>
                <th>Division</th>
                <th>Branch To</th>
                <th>Division To</th>
                <th>Status</th>
                <th>Doc Status</th>
                <th>Createdby</th>
                <th>Description</th>
                <th>Actions</th>
            </tr>
        </thead>
    </table>
</div>
<?= $this->endSection() ?>