{{#.}}
    <tr data-id="{{id_atencion_medica}}">
        <td class="text-center">{{fecha_registro}}</td>
        <td class="text-center">{{numero_recibo}}</td>
        <td>{{paciente}}</td>
        <td class="text-center">{{edad}}</td>
        <td class="text-center">{{sexo}}</td>
        <td class="text-center">{{fecha_hora_muestra}}</td>
        <td class="text-center">{{fecha_hora_resultado}}</td>
        <td class="text-center">{{fecha_hora_validado}}</td>
    </tr>
{{else}}
    <tr class="not-tr">
        <td class="text-center" colspan="15"><i>¡No hay atenciones que mostrar!</i></td>
    </tr>
{{/.}}