{{#.}}
    <tr data-id="{{id_colaborador}}">
        <td>
            <div class="input-group-prepend">
                <button type="button" class="btn btn-info btn-sm dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                Opc.
                </button>
                <div class="dropdown-menu p-0">
                    <button class="dropdown-item bg-success btn-cambiarclave" data-id="{{id_colaborador}}"  data-nombres="{{nombres_apellidos}}"  title="Cambiar Clave"><i class="fa fa-lock"></i> Cambiar Clave</button>
                    <button class="dropdown-item bg-warning btn-editar" data-id="{{id_colaborador}}" title="Editar"><i class="fa fa-edit"></i> Editar</button>
                    <button class="dropdown-item bg-danger btn-eliminar" data-id="{{id_colaborador}}" title="Eliminar"><i class="fa fa-trash"></i> Eliminar</button>
                </div>
            </div>
        </td>
        <td>{{numero_documento}}</td>
        <td>{{nombres_apellidos}}</td>
        <td>{{correo}}</td>
        <td>{{telefono}}</td>
        <td>{{rol}}</td>
        <td>{{#if_ estado_acceso '==' 'A'}}SÍ{{else}}NO{{/if_}}</td>
    </tr>
{{/.}}