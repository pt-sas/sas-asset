<?= $this->extend('backend/_partials/overview') ?>

<?= $this->section('content') ?>

<?= $this->include('backend/configuration/user/form_user'); ?>
<div class="card-body card-main">
  <table class="table table-bordered table-hover table-pointer tb_display" style="width: 100%">
    <thead>
      <tr>
        <th>ID</th>
        <th>No</th>
        <th>Username</th>
        <th>Name</th>
        <th>Description</th>
        <th>Email</th>
        <th>Active</th>
        <th>Actions</th>
      </tr>
    </thead>
  </table>
</div>
<?= $this->endSection() ?>