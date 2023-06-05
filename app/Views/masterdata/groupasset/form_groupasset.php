<div class="card-body card-form">
    <form class="form-horizontal" id="form_brand">
        <?= csrf_field(); ?>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="value">Group Asset Code <span class="required">*</span></label>
                    <input type="text" class="form-control code" id="value" name="value" readonly>
                    <small class="form-text text-danger" id="error_value"></small>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="name">Name <span class="required">*</span></label>
                    <input type="text" class="form-control" id="name" name="name">
                    <small class="form-text text-danger" id="error_name"></small>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="initialcode">Initial Code <span class="required">*</span></label>
                    <input type="text" class="form-control" id="initialcode" name="initialcode">
                    <small class="form-text text-danger" id="error_initialcode"></small>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="usefullife">Useful Life <span class="required">*</span></label>
                    <input type="text" class="form-control number" id="usefullife" name="usefullife">
                    <small class="form-text text-danger" id="error_usefullife"></small>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="description">Description </label>
                    <textarea type="text" class="form-control" id="description" name="description" rows="2"></textarea>
                </div>
                <div class="form-group">
                    <?php foreach ($ref_list as $row) : ?>
                        <div class="custom-control custom-radio">
                            <?php if ($row->value === "DB") : ?>
                                <input type="radio" id="<?= strtolower($row->name) ?>" name="depreciationtype" class="custom-control-input" value="<?= $row->value ?>" checked>
                            <?php else : ?>
                                <input type="radio" id="<?= strtolower($row->name) ?>" name="depreciationtype" class="custom-control-input" value="<?= $row->value ?>">
                            <?php endif; ?>
                            <label class="custom-control-label" for="<?= strtolower($row->name) ?>"><?= $row->name ?></label>
                        </div>
                    <?php endforeach; ?>
                    <small class="form-text text-danger" id="error_depreciationtype"></small>
                </div>
                <div class="form-check">
                    <label class="form-check-label">
                        <input type="checkbox" class="form-check-input active" id="isactive" name="isactive">
                        <span class="form-check-sign">Active</span>
                    </label>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="md_sequence_id">Document Sequence <span class="required">*</span></label>
                    <select class="form-control select-data" id="md_sequence_id" name="md_sequence_id" data-url="sequence/getList" default-id="<?= $sequence->getSequenceId() ?>" default-text="<?= $sequence->getName() ?>" disabled>
                        <option value="">Select Document Sequence</option>
                    </select>
                    <small class="form-text text-danger" id="error_md_sequence_id"></small>
                </div>
            </div>
        </div>
    </form>
</div>