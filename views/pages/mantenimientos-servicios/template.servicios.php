{{#.}}
    <tr data-id="{{id}}">
        <td>
            <div class="input-group-prepend">
                <button type="button" class="btn btn-info btn-sm dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                Opc.
                </button>
                <div class="dropdown-menu p-0">
                    <button class="dropdown-item bg-warning btn-editar"  data-idtiposervicio="{{id_tipo_servicio}}" data-id="{{id}}" title="Editar"><i class="fa fa-edit"></i> Editar</button>
                    <button class="dropdown-item bg-danger btn-eliminar" data-id="{{id}}" title="Eliminar"><i class="fa fa-trash"></i> Eliminar</button>
                </div>
            </div>
        </td>
        <td>{{id}}</td>
        <td>{{descripcion}}</td>
        <td>{{area_categoria}}</td>
        <td>{{sub_categoria}}</td>
        <td>{{tipo_servicio}}</td>
        <td>S/ {{round valor_venta 2}}</td>
        <td>S/ {{round precio_venta 2}}</td>
    </tr>
{{/.}}
