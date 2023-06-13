{{#.}}
    <tr {{#if_ estado_anulado '==' '1'}} style="color: red !important;"{{/if_}}>
        <td>
            <div class="input-group-prepend">
                <button type="button" class="btn btn-info btn-sm dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                Opc.
                </button>
                <div class="dropdown-menu">
                    {{#id_atencion_medica}}
                    <a class="dropdown-item" target="_blank" href="../../../impresiones/ticket.atencion.sinprecios.php?id={{this}}" title="Ver Ticket Consulta"><i class="fa fa-eye"></i> TICKET (CONSULTA)</a>
                    {{/id_atencion_medica}}

                    {{#iddocumento_electronico}}
                    {{#if_ ../debo_mostrar_enviar '==' 1}}
                        <a class="dropdown-item btn-enviarsunat" style="color:red;font-weight:bold" data-comprobante="{{../comprobante}}" data-id="{{this}}" href="#" title="Enviar a SUNAT"><i class="fa fa-upload"></i> ENVIAR A SUNAT</a>
                    {{/if_}}
                    <a class="dropdown-item" rel="noopener noreferrer" target="_blank" href="../../../impresiones/ticket.comprobante.php?id={{this}}" title="Ver Comprobante"><i class="fa fa-eye"></i> COMPROBANTE</a>
                    <a class="dropdown-item" rel="noopener noreferrer" target="_blank" href="../../../impresiones/comprobante.a4.pdf.php?id={{this}}" title="Ver Comprobante"><i class="fa fa-eye"></i> COMPROBANTE (A4)</a>
                    <a class="dropdown-item btn-copiarcomprobante" data-comprobante="{{../comprobante}}" data-id="{{this}}" href="#" title="Copiar Comprobante"><i class="fa fa-copy"></i> COPIAR COMPROBANTE</a>
                    {{/iddocumento_electronico}}

                    {{#iddocumento_electronico_relacionado}}
                    <a class="dropdown-item" rel="noopener noreferrer" target="_blank" href="../../../impresiones/ticket.comprobante.php?id={{this}}" title="Ver Comprobante"><i class="fa fa-file"></i> COMPROBANTE (SALDO PAGADO)</a>
                    {{/iddocumento_electronico_relacionado}}

                    <a class="dropdown-item dropdown-separator"></a>

                    {{#if_ estado_anulado '==' 0}}
                        {{#id_atencion_medica}}
                        <a class="dropdown-item btn-cambiarmedico" data-id="{{this}}" href="#" title="Cambiar Médico"><i class="fa fa-medkit"></i> CAMBIAR MÉDICO</a>
                        {{/id_atencion_medica}}

                        <a class="dropdown-item btn-canjearcomprobante" data-cliente="{{cliente}}" data-id="{{id_atencion_medica}}" href="#" title="Canjear Comprobante"><i class="fa fa-refresh"></i> CANJEAR COMPROBANTE</a>
                        <a class="dropdown-item btn-anularmovimiento" data-cliente="{{cliente}}" data-id="{{id_atencion_medica}}" href="#" title="Anular Atención"><i class="fa fa-trash"></i> ANULAR ATENCIÓN</a>
                        {{#iddocumento_electronico}}
                        
                        <!-- <a class="dropdown-item btn-anularcomprobante" data-cliente="{{../cliente}}" data-id="{{../id_atencion_medica}}" href="#" title="Anular Comprobante"><i class="fa fa-trash"></i> ANULAR COMPROBANTE</a> -->
                        {{/iddocumento_electronico}}
                    {{/if_}}

                     <a class="dropdown-item dropdown-separator"></a>

                    {{#iddocumento_electronico_nota}}
                    <a class="dropdown-item" rel="noopener noreferrer" target="_blank" href="../../../impresiones/ticket.comprobante.php?id={{this}}" title="Ver Comprobante"><i class="fa fa-file"></i> NOTA CRÉDITO</a>
                    <a class="dropdown-item" rel="noopener noreferrer" target="_blank" href="../../../impresiones/comprobante.a4.pdf.php?id={{this}}" title="Ver Comprobante"><i class="fa fa-file"></i> NOTA CRÉDITO (A4)</a>
                    {{/iddocumento_electronico_nota}}
                </div>
            </div>
        </td>
        <td class="text-center cdrestadodescripcion" style="max-width: 200px;">  
            <span class="badge bg-{{cdr_estado_color}}">{{cdr_estado_descripcion}}</span>
        </td>
        <td class="text-center">{{fecha_registro}}</td>
        <td class="text-center">{{numero_acto_medico}}</td>
        <td>{{comprobante}} {{#comprobante_nota}}<br><small style="color:black !important">NC:{{this}}</small>{{/comprobante_nota}}</td>
        <td>{{paciente}}</td>
        <td>S/ {{monto_efectivo}}</td>
        <td>S/ {{monto_deposito}}</td>
        <td>S/ {{monto_tarjeta}}</td>
        <td>S/ {{monto_credito}}</td>
        <td>S/ {{monto_total}}</td>
    </tr>
{{else}}
    <tr>
        <td colspan="15" class="text-center">
            <i>Sin registros que mostrar</i>   
        </td>
    </tr>
{{/.}}
