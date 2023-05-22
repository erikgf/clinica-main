{{#.}}
    <tr>
        <td>
            <div class="input-group-prepend">
                <button title="ID: {{id}}" type="button" class="btn btn-info btn-sm dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                Opc.
                </button>
                <div class="dropdown-menu" style="">
                    <a  class="dropdown-item" href="#">ID: {{id}}</a>
                    {{#id_atencion_medica}}
                    <a class="dropdown-item" target="_blank" href="../../../impresiones/ticket.atencion.php?id={{this}}" title="Ver Ticket"><i class="fa fa-eye"></i> TICKET</a>
                    <a class="dropdown-item" target="_blank" href="../../../impresiones/ticket.atencion.sinprecios.php?id={{this}}" title="Ver Ticket Consulta"><i class="fa fa-eye"></i> TICKET (CONSULTA)</a>
                    {{/id_atencion_medica}}

                    {{#iddocumento_electronico}}
                    <a class="dropdown-item" rel="noopener noreferrer" target="_blank" href="../../../impresiones/ticket.comprobante.php?id={{this}}" title="Ver Comprobante"><i class="fa fa-eye"></i> COMPROBANTE</a>
                    {{/iddocumento_electronico}}

                    {{#iddocumento_electronico_relacionado}}
                    <a class="dropdown-item" rel="noopener noreferrer" target="_blank" href="../../../impresiones/ticket.comprobante.php?id={{this}}" title="Ver Comprobante"><i class="fa fa-eye"></i> COMPROBANTE (SALDO PAGADO)</a>
                    {{/iddocumento_electronico_relacionado}}

                    {{#if_ es_anulable '==' 1}}
                        {{#if_ estado_anulado '==' 0}}
                            <a class="dropdown-item btn-anularmovimiento" data-tipomovimiento="{{movimiento}}" data-id="{{id}}" data-cliente="{{cliente}}" href="#" title="Anular Movimiento"><i class="fa fa-trash"></i> ANULAR MOVIMIENTO</a>
                        {{/if_}}
                    {{/if_}}
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
