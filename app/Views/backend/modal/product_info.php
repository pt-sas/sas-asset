<div class="modal fade" id="modal_product_info">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Product Info</h4>
                <button type="button" class="close x_form" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="product_info">
                <form class="form-horizontal" id="form_product_info">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group form-inline">
                                <label for="name" class="col-sm-3 col-form-label">Name </label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control input-full" id="name" name="name">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group form-inline">
                                <label for="md_category_id" class="col-sm-3 col-form-label">Group Asset </label>
                                <div class="col-sm-9">
                                    <select class="form-control select-data" id="md_groupasset_id" name="md_groupasset_id" data-url="groupasset/getList">
                                        <option value="">Select Group Asset</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group form-inline">
                                <label for="md_brand_id" class="col-sm-3 col-form-label">Brand </label>
                                <div class="col-sm-9">
                                    <select class="form-control select-data" id="md_brand_id" name="md_brand_id" data-url="brand/getList">
                                        <option value="">Select Brand</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group form-inline">
                                <label for="md_category_id" class="col-sm-3 col-form-label">Category </label>
                                <div class="col-sm-9">
                                    <select class="form-control select-data" id="md_category_id" name="md_category_id" data-url="category/getList">
                                        <option value="">Select Category</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group form-inline">
                                <label for="md_subcategory_id" class="col-sm-3 col-form-label">Subcategory </label>
                                <div class="col-sm-9">
                                    <select class="form-control select-data" id="md_subcategory_id" name="md_subcategory_id" data-url="subcategory/getList">
                                        <option value="">Select Subcategory</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group form-inline">
                                <label for="md_type_id" class="col-sm-3 col-form-label">Type </label>
                                <div class="col-sm-9">
                                    <select class="form-control select-data" id="md_type_id" name="md_type_id" data-url="type/getList">
                                        <option value="">Select Type</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" class="form-control" id="isfree" name="isfree">
                    </div>
                </form>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group table-responsive">
                            <table class="table-line table-head-bg-primary table-bordered table-hover table-pointer table_info" style="width: 100%">
                                <thead>
                                    <tr>
                                        <th class="text-center">ID</th>
                                        <th class="text-center">#</th>
                                        <th class="text-center">Name</th>
                                        <th class="text-center">Qty</th>
                                        <th class="text-center">Unit Price</th>
                                        <th class="text-center">Spare</th>
                                        <th class="text-center">Employee</th>
                                        <th class="text-center">Specification</th>
                                        <th class="text-center">Description</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer d-flex justify-content-between mb-3">
                <div>
                    <button type="button" class="btn btn-icon btn-round btn-primary btn_requery_info" data-toggle="tooltip" data-placement="top" title="ReQuery">
                        <i class="fas fa-sync"></i>
                    </button>
                </div>
                <div>
                    <button type="button" class="btn btn-icon btn-round btn-danger btn_close_info" data-toggle="tooltip" data-placement="top" title="Cancel" data-dismiss="modal">
                        <i class="fas fa-times"></i>
                    </button>
                    <button type="button" class="btn btn-icon btn-round btn-success btn_save_info" data-toggle="tooltip" data-placement="top" title="OK">
                        <i class="fas fa-check"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>