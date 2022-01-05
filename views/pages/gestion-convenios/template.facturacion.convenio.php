{{#.}}
    <tr data-id="{{iddocumento_electronico}}" data-comprobante="{{serie}}-{{comprobante}}">
        <td>
            <div class="input-group-prepend">
                <span style="display:none">{{iddocumento_electronico}}</span>
                <button type="button" class="btn btn-info btn-sm dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                Opc.
                </button>
                <div class="dropdown-menu p-0">
                    {{#iddocumento_electronico}}
                    <a class="dropdown-item" rel="noopener noreferrer" target="_blank" href="../../../impresiones/comprobante.a4.pdf.php?id={{this}}" title="Ver Comprobante"><i class="fa fa-eye"></i> VER COMPROBANTE</a>
                    {{/iddocumento_electronico}}

                    {{#if_ estado_anulado '==' '0'}}
                        
                        {{#if_ cdr_estado '==' '0'}}
                            {{#if_ idtipo_comprobante '==' '01'}}
                            <a  class="dropdown-item btn-modificarpornotacredito bg-red"  href="#" title="MODIFICAR NOTA CRÉDITO"><i class="fa fa-trash"></i> MODIFICAR (NOTA CRÉDITO)</a>
                            {{/if_}}
                        {{else}}

                            {{#if_ cdr_estado '<' 2000}}
                            <a class="dropdown-item btn-enviarsunat bg-green"  href="#" title="Enviar SUNAT"><i class="fa fa-file"></i> ENVIAR SUNAT</a>
                            {{else}}
                                {{^cdr_estado}}
                                <a class="dropdown-item btn-enviarsunat bg-green"  href="#" title="Enviar SUNAT"><i class="fa fa-file"></i> ENVIAR SUNAT</a>
                                {{/cdr_estado}}
                            {{/if_}}
                        {{/if_}}
                    {{/if_}}
                </div>
            </div>
        </td>
        <td>{{numero_documento_cliente}}</td>
        <td>{{cliente}}</td>
        <td>{{idtipo_comprobante}}</td>
        <td>{{serie}}-{{comprobante}}</td>
        <td class="text-center">{{fecha_emision}}</td>
        <td class="text-center">S/ {{total_gravadas}}</td>
        <td class="text-center">S/ {{total_igv}}</td>
        <td class="text-center">S/ {{importe_total}}</td>
        <td class="text-center" style="max-width: 200px;">  
            {{#cdr_estado}}
                <span class="badge bg-{{#if_ this '==' '0'}}green{{else}}red{{/if_}} ">{{#if_ this '==' '0'}}ACEPTADO{{else}}RECHAZADO{{/if_}}</span>
                <div class="" style="font-size:small;word-wrap: break-word;">
                    {{../cdr_descripcion}} 
                </div> 
            {{else}}
                <span class="badge bg-gray">NO ENVIADO</span>
            {{/cdr_estado}}
            
        </td>
    </tr>
{{/.}}

