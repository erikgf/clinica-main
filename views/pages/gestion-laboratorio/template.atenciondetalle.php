{{#registros}}
    <tr data-id="{{id_atencion_medica_servicio}}" data-fuemuestreadoantes="{{fue_muestreado}}">
        <td class="text-center">
            {{#if_ fue_muestreado '==' '1'}}
                <button title="Registrar Resultados" data-id="{{id_atencion_medica_servicio}}" class="btn-registraresultados btn btn-sm btn-primary"><i class="fa fa-edit"></i></button>
            {{/if_}}
        </td>
        <td  class="text-center"><input type="checkbox" {{#if_ numero_impresiones_laboratorio '<=' 0}}checked{{/if_}} {{#if_ fue_validado '==' '0'}}disabled{{/if_}} /><b title="Cantidad de veces impreso.">({{numero_impresiones_laboratorio}})</b></td>
        <td class="text-center">
            {{#fecha_hora_muestra}}
                {{#if_ ../fue_muestreado '==' '0'}} <input type="checkbox" class="chkmuestra" checked> {{else}} SÍ {{/if_}} 
            {{else}}  
                 <input type="checkbox" class="chkmuestra" checked>
            {{/fecha_hora_muestra}}
        </td>
        <td>{{nombre_servicio}}</td>
        <td class="text-center input-group-sm" style="font-size:small"  title="Registrado por: {{usuario_muestra}}">
            {{#if_ fue_muestreado '==' '1'}}
                {{fecha_hora_muestra}}
            {{else}}
                <input style="max-width:160px;" type="datetime-local" class="text-center txt-fechahoramuestra form-control" value="{{fecha_hora_hoy_muestra}}"/>
            {{/if_}}
        </td>
        <td class="text-center input-group-sm" style="font-size:small"  title="Registrado por: {{usuario_entrega}}">
            {{#if_ fue_muestreado '==' '1'}}
                {{#fecha_hora_entrega}}
                    {{this}}
                {{else}}
                    <input style="max-width:160px;" type="datetime-local" class="text-center txt-fechahoraentrega form-control" value="{{fecha_hora_hoy_resultado}}"/>
                {{/fecha_hora_entrega}}
            {{else}}
                <input style="max-width:160px;" type="datetime-local" class="text-center txt-fechahoraentrega form-control" value="{{fecha_hora_hoy_resultado}}"/>
            {{/if_}}
        </td>
        <td class="text-center" style="font-size:small" title="Registrado por: {{usuario_resultado}}">{{fecha_hora_resultado}}</td>
        <td class="text-center" style="font-size:small" title="Registrado por: {{usuario_validado}}">{{fecha_hora_validado}}</td>
    </tr>
{{else}}
    <tr class="not-tr">
        <td class="text-center" colspan="15"><i>¡No hay examenes que mostrar!</i></td>
    </tr>
{{/registros}}

