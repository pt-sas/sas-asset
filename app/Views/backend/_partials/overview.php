<!DOCTYPE html>
<html>
<?= $this->include('backend/_partials/head') ?>

<body>
  <div class="wrapper">
    <div class="main-header">
      <?= $this->include('backend/_partials/logo') ?>
      <?= $this->include('backend/_partials/navbar') ?>
    </div>
    <?= $this->include('backend/_partials/sidebar') ?>

    <div class="main-panel">
      <div class="container">
        <div class="page-inner">
          <?php if (!empty(session()->getFlashdata('error'))) : ?>
            <div class="alert alert-danger">
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
              <?= session()->getFlashdata('error'); ?>
            </div>
          <?php endif; ?>

          <?= $this->include('backend/_partials/breadcrumb') ?>
          <?= !$filter ? '' : $this->include($filter) ?>
          <div class="row main_page">
            <div class="col-md-12">
              <div class="card">
                <div class="card-header">
                  <div class="float-left">
                    <h4 class="card-title"><?= $title; ?></h4>
                  </div>
                  <div class="float-right">
                    <?= $toolbar_button ?>
                  </div>
                </div>
                <?= $this->renderSection('content') ?>
                <?= $action_button ?>
              </div>
            </div>
          </div>
        </div>
      </div>
      <?= $this->include('backend/_partials/footer') ?>
    </div>

    <?= $this->include('backend/_partials/quicksidebar') ?>
  </div>
  <a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
  </a>
  <?= $this->include('backend/auth/form_password') ?>

  <?= $this->include('backend/_partials/js') ?>
</body>

</html>