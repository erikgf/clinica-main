{{#.}}
    <tr data-id="{{id_lab_examen}}"> 
        <td class="input-group-sm">
            <select data-pos="0" class="form-control txt-nivel">
                {{#if_ eliminar '==' 0}}
                    <option {{#if_ nivel '==' '0'}}selected{{/if_}} value="0" title="Sangría 0">[ ]</option>
                {{/if_}}
                <option {{#if_ nivel '==' '1'}}selected{{/if_}} value="1" title="Sangría Media"> *</option>
                <option {{#if_ nivel '==' '2'}}selected{{/if_}} value="2" title="Sangría Larga"> ' '</option>
                {{#if_ eliminar '==' 1}}
                    <option {{#if_ nivel '==' '99'}}selected{{/if_}} value="99"  title="Comentario. Texto de apoyo.">-</option>
                {{/if_}}
            </select>
        </td>
        <td class="input-group-sm">
            <input type="text" data-pos="1" class="txt-descripcion form-control" value="{{descripcion}}"/>
        </td>
        <td class="input-group-sm">
            <select data-val="{{abreviatura}}" type="text" data-pos="2" class="select2 txt-abreviatura form-control"></select>
        </td>
        <td class="input-group-sm">
            <select data-val="{{unidad}}" type="text" data-pos="3" class="select2 txt-unidad form-control"></select>
        </td>
        <td class="input-group-sm">
            <textarea oninput="auto_grow(this)" data-pos="4" rows="1" class="txt-valoresreferenciales form-control">{{valor_referencial}}</textarea>
        </td>
        <td class="input-group-sm" >
            <select data-val="{{metodo}}" type="text" data-pos="5" class="select2 txt-metodo form-control"></select>
        </td>
        <td class="text-center input-group-sm">
                <button class="btn btn-xs btn-secondary btn-agregarfila" title="Agregar FILA"><i class="fa fa-plus"></i></button>
            {{#if_ eliminar '==' 1}}
                <button class="btn btn-xs btn-danger btn-quitarfila" title="Quitar FILA"><i class="fa fa-close"></i></button>
            {{/if_}}
        </td>
    </tr>
{{else}}
    <tr class="not-tr">
        <td class="text-center" colspan="15"><i>¡No hay registros que mostrar!</i></td>
    </tr>
{{/.}}