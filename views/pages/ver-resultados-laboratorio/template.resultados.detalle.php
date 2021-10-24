{{#.}}
    <tr data-id="{{id_atencion_medica_servicio}}">
        <td class="text-center">
            {{#if_ esta_validado '==' 1}}<input class="chk-imprimir" type="checkbox" {{#if_ numero_impresiones_laboratorio '<=' 0}}checked{{/if_}}> <b title="Cantidad de veces impreso.">({{numero_impresiones_laboratorio}})</b>{{/if_}}
        </td>
        <td>{{nombre_servicio}}</td>
        <td class="text-center bg-{{#if_ esta_validado '==' 1}}gradient-green{{else}}gradient-red{{/if_}}">{{#if_ esta_validado '==' 1}}SÍ{{else}}NO{{/if_}}</td>
        <td class="text-center">{{fecha_hora_muestra}}</td>
        <td class="text-center">{{fecha_hora_entrega}}</td>
    </tr>
{{else}}
    <tr>
        <td colspan="15" class="text-center">
            <i>Sin examenes que mostrar</i>   
        </td>
    </tr>
{{/.}}
