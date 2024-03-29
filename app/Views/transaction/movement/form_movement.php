<div class="card-body card-form">
    <form class="form-horizontal" id="form_movement">
        <?= csrf_field(); ?>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="documentno">Document No <span class="required">*</span></label>
                    <input type="text" class="form-control" id="documentno" name="documentno" placeholder="[auto]" readonly>
                    <small class="form-text text-danger" id="error_documentno"></small>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="ref_movement_id">Reference Movement </label>
                    <select class="form-control select-data" id="ref_movement_id" name="ref_movement_id" data-url="movement/getList" disabled>
                        <option value="">Select Reference Movement</option>
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="movementdate">Movement Date <span class="required">*</span></label>
                    <input type="text" class="form-control datepicker" id="movementdate" name="movementdate" value="<?= $today; ?>" placeholder="Movement Date">
                    <small class="form-text text-danger" id="error_movementdate"></small>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="movementtype">Movement Type </label>
                    <select class="form-control select-data" id="movementtype" name="movementtype" data-url="reference/getList/$MovementType" default-id="<?= $ref_list->value ?>" default-text="<?= $ref_list->name ?>" disabled>
                        <option value="">Select Movement Type</option>
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="md_branch_id">Branch <span class="required">*</span></label>
                    <select class="form-control select-data" id="md_branch_id" name="md_branch_id" data-url="branch/getList" default-id="<?= $branch ? $branch['id'] : "" ?>" default-text="<?= $branch ? $branch['text'] : "" ?>" <?= $branch ? "disabled" : "" ?>>
                        <option value="">Select Branch</option>
                    </select>
                    <small class="form-text text-danger" id="error_md_branch_id"></small>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="md_branchto_id">Branch To <span class="required">*</span></label>
                    <select class="form-control select-data" id="md_branchto_id" name="md_branchto_id" data-url="branch/getList">
                        <option value="">Select Branch</option>
                    </select>
                    <small class="form-text text-danger" id="error_md_branchto_id"></small>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="md_divisionto_id">Division To <span class="required">*</span></label>
                    <select class="form-control select-data" id="md_divisionto_id" name="md_divisionto_id" data-url="division/getList">
                        <option value="">Select Division To</option>
                    </select>
                    <small class="form-text text-danger" id="error_md_divisionto_id"></small>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="movementstatus">Status </label>
                    <select class="form-control select2" id="movementstatus" name="movementstatus" style="width: 100%;" <?= !$default_role ? "disabled" : "" ?>>
                        <option value="">Select Status</option>
                        <?php foreach ($status as $row) : ?>
                            <option value="<?= $row->getStatusId() ?>"><?= $row->getName() ?></option>
                        <?php endforeach; ?>
                    </select>
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
                                <th rowspan="2" class="text-center">Action</th>
                                <th rowspan="2" class="text-center">Asset Code</th>
                                <th rowspan="2" class="text-center">Product</th>
                                <th rowspan="2" class="text-center">Status</th>
                                <th rowspan="2" class="text-center">New</th>
                                <th colspan="2" class="text-center">Employee</th>
                                <th colspan="2" class="text-center">Branch</th>
                                <th colspan="2" class="text-center">Division</th>
                                <th colspan="2" class="text-center">Room</th>
                                <th rowspan="2" class="text-center">Description</th>
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