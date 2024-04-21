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
        <div class="row">
            {{#each data}}
            <div class="col-md-4 col-xs-12 col-lg-2">
                <h5 class="font-weight-bold">{{area}}: </h4> 
                <p><b style="font-size:1em" class="badge badge-success">REALIZADOS: {{cantidad_atendidos}}</b> <b style="font-size:1em" class="badge badge-secondary">PENDIENTES: {{cantidad_pendientes}}</b> </p>
            </div>
            {{/each}}
        </div>
    </div>
</div>