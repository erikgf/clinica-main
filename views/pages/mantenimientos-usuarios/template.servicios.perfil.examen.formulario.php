{{#.}}
    <tr data-id="{{id_servicio}}"> 
        <td> <select data-id="{{id_servicio}}" data-val="{{nombre_servicio}}"  class="select2 txt-servicio form-control"></select></td>
        <td class="txt-valorventa text-center">{{valor_venta}}</td>
        <td class="txt-precioventa text-center">{{precio_venta}}</td>
        <td class="text-center input-group-sm">
            <button class="btn btn-xs btn-secondary btn-agregarfila" title="Agregar FILA"><i class="fa fa-plus"></i></button>
            <button class="btn btn-xs btn-danger btn-quitarfila" title="Quitar FILA"><i class="fa fa-close"></i></button>
        </td>
    </tr>
{{else}}
    <tr class="not-tr">
        <td class="text-center" colspan="3"><i>¡No hay registros que mostrar!</i></td>
        <td class="text-center input-group-sm">
            <button class="btn btn-xs btn-secondary btn-agregarfila" title="Agregar FILA"><i class="fa fa-plus"></i></button>
        </td>
    </tr>
{{/.}}