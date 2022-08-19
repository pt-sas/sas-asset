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
      <?php if (!empty(session()->getFlashdata('error'))) : ?>
        <div class="alert alert-danger">
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          <?= session()->getFlashdata('error'); ?>
        </div>
      <?php endif; ?>
      <div class="container">
        <div class="page-inner">
          <?= $this->include('backend/_partials/breadcrumb') ?>
          <?php if ($menu_action == 1) : ?>
            <div class="row">
              <div class="col-md-12">
                <div class="card">
                  <!-- <div class="card-header">
                    <div class="float-left">
                      <h4 class="card-title"><?= $title; ?></h4>
                    </div>
                  </div> -->
                  <?= $this->renderSection('content') ?>
                  <?= $action_button ?>
                </div>
              </div>
            </div>
          <?php else : ?>
            <?= !$filter ? '' : $this->include($filter) ?>
            <?php if ($title) : ?>
              <div class="row">
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
            <?php endif ?>
          <?php endif; ?>
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