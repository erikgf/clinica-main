{{#.}}
    <tr>
        <td>
            <div class="input-group-prepend">
                <button type="button" class="btn btn-info btn-sm dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                Opc.
                </button>
                <div class="dropdown-menu" style="">
                    {{#id_atencion_medica}}
                    <a class="dropdown-item" target="_blank" href="../../../impresiones/ticket.atencion.php?id={{this}}" title="Ver Ticket"><i class="fa fa-eye"></i> TICKET</a>
                    <a class="dropdown-item" target="_blank" href="../../../impresiones/ticket.atencion.sinprecios.php?id={{this}}" title="Ver Ticket Consulta"><i class="fa fa-eye"></i> TICKET (CONSULTA)</a>
                    {{/id_atencion_medica}}

                    {{#iddocumento_electronico}}
                    <a class="dropdown-item" rel="noopener noreferrer" target="_blank" href="../../../impresiones/ticket.comprobante.php?id={{this}}" title="Ver Ticket"><i class="fa fa-file"></i> COMPROBANTE</a>
                    {{/iddocumento_electronico}}
                </div>
            </div>
        </td>
        <td class="text-center">
            {{#if_ es_ingreso '==' '1'}}
                <span class="badge bg-green">INGRESO</span>
            {{else}}
                <span class="badge bg-red">EGRESO</span>
            {{/if_}}
        </td>
        <td>{{movimiento}}</td>
        <td>{{cliente}}</td>
        <td>S/ {{monto_efectivo}}</td>
        <td>S/ {{monto_deposito}}</td>
        <td>S/ {{monto_tarjeta}}</td>
        <td>S/ {{monto_credito}}</td>
    </tr>
{{else}}
    <tr>
        <td colspan="15" class="text-center">
            <i>Sin registros que mostrar</i>   
        </td>
    </tr>
{{/.}}
