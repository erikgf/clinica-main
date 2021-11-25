{{#.}}
    <tr data-id="{{id}}">
        <td>
            <div class="input-group-prepend">
                <button type="button" class="btn btn-info btn-sm dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                Opc.
                </button>
                <div class="dropdown-menu p-0">
                    <button class="dropdown-item bg-warning btn-editar" data-id="{{id}}" title="Editar"><i class="fa fa-edit"></i> Editar</button>
                    {{#if_ estado '==' 'A'}}
                    <button class="dropdown-item bg-danger btn-darbaja" data-id="{{id}}" title="Dar Baja"><i class="fa fa-trash"></i> Dar Baja</button>
                    {{else}}
                    <button class="dropdown-item bg-success btn-daralta" data-id="{{id}}" title="Dar Alta"><i class="fa fa-check"></i> Dar Alta</button>
                    {{/if_}}
                </div>
            </div>
        </td>
        <td>{{numero_documento}}</td>
        <td>{{razon_social}}</td>
        <td>{{fecha_alta}}</td>
        <td>{{fecha_baja}}</td>
        <td><span class="badge bg-{{#if_ estado '==' 'A'}}green{{else}}red{{/if_}}">{{estado_rotulo}}</span></td>
    </tr>
{{/.}}