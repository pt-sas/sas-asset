<div class="modal fade" id="modal_service_part">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Service Spare Part</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="service_part_info">
                <form class="form-horizontal" id="form_service_part">
                    <?= csrf_field(); ?>
                    <div class="form-group row">
                        <label for="assetcode" class="col-lg-3 col-md-3 col-sm-4 mt-sm-2 text-right">Asset Code <span class="required-label">*</span></label>
                        <div class="col-lg-4 col-md-4 col-sm-4">
                            <input type="text" class="form-control" name="assetcode" readonly>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="product" class="col-lg-3 col-md-3 col-sm-4 mt-sm-2 text-right">Product <span class="required-label">*</span></label>
                        <div class="col-lg-4 col-md-4 col-sm-4">
                            <input type="text" class="form-control" name="product" readonly>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="id" class="col-lg-3 col-md-3 col-sm-4 mt-sm-2 text-right"></label>
                        <div class="col-lg-4 col-md-4 col-sm-4">
                            <input type="hidden" class="form-control foreignkey" name="id" readonly>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="text-right">
                                    <button type="button" name="button" class="btn btn-primary btn-sm btn-round ml-auto add_row_part" title="Add Row"><i class="fa fa-plus fa-fw"></i> Add New</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group table-responsive">
                                <table class="table-rounded table-head-bg-primary table-hover tb_service_part" id="table-rule-value" style="width: 100%">
                                    <thead>
                                        <tr>
                                            <th class="text-center">Spare Part</th>
                                            <th class="text-center">Qty</th>
                                            <th class="text-center">Unit Price</th>
                                            <th class="text-center">Line Net Amount</th>
                                            <th class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer d-flex justify-content-end mb-3">
                <button type="button" class="btn btn-outline-danger btn-round close_form_part" data-toggle="tooltip" data-placement="top" title="Close" data-dismiss="modal">Close</button>
                <button type=" button" class="btn btn-primary btn-round save_form_part" data-toggle="tooltip" data-placement="top" title="Save changes">Save changes</button>
            </div>
        </div>
    </div>
</div>