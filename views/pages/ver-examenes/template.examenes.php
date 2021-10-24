{{#.}}
    <tr >
        <td class="text-center">{{fecha_atencion}}</td>
        <td  class="text-center">{{recibo}}</td>
        <td>{{nombre_paciente}}</td>
        <td>{{area}}</td>
        <td>{{examen}}</td>
        <td class="text-center">S/ {{round monto_examen 2}}</td>
        <td class="text-center"><b>S/ {{round monto 2}} {{#if_ monto_descuento '>' 0.00}} <span class="text-red">(+ {{monto_descuento}} Dscto.) </span>{{/if_}}</b></td>
        <td class="text-center font-weight-bold {{#if_ monto_deuda '>' '0.00'}}text-red{{/if_}}">S/ {{round monto_deuda 2}} 
            {{#if_ veces_amortizacion '>' 0}}  
                <button tabindex="0" type="button" data-html="true" data-popoveron="0" data-id="{{id_atencion_medica}}" data-content="Cargando..."  data-toggle="popover" class="btn btn-xs btn-info onmostrar-pagos" title="Mostrando Pagos"><span class="fa fa-eye"></span></button>
            {{/if_}}
        </td>
        <td>{{metodo_pago}}</td>
        <td {{#if_ fue_atendido '!=' '0'}} title="Registro: {{usuario_registro}}" {{/if_}} class="text-center bg-{{rotulo_color_atendido}}">{{#if_ fue_atendido '!=' '0'}}{{fecha_atendido}} {{hora_atendido}}{{else}}-{{/if_}}</td>
        <td>{{medico_realizado}}</td>
        <td>{{medico_atendido}}</td>
        <td>{{observaciones}}</td>
    </tr>
{{else}}
    <tr>
        <td colspan="15" class="text-center">
            <i>Sin registros que mostrar</i>   
        </td>
    </tr>
{{/.}}
