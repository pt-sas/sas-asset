<div class="row filter_page">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body card-filter">
                <form class="form-horizontal" id="filter_quotation">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <select class="form-control select-employee" name="md_employee_id">
                                    <option value="">Select Employee</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <div class="input-icon">
                                    <input type="text" class="form-control daterange" name="quotationdate" value="<?= $date_range ?>" placeholder="Date">
                                    <span class="input-icon-addon">
                                        <i class="fas fa-calendar-alt"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <select class="form-control select2" name="md_status_id" style="width: 100%;" <?= isset($default_logic->condition) ? "disabled" : "" ?>>
                                    <option value="">Select Status</option>
                                    <?php foreach ($status as $row) : ?>
                                        <option value="<?= $row->getStatusId() ?>" <?= (isset($default_logic->id) && $default_logic->id == $row->getStatusId()) && isset($default_logic->condition) ? "selected" : "" ?>><?= $row->getName() ?></option>
                                    <?php endforeach; ?>
                                </select>
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