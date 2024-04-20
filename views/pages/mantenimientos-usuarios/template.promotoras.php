{{#.}}
    <tr data-id="{{id_promotora}}">
        <td>
            <div class="input-group-prepend">
                <button type="button" class="btn btn-info btn-sm dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                Opc.
                </button>
                <div class="dropdown-menu p-0">
                    <button class="dropdown-item bg-success btn-cambiarclave" data-id="{{id_promotora}}"  data-nombres="{{nombres}}"  title="Cambiar Clave"><i class="fa fa-lock"></i> Cambiar Clave</button>
                    <button class="dropdown-item bg-warning btn-editar" data-id="{{id_promotora}}" title="Editar"><i class="fa fa-edit"></i> Editar</button>
                </div>
            </div>
        </td>
        <td>{{numero_documento}}</td>
        <td>{{nombres}}</td>
        <td>{{#if_ estado_acceso '==' 'A'}}SÍ{{else}}NO{{/if_}}</td>
    </tr>
{{/.}}