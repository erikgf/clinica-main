{{#.}}
    <tr data-id="{{id_servicio}}"> 
        <td class="text-center input-group-sm">
            <button type="button" class="btn btn-xs btn-success btn-subirfila" title="Subir FILA"><i class="fa fa-arrow-up"></i></button>
            <button type="button" class="btn btn-xs btn-info btn-bajarfila" title="Bajar FILA"><i class="fa fa-arrow-down"></i></button>
            <button type="button" class="btn btn-xs btn-secondary btn-agregarfila" title="Agregar FILA"><i class="fa fa-plus"></i></button>
            <button type="button" class="btn btn-xs btn-danger btn-quitarfila" title="Quitar FILA"><i class="fa fa-close"></i></button>
        </td>
        <td> <select data-id="{{id_servicio}}" data-val="{{nombre_servicio}}"  class="select2 txt-servicio form-control"></select></td>
        <td class="txt-valorventa text-right">{{valor_venta}}</td>
        <td class="txt-precioventa text-right">{{precio_venta}}</td>
    </tr>
{{else}}
    <tr class="not-tr">
        <td class="text-center input-group-sm">
            <button class="btn btn-xs btn-secondary btn-agregarfila" title="Agregar FILA"><i class="fa fa-plus"></i></button>
        </td>
        <td class="text-center" colspan="4"><i>¡No hay registros que mostrar!</i></td>
    </tr>
{{/.}}