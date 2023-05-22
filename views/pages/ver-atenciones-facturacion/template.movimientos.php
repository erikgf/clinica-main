{{#.}}
    <tr {{#if_ estado_anulado '==' '1'}} style="color: red !important;"{{/if_}}>
        <td class="text-center" style="max-width: 200px;">  
            <span class="badge bg-{{cdr_estado_color}}">{{cdr_estado_descripcion}}</span>
            <div class="" style="font-size:small;word-wrap: break-word;">
                {{cdr_descripcion}} 
            </div> 
        </td>
        <td>{{idtipo_comprobante}}</td>
        <td>{{serie}}-{{comprobante}}</td>
        <td>{{fecha_emision}}</td>
        <td>{{cliente}}</td>
        <td>{{numero_documento_cliente}}</td>
        <td>{{metodo_pago}}</td>
        <td>{{porcentaje_igv}}</td>
        <td>S/{{total_gravadas}}</td>
        <td>S/{{total_igv}}</td>
        <td>S/{{importe_total}}</td>
        <td>{{fecha_modificado}}</td>
        <td>{{td_modifica}}</td>
        <td>{{serie_modifica}}</td>
        <td>{{correlativo_modifica}}</td>
    </tr>
{{else}}
    <tr>
        <td colspan="20" class="text-center">
            <i>Sin registros que mostrar</i>   
        </td>
    </tr>
{{/.}}