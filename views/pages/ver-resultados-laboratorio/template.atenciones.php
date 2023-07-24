{{#.}}
    <tr data-id="{{id_atencion_medica}}">
        <td></td>
        <td>
            <button data-id="{{id_atencion_medica}}" data-recibo="{{numero_recibo}}" data-paciente="{{nombre_paciente}}" title="Imprimir" data-id="{{id_atencion_medica}}" class="btn-preimprimir btn btn-sm {{#if_ cantidad_validados '==' cantidad_total}}btn-success{{else}}btn-danger{{/if_}}"><i class="fa fa-print"></i> IMPRIMIR</button>
        </td>
        <td>{{numero_recibo}}</td>
        <td>{{nombre_paciente}}</td>
        <td>{{#if_ edad_anios '<=' 0}} {{edad_meses}} meses {{else}} {{edad_anios}} años{{/if_}}</td>
        <td>{{sexo}}</td>
        <td class="text-center">{{fecha_atencion}}</td>
        <td class="text-center">{{cantidad_validados}}/{{cantidad_total}}</td>
    </tr>
{{else}}
    <tr>
        <td colspan="15" class="text-center">
            <i>Sin registros que mostrar</i>   
        </td>
    </tr>
{{/.}}
