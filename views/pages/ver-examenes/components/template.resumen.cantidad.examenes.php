<div class="card ResumenCantidadExamenes_container">
    <div class="card-header">
        <h4 class="card-title">Resumen por Área/Categoría</h4>
        <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-minus"></i>
            </button>
        </div>
    </div>
    <div class="card-body">
        {{#each data}}
        <div class="row">
            <div class="col-sm-2 col-md-3  col-xs-6">
                <h4 class="font-weight-bold">{{area}} </h4>
            </div>
            <div class="col-sm-2 col-md-3 col-xs-6">
                <b class="badge">PENDIENTES</b> :  {{cantidad_pendientes}} exámenes.
            </div>
            <div class="col-sm-2 col-md-3  col-xs-6">
                <b class="badge badge-success">REALIZADOS</b> :  {{cantidad_realizados}} exámenes.
            </div>
            <div class="col-sm-2 col-md-3 col-xs-6">
                <b class="badge badge-danger">CANCELADOS</b> :  {{cantidad_cancelados}} exámenes.
            </div>
        </div>
        <hr>
        {{/each}}
    </div>
</div>