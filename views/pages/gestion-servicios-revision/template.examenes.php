{{#examenes}}
    <tr data-id="{{id_atencion_medica_servicio}}" data-fueatendido="{{fue_atendido}}" title="Atendido: {{#if_ fue_atendido '!=' '0'}}{{fecha_hora_atendido}}{{else}}No ha sido atendido aún.{{/if_}}" class="bg-{{rotulo_color_atendido}}">
        <td class="text-center">{{fecha_atencion}}</td>
        <td class="text-center">{{recibo}}</td>
        <td >{{nombre_paciente}}</td>
        <td>{{examen}}</td>
        <td class="text-center">{{#if_ monto_deuda '<=' 0.00 }} <span class="font-weight-bold text-green">NO</span> {{else}} <span class="font-weight-bold text-red">SÍ</span> {{/if_}}</td>
        <td>
            <select class="txt-medicorealizado form-control" style="max-width:250px">
                <option value="">Seleccionar</option>
                {{#../medicos_realizantes}}
                    <option {{#if_ ../id_medico_realizado '==' this.id_medico}}selected{{/if_}} value="{{this.id_medico}}">{{#if_ tipo_personal_medico '==' '0'}}Dr. {{else}}TM. {{/if_}}{{this.medico}}</option>
                {{/../medicos_realizantes}}
            </select>
        </td>
        <td>
            <select class="txt-medicoatendido form-control" style="max-width:250px">
                <option value="">Seleccionar</option>
                {{#../medicos_informantes}}
                    <option {{#if_ ../id_medico_atendido '==' this.id_medico}}selected{{/if_}} value="{{this.id_medico}}">{{#if_ tipo_personal_medico '==' '0'}}Dr. {{else}}TM. {{/if_}}{{this.medico}}</option>
                {{/../medicos_informantes}}
            </select>
        </td>
        <td><textarea class="form-control txt-observaciones text-uppercase" rows="1" style="max-width:250px">{{observaciones_atendido}}</textarea></td>
        <td class="text-center">
            <div class="input-group-sm">
                <select class="form-control txt-estado">
                    <option {{#if_ fue_atendido '==' '0'}}selected{{/if_}} value="0">PENDIENTE</option>
                    <option {{#if_ fue_atendido '==' '1'}}selected{{/if_}} class="bg-gradient-green" value="1">REALIZADO</option>
                    <option {{#if_ fue_atendido '==' '2'}}selected{{/if_}} class="bg-gradient-red" value="2">CANCELADO</option>
                </select>
            </div>
            <button class="btn btn-block btn-success btn-sm btn-guardar" title="Guardar">GUARDAR <i class="fa fa-save"></i></button>
        </td>
    </tr>
{{else}}
    <tr>
        <td colspan="15" class="text-center">
            <i>Sin registros que mostrar</i>   
        </td>
    </tr>
{{/examenes}}
