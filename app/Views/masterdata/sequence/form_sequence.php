<div class="card-body card-form">
    <form class="form-horizontal" id="form_sequence">
        <?= csrf_field(); ?>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="name">Name <span class="required">*</span></label>
                    <input type="text" class="form-control" id="name" name="name">
                    <small class="form-text text-danger" id="error_name"></small>
                </div>
                <div class="form-group">
                    <label for="description">Description </label>
                    <textarea type="text" class="form-control" id="description" name="description" rows="2"></textarea>
                </div>
            </div>
            <div class="col-md-2 mt-4">
                <div class="form-check">
                    <label class="form-check-label">
                        <input type="checkbox" class="form-check-input active" id="isactive" name="isactive">
                        <span class="form-check-sign">Active</span>
                    </label>
                </div>
            </div>
            <div class="col-md-2 mt-4">
                <div class="form-check">
                    <label class="form-check-label">
                        <input type="checkbox" class="form-check-input" id="isautosequence" name="isautosequence" show-field="incrementno, currentnext, startnewyear, startno" hide-field="vformat">
                        <span class="form-check-sign">Auto numbering</span>
                    </label>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="vformat">Value Format </label>
                    <input type="text" class="form-control" id="vformat" name="vformat">
                </div>
                <div class="form-group">
                    <label for="incrementno">Increment <span class="required">*</span></label>
                    <input type="text" class="form-control" id="incrementno" name="incrementno" value="<?= $incrementno ?>">
                    <small class="form-text text-danger" id="error_incrementno"></small>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="maxvalue">Max Value <span class="required">*</span></label>
                    <input type="text" class="form-control" id="maxvalue" name="maxvalue" value="<?= $maxvalue ?>" readonly>
                    <small class="form-text text-danger" id="error_maxvalue"></small>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="currentnext">Current Next <span class="required">*</span></label>
                    <input type="text" class="form-control" id="currentnext" name="currentnext" value="<?= $currentnext ?>">
                    <small class="form-text text-danger" id="error_currentnext"></small>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="decimalpattern">Decimal Pattern </label>
                    <input type="text" class="form-control" id="decimalpattern" name="decimalpattern">
                </div>
            </div>
            <!-- <div class="col-md-12">
                <div class="form-group">
                    <label for="prefix">Prefix </label>
                    <input type="text" class="form-control" id="prefix" name="prefix" title="Prefix before the sequence number">
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label for="suffix">Suffix </label>
                    <input type="text" class="form-control" id="suffix" name="suffix" title="Suffix after the number">
                </div>
            </div> -->
            <div class="col-md-6 mt-4">
                <div class="form-check">
                    <label class="form-check-label">
                        <input type="checkbox" class="form-check-input" id="isgassetlevelsequence" name="isgassetlevelsequence" show-field="gassetcolumn">
                        <span class="form-check-sign">Group Asset Level</span>
                    </label>
                    <small class="form-text text-danger" id="error_isgassetlevelsequence"></small>
                </div>
                <div class="form-check">
                    <label class="form-check-label">
                        <input type="checkbox" class="form-check-input" id="iscategorylevelsequence" name="iscategorylevelsequence" show-field="categorycolumn">
                        <span class="form-check-sign">Category Level</span>
                    </label>
                </div>
                <div class="form-check">
                    <label class="form-check-label">
                        <input type="checkbox" class="form-check-input" id="startnewyear" name="startnewyear" show-field="startnewmonth, datecolumn">
                        <span class="form-check-sign">Restart sequence every Year</span>
                    </label>
                </div>
                <div class="form-check">
                    <label class="form-check-label">
                        <input type="checkbox" class="form-check-input" id="startnewmonth" name="startnewmonth">
                        <span class="form-check-sign">Restart sequence every Month</span>
                    </label>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="gassetcolumn">Group Asset Column </label>
                    <input type="text" class="form-control" id="gassetcolumn" name="gassetcolumn">
                </div>
                <div class="form-group">
                    <label for="categorycolumn">Category Column </label>
                    <input type="text" class="form-control" id="categorycolumn" name="categorycolumn">
                </div>
                <div class="form-group">
                    <label for="datecolumn">Date Column <span class="required">*</span></label>
                    <input type="text" class="form-control" id="datecolumn" name="datecolumn">
                    <small class="form-text text-danger" id="error_datecolumn"></small>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="startno">Start No <span class="required">*</span></label>
                    <input type="text" class="form-control" id="startno" name="startno" value="<?= $startno ?>">
                    <small class="form-text text-danger" id="error_startno"></small>
                </div>
            </div>
        </div>
    </form>
</div>