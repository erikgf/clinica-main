{{#detalle}}
    <tr data-id="{{id_lab_examen}}" data-nivel="{{nivel}}"> 
        <td class="input-group-sm th-examen" {{#if_ nivel '==' '1'}}style="padding-left:20px;" {{else}} {{#if_ nivel '==' '2'}} style="padding-left:48px;"{{else}} style="font-weight:bold;" {{/if_}} {{/if_}}>
            {{#if_ ../fue_validado '==' '1'}}
                {{descripcion}}
            {{else}}
                {{#if_ id_lab_examen '==' ''}}
                    <input type="text" data-pos="0" {{#if_ nivel '==' 99}}readonly{{/if_}} class=" txt-descripcion form-control" value=""/>
                {{else}}
                    {{descripcion}}
                {{/if_}}
            {{/if_}}
        </td>
        <td class="input-group-sm" style="position:relative;">
            <input data-pos="1" {{#if_ nivel '==' 99}}readonly{{/if_}} {{#if_ ../fue_validado '==' '1'}}readonly{{/if_}} type="text" class="text-center txt-valorexamen form-control" value="{{resultado}}"/>
            <div class="blk-buscador">
                <ul ></ul>
            </div>
        </td>
        <td class="text-center input-group-sm"  style="position:relative;">
            <input data-pos="2" {{#if_ nivel '==' 99}}readonly{{/if_}} {{#if_ ../fue_validado '==' '1'}}readonly{{/if_}} type="text" class="text-center txt-valorunidad form-control" value="{{unidad}}"/>
            <div class="blk-buscador">
                <ul ></ul>
            </div>
        </td>
        <td class="text-center input-group-sm">
            <input data-pos="3" {{#if_ ../fue_validado '==' '1'}}readonly{{/if_}} type="text" class="txt-valorreferencial form-control" value="{{valor_referencial}}"/>
        </td>
        <td class="text-center input-group-sm"  style="position:relative;">
            <input data-pos="4" {{#if_ ../fue_validado '==' '1'}}readonly{{/if_}} type="text" class="text-center txt-valormetodo form-control" value="{{metodo}}"/>
            <div class="blk-buscador">
                <ul ></ul>
            </div>
        </td>
        <td class="text-center input-group-sm">
            {{#if_ ../fue_validado '==' '0'}}
                <button class="btn btn-xs btn-secondary btn-agregarfila" title="Agregar FILA"><i class="fa fa-plus"></i></button>
                <button class="btn btn-xs btn-danger btn-quitarfila" title="Quitar FILA"><i class="fa fa-close"></i></button>
            {{/if_}}
        </td>
    </tr>
{{else}}
    <tr class="not-tr">
        <td class="text-center" colspan="15"><i>¡No hay examenes que mostrar!</i></td>
    </tr>
{{/detalle}}