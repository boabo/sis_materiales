<?php
/**
 *@package pXP
 *@file    FormObligacion.php
 *@author  Rensi Arteaga Copari
 *@date    30-01-2014
 *@description permites subir archivos a la tabla de documento_sol
 */
header("content-type: text/javascript; charset=UTF-8");
?>
<script>
    Phx.vista.FromFormula=Ext.extend(Phx.frmInterfaz,{

        ActSave:'../../sis_gestion_materiales/control/Solicitud/insertarSolicitudCompleta',

        tam_pag: 10,
        layout: 'fit',
        autoScroll: false,
        breset: false,
        constructor:function(config)
        {

            this.addEvents('beforesave');
            this.addEvents('successsave');
            this.buildComponentesDetalle();
            this.buildDetailGrid();
            this.buildGrupos();
            Phx.vista.FromFormula.superclass.constructor.call(this,config);
            this.init();
            this.onNew();
            this.iniciarEventos();
            //console(setValue);

        },
        buildComponentesDetalle: function () {

            this.detCmp =
                {
                    'nro_parte': new Ext.form.TextField({
                        name: 'nro_parte',
                        msgTarget: 'title',
                        fieldLabel: 'Nro. Partes',
                        allowBlank: false,
                        anchor: '90%',
                        maxLength:50

                    }),
                    'nro_parte_alterno': new Ext.form.TextField({
                        name: 'nro_parte_alterno',
                        msgTarget: 'title',
                        fieldLabel: 'Nro. Parte alterno',
                        allowBlank: false,
                        anchor: '90%',
                        maxLength:50
                    }),
                    'referencia': new Ext.form.TextField({
                        name: 'referencia',
                        msgTarget: 'title',
                        fieldLabel: 'Referencia',
                        allowBlank: false,
                        anchor: '90%',
                        maxLength:50
                    }),

                    'descripcion': new Ext.form.TextArea({
                        name: 'descripcion',
                        msgTarget: 'title',
                        fieldLabel: 'Descripcion',
                        allowBlank: false,
                        anchor: '80%',
                        maxLength:5000
                    }),
                    'cantidad_sol': new Ext.form.TextField({
                        name: 'cantidad_sol',
                        msgTarget: 'title',
                        currencyChar:' ',
                        fieldLabel: 'Cantidad',
                        minValue: 0.0001,
                        allowBlank: false,
                        allowDecimals: true,
                        allowNegative:false,
                        decimalPrecision:2
                    }),
                    'unidad_medida': new Ext.form.TextField({
                        name: 'unidad_medida',
                        msgTarget: 'title',
                        fieldLabel: 'Unidad Medida',
                        allowBlank: false,
                        anchor: '80%',
                        maxLength:50
                    })
                    /*'precio': new Ext.form.NumberField({
                     name: 'precio',
                     msgTarget: 'title',
                     fieldLabel: 'Precio',
                     allowBlank: false,
                     anchor: '80%',
                     maxLength:5000
                     //allowBlank: false,
                     // allowDecimals: true,
                     // maxLength:10,
                     // readOnly :true
                     }),
                     'moneda': new Ext.form.TextField({
                     name: 'moneda',
                     msgTarget: 'title',
                     currencyChar:' ',
                     fieldLabel: 'Moneda',
                     allowBlank: false,
                     anchor: '80%',
                     maxLength:5000
                     })*/

                }
        },

        iniciarEventos : function () {
            //this.CmpFuncionario = this.getComponente('id_funcionario');
           // this.Cmp.fecha_solicitud =this.getComponente('fecha_solicitud');
            this.Cmp.fecha_solicitud.on('change',function(f){
                Phx.CP.loadingShow();
                this.obtenerGestion(this.Cmp.fecha_solicitud);
                this.Cmp.id_funcionario_sol.reset();
                this.Cmp.id_funcionario_sol.enable();
                this.Cmp.id_funcionario_sol.store.baseParams.fecha = this.Cmp.fecha_solicitud.getValue().dateFormat(this.Cmp.fecha_solicitud.format);
                this.Cmp.id_funcionario_sol.store.load({params:{start:0,limit:this.tam_pag},
                    callback : function (r) {
                        Phx.CP.loadingHide();
                        if (r.length == 1 ) {
                            this.Cmp.id_funcionario_sol.setValue(r[0].data.id_funcionario);
                            this.Cmp.id_funcionario_sol.fireEvent('select',  this.Cmp.id_funcionario_sol, r[0]);
                        }

                    }, scope : this
                });


            },this);

            //console.log(this.Cmp.id_funcionario_sol.setValue(data.id_funcionario_sol));
            this.mostrarComponente(this.Cmp.id_funcionario_sol);
            this.Cmp.id_funcionario_sol.reset();
        },
        evaluaRequistos: function(){
            //valida que todos los requistosprevios esten completos y habilita la adicion en el grid
            var i = 0;
            sw = true
            while( i < this.Cmp.length) {

                if(!this.Cmp[i].isValid()){
                    sw = false;
                    //i = this.Componentes.length;
                }
                i++;
            }
            return sw
        },
        bloqueaRequisitos: function(sw){

            this.Cmp.id_funcionario_sol.setDisabled(sw);
        },
        evaluaGrilla: function(){
            //al eliminar si no quedan registros en la grilla desbloquea los requisitos en el maestro
            var  count = this.mestore.getCount();
            if(count == 0){
                this.bloqueaRequisitos(false);
            }
        },
        obtenerGestion:function(x){

            var fecha = x.getValue().dateFormat(x.format);
            Phx.CP.loadingShow();
            Ext.Ajax.request({
                // form:this.form.getForm().getEl(),
                url:'../../sis_parametros/control/Gestion/obtenerGestionByFecha',
                params:{fecha:fecha},
                success:this.successGestion,
                failure: this.conexionFailure,
                timeout:this.timeout,
                scope:this
            });
        },
        successGestion:function(resp){
            Phx.CP.loadingHide();
            var reg = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));
            if(!reg.ROOT.error){

                this.Cmp.id_gestion.setValue(reg.ROOT.datos.id_gestion);


            }else{

                alert('ocurrio al obtener la gestion')
            }
        },
        onNew: function(){

            this.mostrarComponente(this.Cmp.id_funcionario_sol);
            this.Cmp.id_funcionario_sol.reset();
            this.Cmp.fecha_solicitud.enable();
            this.Cmp.id_funcionario_sol.disable();
            this.Cmp.fecha_solicitud.setValue(new Date());
            this.Cmp.fecha_solicitud.fireEvent('change');
            //this.Cmp.id_funcionario_sol.setValue(this.Cmp.desc_funcionario1);
        },

        onInitAdd: function(){


        },
        onCancelAdd: function(re,save){
            if(this.sw_init_add){
                this.mestore.remove(this.mestore.getAt(0));
            }

            this.sw_init_add = false;
           this.evaluaGrilla();

        },
        onUpdateRegister: function(){
            this.sw_init_add = false;
        },

        onAfterEdit:function(){

        },


        buildDetailGrid:function () {
            var Items = Ext.data.Record.create([{
                name: 'cantidad_sol',
                type: 'int'
            }
            ]);
            this.mestore = new Ext.data.JsonStore({
                url: '../../sis_gestion_materiales/control/DetalleSol/listarDetalleSol',
                id: 'id_detalle',
                root: 'datos',
                totalProperty: 'total',
                fields: ['id_detalle','id_solicitud','precio', 'cantidad_sol',
                    'unidad_medida','descripcion','nro_parte_alterno','moneda','referencia',
                    'nro_parte'
                ],remoteSort: true,
                baseParams: {dir:'ASC',sort:'id_detalle',limit:'100',start:'0'}
            });

            this.editorDetail = new Ext.ux.grid.RowEditor({
                saveText: 'Aceptar',
                name: 'btn_editor'

            });
            // al iniciar la edicion
            this.editorDetail.on('beforeedit', this.onInitAdd , this);

            //al cancelar la edicion
            this.editorDetail.on('canceledit', this.onCancelAdd , this);

            //al cancelar la edicion
            this.editorDetail.on('validateedit', this.onUpdateRegister, this);

            this.editorDetail.on('afteredit', this.onAfterEdit, this);

            this.megrid = new Ext.grid.GridPanel({
                layout: 'fit',
                store:  this.mestore,
                region: 'center',
                split: true,
                border: false,
                plain: true,
                plugins: [ this.editorDetail ],
                stripeRows: true,
                tbar: [{
                    text: '<i class="fa fa-plus-circle fa-lg"></i> Agregar ',
                    scope: this,
                    width: '100',
                    handler: function(){
                      if(this.evaluaRequistos() === true) {
                            var e = new Items({
                                cantidad_sol: 1
                            });
                            this.editorDetail.stopEditing();
                            this.mestore.insert(0, e);
                            this.megrid.getView().refresh();
                            this.megrid.getSelectionModel().selectRow(0);
                            this.editorDetail.startEditing(0);
                            this.sw_init_add = true;
                            //this.bloqueaRequisitos(true);
                        }
                    }
                },{
                    ref: '../removeBtn',
                    text: '<i class="fa fa-trash fa-lg"></i> Eliminar',
                    scope:this,
                    handler: function(){
                        this.editorDetail.stopEditing();
                        var s = this.megrid.getSelectionModel().getSelections();
                        for(var i = 0, r; r = s[i]; i++){
                            this.mestore.remove(r);
                        }
                        this.evaluaGrilla();
                    }
                }],

                columns: [
                    new Ext.grid.RowNumberer(),
                    {
                        header: 'Nro. Parte',
                        dataIndex: 'nro_parte',
                        align: 'center',
                        width: 165,
                        editor: this.detCmp.nro_parte
                    },
                    {
                        header: 'Nro. Parte Alterno',
                        dataIndex: 'nro_parte_alterno',
                        align: 'center',
                        width: 165,
                        editor: this.detCmp.nro_parte_alterno
                    },
                    {
                        header: 'Referencia',
                        dataIndex: 'referencia',
                        align: 'center',
                        width: 165,
                        editor: this.detCmp.referencia
                    },
                    {
                        header: 'Descripcion',
                        dataIndex: 'descripcion',
                        align: 'center',
                        width: 180,
                        editor: this.detCmp.descripcion
                    },
                    {
                        header: 'Cantidad',
                        dataIndex: 'cantidad_sol',
                        align: 'center',
                        width: 50,
                        editor: this.detCmp.cantidad_sol
                    },
                    {
                        header: 'U/M',
                        dataIndex: 'unidad_medida',
                        align: 'center',
                        width: 50,
                        editor: this.detCmp.unidad_medida
                    }
                    /*{
                     header: 'Presio',
                     dataIndex: 'precio',
                     align: 'center',
                     width: 50,
                     editor: this.detCmp.precio
                     },
                     {
                     header: 'Moneda',
                     dataIndex: 'moneda',
                     align: 'center',
                     width: 50,
                     editor: this.detCmp.moneda
                     }*/

                ]
            });

        },
        buildGrupos: function () {

            this.Grupos = [{
                layout: 'border',
                border: false,
                frame:true,
                items:[
                    {
                        xtype: 'fieldset',
                        border: false,
                        split: true,
                        layout: 'column',
                        region: 'north',
                        autoScroll: true,
                        autoHeight: true,
                        collapseFirst : false,
                        collapsible: true,
                        width: '100%',
                        //autoHeight: true,
                        padding: '0 0 0 10',
                        items:[
                            {
                                bodyStyle: 'padding-right:5px;',

                                autoHeight: true,
                                border: false,
                                items:[
                                    {
                                        xtype: 'fieldset',
                                        frame: true,
                                        border: false,
                                        layout: 'form',
                                        title: ' Datos Generales ',
                                        width: '33%',
                                        padding: '0 0 0 10',
                                        bodyStyle: 'padding-left:5px;',
                                        id_grupo: 0,
                                        items: [],
                                    }]
                            },
                            {
                                bodyStyle: 'padding-right:5px;',

                                border: false,
                                autoHeight: true,
                                items: [{
                                    xtype: 'fieldset',
                                    frame: true,
                                    layout: 'form',
                                    title: ' Justificacion de Necesidad ',
                                    width: '33%',
                                    border: false,
                                    padding: '0 0 0 10',
                                    bodyStyle: 'padding-left:5px;',
                                    id_grupo: 1,
                                    items: [],
                                }]
                            },

                        ]
                    },
                    this.megrid
                ]
            }];
        },
        successSave:function(resp)
        {
            Phx.CP.loadingHide();
            Phx.CP.getPagina(this.idContenedorPadre).reload();
            this.panel.close();
        },
        Atributos:[
            {
                //configuracion del componente
                config:{
                    labelSeparator:'',
                    inputType:'hidden',
                    name: 'id_solicitud'
                },
                type:'Field',
                form:true
            },
            {
                config:{
                    name: 'nro_solicitud',
                    fieldLabel: 'Nro. solicitud',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 80,
                    maxLength:50
                },
                type:'TextField',
                filters:{pfiltro:'sol.nro_tramite',type:'string'},
                id_grupo:1,
                grid:false,
                form:false
            },
            {
                config:{
                    name: 'nro_tramite',
                    fieldLabel: 'Nro. Tramite',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 80,
                    maxLength:50
                },
                type:'TextField',
                filters:{pfiltro:'sol.nro_tramite',type:'string'},
                id_grupo:1,
                grid:true,
                form:false
            },
            {
                config:{
                    name: 'estado',
                    fieldLabel: 'Estado',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 100,
                    maxLength:100
                },
                type:'TextField',
                filters:{pfiltro:'sol.estado',type:'string'},
                id_grupo:1,
                grid:true,
                form:false
            },

            {
                config:{
                    name:'origen_pedido',
                    fieldLabel:'Origen Pedido',
                    allowBlank:false,
                    emptyText:'Elija una opción...',
                    typeAhead: true,
                    triggerAction: 'all',
                    lazyRender:true,
                    mode: 'local',
                    anchor: '105%',
                    store:['Gerencia de Operaciones','Gerencia de Mantenimiento','Almacenes Consumibles o Rotables']

                },
                type:'ComboBox',
                id_grupo:0,
                grid:true,
                form:true

            },
            {
                config: {
                    name: 'id_funcionario_sol',
                    fieldLabel: 'Funcionario Solicitante',
                    allowBlank: true,
                    emptyText: 'Elija una opción...',
                    store: new Ext.data.JsonStore({
                        url: '../../sis_organigrama/control/Funcionario/listarFuncionarioCargo',
                        id: 'id_funcionario_sol',
                        root: 'datos',
                        sortInfo: {
                            field: 'desc_funcionario1',
                            direction: 'ASC'
                        },
                        totalProperty: 'total',
                        fields: ['id_funcionario','desc_funcionario1','email_empresa','nombre_cargo','lugar_nombre','oficina_nombre'],
                        remoteSort: true,
                        baseParams: {par_filtro: 'FUNCAR.desc_funcionario1#FUNCAR.nombre_cargo'}
                    }),
                    valueField: 'id_funcionario',
                    displayField: 'desc_funcionario1',
                    gdisplayField: 'desc_funcionario1',
                    tpl:'<tpl for="."><div class="x-combo-list-item"><p>{desc_funcionario1}</p><p style="color: blue">{nombre_cargo}<br>{email_empresa}</p><p style="color:blue">{oficina_nombre} - {lugar_nombre}</p></div></tpl>',
                    hiddenName: 'id_funcionario_sol',
                    forceSelection: true,
                    typeAhead: false,
                    triggerAction: 'all',
                    lazyRender: true,
                    mode: 'remote',
                    pageSize: 15,
                    queryDelay: 1000,
                    anchor: '105%',
                    gwidth: 230,
                    minChars: 2,
                    renderer : function(value, p, record) {
                        return String.format('{0}', record.data['desc_funcionario1']);
                    }
                },
                type: 'ComboBox',
                id_grupo: 0,
                filters: {pfiltro:' f.desc_funcionario1', type:'string'},
                grid: true,
                form: true,
                bottom_filter:true
            },
            /*{
                config:{
                    name:'id_funcionario_sol',
                    hiddenName: 'id_funcionario',
                    origen:'FUNCIONARIOCAR',
                    fieldLabel:'Funcionario',
                    allowBlank:false,
                    gwidth:200,
                    valueField: 'id_funcionario',
                    gdisplayField: 'desc_funcionario',
                    baseParams: { es_combo_solicitud : 'si' } },
                type: 'ComboRec',//ComboRec
                id_grupo: 0,
                form:true
            },*/
            {
                config:{
                    name: 'fecha_solicitud',
                    fieldLabel: 'Fecha Solicitud',
                    qtip: 'Según esta fecha se escoje el formulario de solicitud',
                    readOnly : true,
                    allowBlank: false,
                    gwidth: 100,
                    format: 'd/m/Y'
                },
                type: 'DateField',
                id_grupo: 0,
                form: true
            },

            {
                config: {
                    name: 'id_matricula',
                    fieldLabel: 'Matricula',
                    allowBlank: true,
                    emptyText: 'Elija una opción...',
                    store: new Ext.data.JsonStore({
                        url: '../../sis_gestion_materiales/control/Solicitud/listarMatricula',
                        id: 'id_matricula',
                        root: 'datos',
                        sortInfo: {
                            field: 'matricula',
                            direction: 'ASC'
                        },
                        totalProperty: 'total',
                        fields: ['id_orden_trabajo','desc_orden', 'matricula'],
                        remoteSort: true,
                        baseParams: {par_filtro: 'ord.desc_orden'}
                    }),
                    valueField: 'id_orden_trabajo',
                    displayField: 'desc_orden',
                    gdisplayField: 'desc_orden',
                    tpl:'<tpl for="."><div class="x-combo-list-item"><p>{desc_orden}</p><p style="color: blue">{matricula}</p></div></tpl>',
                    hiddenName: 'id_matricula',
                    forceSelection: true,
                    typeAhead: false,
                    triggerAction: 'all',
                    lazyRender: true,
                    mode: 'remote',
                    pageSize: 100,
                    queryDelay: 1000,
                    anchor: '105%',
                    gwidth: 150,
                    minChars: 2,
                    renderer : function(value, p, record) {
                        return String.format('{0}', record.data['matricula']);
                    }
                },
                type: 'ComboBox',
                id_grupo: 0,
                filters: {pfiltro: 'ord.matricula',type: 'string'},
                grid: true,
                form: true
            },
            {
                config:{
                    name: 'motivo_solicitud',
                    fieldLabel: 'Motivo Solicitud',
                    allowBlank: false,
                    anchor: '105%',
                    gwidth: 100,
                    maxLength:100
                },
                type:'TextArea',
                filters:{pfiltro:'sol.motivo_solicitud',type:'string'},
                id_grupo:0,
                grid:true,
                form:true
            },
            {
                config:{
                    name: 'observaciones_sol',
                    fieldLabel: 'Observaciones',
                    allowBlank: false,
                    anchor: '105%',
                    gwidth: 100,
                    maxLength:100
                },
                type:'TextArea',
                filters:{pfiltro:'sol.observaciones_sol',type:'string'},
                id_grupo:0,
                grid:true,
                form:true
            },
            {
                config:{
                    name:'justificacion',
                    fieldLabel:'Justificación ',
                    allowBlank:false,
                    emptyText:'Elija una opción...',
                    typeAhead: true,
                    triggerAction: 'all',
                    lazyRender: true,
                    lazyRender:true,
                    mode: 'local',
                    anchor: '100%',
                    store:['Directriz de Aeronavegabilidad','Boletín de Servicio','Task Card','"0" Existemcia en Almacén','Otros'],
                    enableMultiSelect: true
                },
                type:'AwesomeCombo',
                id_grupo:1,
                grid:true,
                form:true

            },
            {
                config:{
                    name:'tipo_solicitud',
                    fieldLabel:'Tipo Solicitud',
                    allowBlank:false,
                    emptyText:'Elija una opción...',

                    typeAhead: true,
                    triggerAction: 'all',
                    lazyRender:true,
                    mode: 'local',
                    anchor: '100%',
                    store:['AOG','Critico','Normal']

                },
                type:'ComboBox',
                id_grupo:1,
                grid:true,
                form:true

            },
            {
                config:{
                    name:'tipo_falla',
                    fieldLabel:'Tipo de Falla',
                    allowBlank:true,
                    emptyText:'Elija una opción...',
                    typeAhead: true,
                    triggerAction: 'all',
                    lazyRender: true,
                    lazyRender:true,
                    mode: 'local',
                    anchor: '100%',
                    store:['Falla Confirmada','T/S en Progreso '],
                    enableMultiSelect: true
                },
                type:'AwesomeCombo',
                id_grupo:1,
                grid:true,
                form:true

            },
            {
                config:{
                    name:'tipo_reporte',
                    fieldLabel:'Tipo de Reporte',
                    allowBlank:true,
                    emptyText:'Elija una opción...',

                    typeAhead: true,
                    triggerAction: 'all',
                    lazyRender:true,
                    mode: 'local',
                    anchor: '100%',
                    store:['PIREPS','MAREPS']

                },
                type:'ComboBox',
                id_grupo:1,
                grid:true,
                form:true

            },
            {
                config:{
                    name:'mel',
                    fieldLabel:'MEL',
                    allowBlank:true,
                    emptyText:'Elija una opción...',

                    typeAhead: true,
                    triggerAction: 'all',
                    lazyRender:true,
                    mode: 'local',
                    anchor: '100%',
                    store:['A','B','C']

                },
                type:'ComboBox',
                id_grupo:1,
                grid:true,
                form:true

            },
            {
                config:{
                    name: 'fecha_requerida',
                    fieldLabel: 'Fecha Requerida / Due Date',
                    allowBlank: true,
                    anchor: '95%',
                    gwidth: 100,
                    format: 'd/m/Y',
                    renderer:function (value,p,record){return value?value.dateFormat('d/m/Y'):''}
                },
                type:'DateField',
                filters:{pfiltro:'sol.fecha_requerida',type:'date'},
                id_grupo:1,
                grid:true,
                form:true
            },
            {
                config:{
                    name: 'nro_no_rutina',
                    fieldLabel: 'Nro. Doc. Origen de Solicitud',
                    allowBlank: true,
                    anchor: '100%',
                    gwidth: 200,
                    maxLength:100
                },
                type:'TextField',
                filters:{pfiltro:'sol.motivo_solicitud',type:'string'},
                id_grupo:1,
                grid:true,
                form:true
            },

            {
                config:{
                    name: 'estado_reg',
                    fieldLabel: 'Estado Reg.',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 100,
                    maxLength:10
                },
                type:'TextField',
                filters:{pfiltro:'sol.estado_reg',type:'string'},
                id_grupo:1,
                grid:true,
                form:false
            },
            {
                config:{
                    name: 'usr_reg',
                    fieldLabel: 'Creado por',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 100,
                    maxLength:4
                },
                type:'Field',
                filters:{pfiltro:'usu1.cuenta',type:'string'},
                id_grupo:1,
                grid:true,
                form:false
            },
            {
                config:{
                    name: 'usuario_ai',
                    fieldLabel: 'Funcionaro AI',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 100,
                    maxLength:300
                },
                type:'TextField',
                filters:{pfiltro:'sol.usuario_ai',type:'string'},
                id_grupo:1,
                grid:true,
                form:false
            },
            {
                config:{
                    name: 'fecha_reg',
                    fieldLabel: 'Fecha creación',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 100,
                    format: 'd/m/Y',
                    renderer:function (value,p,record){return value?value.dateFormat('d/m/Y H:i:s'):''}
                },
                type:'DateField',
                filters:{pfiltro:'sol.fecha_reg',type:'date'},
                id_grupo:1,
                grid:true,
                form:false
            },
            {
                config:{
                    name: 'id_usuario_ai',
                    fieldLabel: 'Fecha creación',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 100,
                    maxLength:4
                },
                type:'Field',
                filters:{pfiltro:'sol.id_usuario_ai',type:'numeric'},
                id_grupo:1,
                grid:false,
                form:false
            },
            {
                config:{
                    name: 'usr_mod',
                    fieldLabel: 'Modificado por',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 100,
                    maxLength:4
                },
                type:'Field',
                filters:{pfiltro:'usu2.cuenta',type:'string'},
                id_grupo:1,
                grid:true,
                form:false
            },
            {
                config:{
                    name: 'fecha_mod',
                    fieldLabel: 'Fecha Modif.',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 100,
                    format: 'd/m/Y',
                    renderer:function (value,p,record){return value?value.dateFormat('d/m/Y H:i:s'):''}
                },
                type:'DateField',
                filters:{pfiltro:'sol.fecha_mod',type:'date'},
                id_grupo:1,
                grid:true,
                form:false
            }


        ],
        title: 'Frm Materiales',

        onSubmit: function(o) {
            //  validar formularios
            var arra = [], i, me = this;
            for (i = 0; i < me.megrid.store.getCount(); i++) {
                record = me.megrid.store.getAt(i);
                arra[i] = record.data;
            }
            me.argumentExtraSubmit = {
                'json_new_records': JSON.stringify(arra, function replacer(key, value) {
                    if (typeof value === 'string') {
                        return String(value).replace(/&/g, "%26")
                    }
                    return value;
                })
            };
            if (i > 0 && !this.editorDetail.isVisible()) {
                Phx.vista.FromFormula.superclass.onSubmit.call(this, o);
            }
            else {
                alert('no tiene ningun elemento en la formula')
            }
        }
    })
</script>



