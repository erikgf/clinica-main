{{#.}}
    <tr>
        <td>{{codigo}}</td>
        <td>{{sede}}</td>
        <td>{{medicos}}</td>
        <td class="text-right">S/ {{comision_sin_igv}}</td>
    </tr>
{{else}}
    <tr>
        <td colspan="12" class="text-center"><i>Sin datos que mostrar</i></td>
    </tr>
{{/.}}