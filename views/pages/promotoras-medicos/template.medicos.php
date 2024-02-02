{{#.}}
    <tr data-id="{{id_medico}}">
        <td>
            <div class="input-group-prepend">
                <button type="button" class="btn btn-info btn-sm dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                Opc.
                </button>
                <div class="dropdown-menu p-0">
                    <button class="dropdown-item bg-warning btn-editar" data-id="{{id_medico}}" title="Editar"><i class="fa fa-edit"></i> Editar</button>
                    <button class="dropdown-item bg-danger btn-eliminar" data-id="{{id_medico}}" title="Eliminar"><i class="fa fa-trash"></i> Eliminar</button>
                </div>
            </div>
        </td>
        <td>{{sede}}</td>
        <td>{{medico}}</td>
        <td>{{colegiatura}}</td>
        <td>{{rne}}</td>
        <td>{{telefonos}}</td>
        <td>{{domicilio}}</td>
        <td>{{promotora}}</td>
        <td>{{especialidad}}</td>
    </tr>
{{/.}}