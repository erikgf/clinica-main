{{#.}}
    <tr>
        <td class="text-center">
            <button class="btn btn-xs bg-danger btn-eliminarcuota" type="button" title="Eliminar"><i class="fa fa-trash"></i></button>
        </td>
        <td class="text-center">{{numero_cuota}}</td>
        <td class="input-group-sm"><input type="number" required step="0.01" value="{{monto_cuota}}" class="form-control input-sm"/></td>
        <td class="input-group-sm"><input type="date" required class="form-control input-sm txt-fechapago"/></td>
    </tr>
{{/.}}

