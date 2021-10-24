{{#.}}
    <tr>
        <td class="text-center">{{numero_recibo}}</td>
        <td class="text-center">{{fecha_registro}}</td>
        <td>{{paciente}}</td>
        <td>{{#if_ tipo_comprobante '!=' ''}}
                {{tipo_comprobante}}<br>
                <a  rel="noopener noreferrer" target="_blank" href="../../../impresiones/ticket.comprobante.php?id={{iddocumento_electronico}}" title="Ver Comprobante"><i class="fa fa-download"></i> {{comprobante}}</a>
            {{else}}
            -
            {{/if_}}  
        </td>
        <td>S/ {{monto_acuenta}}</td>
        <td>{{caja_monto_acuenta}}</td>
        <td class="font-weight-bold {{#if_ monto_saldo '>' '0.00'}}text-red{{/if_}}">S/ {{round monto_saldo 2}} 
            {{#if_ veces_amortizacion '>' 0}}  
                <button tabindex="0" type="button" data-html="true" data-popoveron="0" data-id="{{id_atencion_medica}}" data-content="Cargando..."  data-toggle="popover" class="btn btn-xs btn-info onmostrar-pagos" title="Mostrando Pagos"><span class="fa fa-eye"></span></button>
            {{/if_}}
        </td>
        <td>S/ {{monto_total}}</td>
    </tr>
{{else}}
    <tr>
        <td colspan="15" class="text-center">
            <i>Sin registros que mostrar</i>   
        </td>
    </tr>
{{/.}}
