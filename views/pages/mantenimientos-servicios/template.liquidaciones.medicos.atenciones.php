{{#.}}
    <tr>
        <td>{{codigo}}</td>
        <td>{{nombre_servicio}}</td>
        <td class="text-center">{{cantidad_servicios}}</td>
        <td class="text-right">{{subtotal_sin_igv}}</td>
        <td class="text-right">{{sin_igv}}</td>
    </tr>
{{else}}
    <tr>
        <td colspan="12" class="text-center"><i>Sin datos que mostrar</i></td>
    </tr>
{{/.}}