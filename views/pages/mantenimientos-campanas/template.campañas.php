{{#.}}
    <tr data-id="{{id_campaña}}">
        <td>
            <div class="input-group-prepend">
                <button type="button" class="btn btn-info btn-sm dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                Opc.
                </button>
                <div class="dropdown-menu p-0">
                    <button class="dropdown-item bg-warning btn-editar" data-id="{{id_campaña}}" title="Editar"><i class="fa fa-edit"></i> Editar</button>
                    <button class="dropdown-item bg-danger btn-eliminar" data-id="{{id_campaña}}" title="Eliminar"><i class="fa fa-trash"></i> Eliminar</button>
                </div>
            </div>
        </td>
        <td>{{sede}}</td>
        <td>{{nombre}}</td>
        <td>{{fecha_inicio}}</td>
        <td>{{fecha_fin}}</td>
        <td>{{#if_ estado '==' 1}}<span class="badge bg-success">ACTIVO</span>{{else}}<span class="badge bg-danger">INACTIVO</span>{{/if_}}</td>
    </tr>
{{/.}}