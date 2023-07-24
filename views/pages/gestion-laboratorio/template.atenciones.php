{{#.}}
    <tr data-id="{{id_atencion_medica}}">
        <td class="text-center">{{fecha_registro}}</td>
        <td class="text-center">
            <button type="button" class="button btn-xs bg-gradient-info btn-ver"><i class="fa fa-eye"></i></button>
            {{numero_recibo}}
        </td>
        <td>{{paciente}}</td>
        <td class="text-center">{{edad}}</td>
        <td class="text-center">{{sexo}}</td>
        <td class="text-center">{{fecha_hora_muestra}}</td>
        <td class="text-center">{{fecha_hora_resultado}}</td>
        <td class="text-center">{{fecha_hora_validado}}</td>
    </tr>
{{else}}
    <tr class="not-tr">
        <td class="text-left" colspan="15"><i>¡No hay atenciones que mostrar!</i></td>
    </tr>
{{/.}}