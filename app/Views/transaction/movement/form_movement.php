<div class="card-body card-form">
    <form class="form-horizontal" id="form_movement">
        <?= csrf_field(); ?>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="documentno">Document No <span class="required">*</span></label>
                    <input type="text" class="form-control code" id="documentno" name="documentno" readonly>
                    <small class="form-text text-danger" id="error_documentno"></small>
                </div>
                <div class="form-group">
                    <label for="movementdate">Movement Date <span class="required">*</span></label>
                    <input type="text" class="form-control datepicker" id="movementdate" name="movementdate" value="<?= $today; ?>">
                    <small class="form-text text-danger" id="error_movementdate"></small>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="description">Description </label>
                    <textarea type="text" class="form-control" id="description" name="description" rows="4"></textarea>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <div class="text-right">
                        <button type="button" name="button" class="btn btn-primary btn-sm btn-round ml-auto add_row" title="Add New"><i class="fa fa-plus fa-fw"></i> Add New</button>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group table-responsive">
                    <table class="table-rounded table-hover tb_displayline" style="width: 100%">
                        <thead>
                            <tr>
                                <th rowspan="2" class="text-center">Asset Code</th>
                                <th rowspan="2" class="text-center">Product</th>
                                <th rowspan="2" class="text-center">Status</th>
                                <th colspan="2" class="text-center">Employee</th>
                                <th colspan="2" class="text-center">Branch</th>
                                <th colspan="2" class="text-center">Division</th>
                                <th colspan="2" class="text-center">Room</th>
                                <th rowspan="2" class="text-center">Description</th>
                                <th rowspan="2" class="text-center">Action</th>
                            </tr>
                            <tr>
                                <th class="text-center">From</th>
                                <th class="text-center">To</th>
                                <th class="text-center">From</th>
                                <th class="text-center">To</th>
                                <th class="text-center">From</th>
                                <th class="text-center">To</th>
                                <th class="text-center">From</th>
                                <th class="text-center">To</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </form>
</div>