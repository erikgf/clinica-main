<div class="card HistorialPaciente_container">
    <div class="card-header">
        <h3 class="card-title">Historial de Paciente</h3>
    </div>
    <div class="card-body">
        <div id="blk-historial-accordion">
            {{#each data}}
                <div class="card card-primary mb-1">
                    <div class="card-header">
                        <h4 class="card-title w-100">
                            <a class="d-block w-100 collapsed" role="button" data-toggle="collapse" href="#blk-historial-{{id_area}}" aria-expanded="false">
                                {{area}} ({{cantidad_registros}})
                            </a>
                        </h4>
                    </div>
                    <div id="blk-historial-{{id_area}}" class="collapse" data-parent="#blk-historial-accordion">
                        <div class="card-body">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Examen</th>
                                        <th>Médico</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{#items}}
                                    <tr>
                                        <td>{{fecha_atencion}}</td>
                                        <td>{{examen}}</td>
                                        <td><span class="badge bg-primary">{{medico}}</span></td>
                                    </tr>
                                    {{/items}}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            {{else}}
                <div>
                    <h6 class="text-center">Sin atenciones registradas</h6>
                </div>
            {{/each}}
        </div>
    </div>
</div>