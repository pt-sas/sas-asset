<?= $this->extend('backend/_partials/overview') ?>

<?= $this->section('content') ?>

<?= $this->include('backend/configuration/menu/form_menu'); ?>
<div class="card-body card-main">
  <table class="table table-bordered table-hover table-pointer tb_display" style="width: 100%">
    <thead>
      <tr>
        <th>ID</th>
        <th>No</th>
        <th>Name</th>
        <th>Url</th>
        <th>Sequence</th>
        <th>Icon</th>
        <th>Status</th>
        <th>Actions</th>
      </tr>
    </thead>
  </table>
</div>
<?= $this->endSection() ?>