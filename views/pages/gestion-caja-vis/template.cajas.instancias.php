{{#.}}
    <tr>
        <td>
            <button data-id="{{id}}" class="btn btn-sm btn-info btn-block btn-ver" title="Seleccionar"><i class="fa fa-eye"></i></button>
        </td>
        <td >{{usuario_caja}}</td>
        <td class="text-center">{{fecha_apertura}}</td>
        <td class="text-center">{{monto_apertura}}</td>
        <td class="text-center">{{fecha_cierre}}</td>
        <td class="text-center">{{monto_cierre}}</td>
    </tr>
{{else}}
    <tr>
        <td colspan="10" class="text-center">
            <i>Sin registros que mostrar</i>   
        </td>
    </tr>
{{/.}}