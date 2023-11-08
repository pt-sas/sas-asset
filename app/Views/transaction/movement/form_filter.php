<div class="row filter_page">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body card-filter">
                <form class="form-horizontal" id="filter_movement">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <select class="form-control select-movementtype" name="movementtype">
                                    <option value="0">All Movement Type</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <div class="input-icon">
                                    <input type="text" class="form-control daterange" name="movementdate" value="<?= $date_range ?>" placeholder="Date">
                                    <span class="input-icon-addon">
                                        <i class="fas fa-calendar-alt"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <button type="button" class="btn btn-primary btn-sm btn-round ml-auto btn_filter" title="Filter">
                                    <i class="fas fa-search fa-fw"></i> Filter
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>