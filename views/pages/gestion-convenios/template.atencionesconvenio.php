{{#.}}
    <tr data-id="{{id_atencion_medica}}"  class="{{#if_ existen_comprobantes '>' 0 }}bg-gradient-cyan{{/if_}}">
        <td>
            <div class="input-group-prepend">
                <button type="button" class="btn btn-info btn-sm dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                Opc.
                </button>
                <div class="dropdown-menu p-0">
                    <a class="dropdown-item btn-crearcomprobante" data-numeroticket="{{numero_ticket}}" href="#" title="Crea Comprobante"><i class="fa fa-file"></i> CREAR COMPROBANTE</a>
                    {{#id_atencion_medica}}
                    <a class="dropdown-item"  rel="noopener noreferrer" target="_blank" href="../../../impresiones/ticket.atencion.php?id={{this}}" title="Ver Ticket"><i class="fa fa-eye"></i> TICKET</a>
                    <a class="dropdown-item"  rel="noopener noreferrer" target="_blank" href="../../../impresiones/ticket.atencion.sinprecios.php?id={{this}}" title="Ver Ticket Consulta"><i class="fa fa-eye"></i> TICKET (CONSULTA)</a>
                    {{/id_atencion_medica}}
                    {{#id_documento_electronico_factura}}
                    <a class="dropdown-item"  rel="noopener noreferrer" target="_blank" href="../../../impresiones/comprobante.a4.pdf.php?id={{this}}" title="Ver Factura"><i class="fa fa-eye"></i> VER FACTURA</a>
                    {{/id_documento_electronico_factura}}
                    {{#id_documento_electronico_notacredito}}
                    <a class="dropdown-item"  rel="noopener noreferrer" target="_blank" href="../../../impresiones/comprobante.a4.pdf.php?id={{this}}"  title="Ver Nota Crédito"><i class="fa fa-file"></i> VER NOTA CRÉDITO</a>
                    {{/id_documento_electronico_notacredito}}
                </div>
            </div>
        </td>
        <td>{{numero_ticket}}</td>
        <td>{{fecha_registro}}</td>
        <td>{{empresa_convenio}}</td>
        <td>{{paciente}}</td>
        <td>{{porcentaje_convenio}}%</td>
        <td>S/ {{round importe_total 2}}</td>
        <td><b>S/ {{round monto_cubierto 2}}</b></td>
    </tr>
{{/.}}