
{{#.}}
    <tr>
        <td>{{nombre_servicio}} 
            {{#descripcion}}
                <br><small>{{this}}</small>
            {{/descripcion}}
            <div style="font-size:12px;padding-left:22px">
                {{#servicios_paquete}}
                    <div><i class="fa fa-check"></i> {{nombre_servicio}}</div>
                {{/servicios_paquete}}
            </div>
        </td>
        <td class="text-right">S/ {{subtotal}}</td>
    </tr>
{{/.}}