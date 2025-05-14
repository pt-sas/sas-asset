<div class="card-body card-form">
    <form class="form-horizontal" id="form_spesification">
        <?= csrf_field(); ?>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="trx_inventory_id">Asset Code <span class="required">*</span></label>
                    <select class="form-control select-data" id="trx_inventory_id" name="trx_inventory_id"
                        data-url="inventory/getAssetCode/$CPU">
                        <option value="">Select Asset Code</option>
                    </select>
                    <small class="form-text text-danger" id="error_trx_inventory_id"></small>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="product">Product</label>
                    <input type="text" class="form-control" name="product" readonly>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="processor_id">Processor</label>
                    <select class="form-control select-data" id="processor_id" name="processor_id"
                        data-url="spare-part/getList/$Processor">
                        <option value="">Select Product</option>
                    </select>
                    <small class="form-text text-danger" id="error_processor_id"></small>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="motherboard_id">Motherboard</label>
                    <select class="form-control select-data" id="motherboard_id" name="motherboard_id"
                        data-url="spare-part/getList/$MotherBoard">
                        <option value="">Select Product</option>
                    </select>
                    <small class="form-text text-danger" id="error_motherboard_id"></small>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="video_graphic_id">Video Graphic</label>
                    <select class="form-control select-data" id="video_graphic_id" name="video_graphic_id"
                        data-url="spare-part/getList/$VGA">
                        <option value="">Select Product</option>
                    </select>
                    <small class="form-text text-danger" id="error_video_graphic_id"></small>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="case_id">Casing</label>
                    <select class="form-control select-data" id="case_id" name="case_id"
                        data-url="spare-part/getList/$Case">
                        <option value="">Select Product</option>
                    </select>
                    <small class="form-text text-danger" id="error_case_id"></small>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="power_supply_id">Power Supply</label>
                    <select class="form-control select-data" id="power_supply_id" name="power_supply_id"
                        data-url="spare-part/getList/$PSU">
                        <option value="">Select Product</option>
                    </select>
                    <small class="form-text text-danger" id="error_power_supply_id"></small>
                </div>
            </div>
            <!-- <div class="col-md-6">
                <div class="form-group">
                    <label for="operation_id">Operating System</label>
                    <select class="form-control select-data" id="operation_id" name="operation_id"
                        data-url="product/getList/$Software">
                        <option value="">Select Part</option>
                    </select>
                    <small class="form-text text-danger" id="error_operation_id"></small>
                </div>
            </div> -->
            <div class="col-md-6">
                <div class="form-group">
                    <label for="description">Description </label>
                    <textarea type="text" class="form-control" id="description" name="description" rows="4"></textarea>
                </div>
                <div class="form-check">
                    <label class="form-check-label">
                        <input type="checkbox" class="form-check-input" id="diskdrive" name="diskdrive">
                        <span class="form-check-sign">Disk Drive</span>
                    </label>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <button type="button" class="btn btn-primary w-100 btn-memory">
                        Memory
                    </button>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <button type="button" class="btn btn-primary w-100 btn-storage">
                        Storage
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>