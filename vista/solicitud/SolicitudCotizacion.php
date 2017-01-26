<?php
/**
 *@package pXP
 *@file RegistroSolicitud.php
 *@author  MAM
 *@date 27-12-2016 14:45
 *@Interface para el inicio de solicitudes de materiales
 */
header("content-type: text/javascript; charset=UTF-8");
?>
<script>
    Phx.vista.SolicitudCotizacion = {
        require: '../../../sis_gestion_materiales/vista/solicitud/Solicitud.php',
        requireclase: 'Phx.vista.Solicitud',
        title: 'Solicitud',
        nombreVista: 'Proceso Compra',
        constructor: function (config) {
            this.Atributos.splice(24,25);
            this.Atributos.push({
                    config:{
                        name: 'nro_po',
                        fieldLabel: 'Nro. PO',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100
                    },
                    type:'TextField',
                    filters:{pfiltro:'rec.nro_po',type:'string'},
                    id_grupo:2,
                    grid:true,
                    form:false
                },
                {
                    config: {
                        name: 'id_proveedor',
                        fieldLabel: 'Proveedor',
                        anchor: '80%',
                        tinit: false,
                        allowBlank: true,
                        origen: 'PROVEEDOR',
                        gdisplayField: 'desc_proveedor',
                        anchor: '100%',
                        gwidth: 280,
                        listWidth: '280',
                        resizable: true
                    },
                    type: 'ComboRec',
                    filters:{pfiltro:'pro.desc_proveedor',type:'string'},
                    id_grupo:2,
                    grid: true,
                    form: false
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
            );
            Phx.vista.SolicitudCotizacion.superclass.constructor.call(this, config);
            //this.maestro = config.maestro;
            this.store.baseParams={tipo_interfaz:this.nombreVista};
            this.store.baseParams.pes_estado = 'compra';
            this.load({params:{start:0, limit:this.tam_pag}});
            this.finCons = true;
        },
        actualizarSegunTab: function(name, indice){
            if(this.finCons){
                this.store.baseParams.pes_estado = name;
                this.load({params:{start:0, limit:this.tam_pag}});
            }
        },
        enableTabDetalle:function(){
            if(this.TabPanelSouth.get(0)){
                this.TabPanelSouth.get(0).enable();
                this.TabPanelSouth.setActiveTab(0);
            }
        },
        disableTabDetalle:function(){
            if(this.TabPanelSouth.get(0)){
                //this.TabPanelSouth.get(0).disable();
                this.TabPanelSouth.setActiveTab(0);
                //this.TabPanelSouth.bdel.getVisible(false);
            }
        },
        preparaMenu:function(n){
            var data = this.getSelectedData();
            var tb =this.tbar;
            Phx.vista.SolicitudCotizacion.superclass.preparaMenu.call(this,n);

            if(data['estado'] ==  'revision'){
                this.getBoton('sig_estado').enable();
                this.getBoton('ant_estado').enable();
                this. enableTabDetalle();


            }else if(data['estado'] !=  'despachado'){
                this.getBoton('sig_estado').enable();
                this.getBoton('ant_estado').enable();
                this.disableTabDetalle();
            }
            else {
                this.getBoton('sig_estado').disable();
                this.getBoton('ant_estado').enable();
                this.disableTabDetalle();
            }
            return tb;
        },
        liberaMenu:function(){
            var tb = Phx.vista.SolicitudCotizacion.superclass.liberaMenu.call(this);
            if(tb){

                this.getBoton('sig_estado').disable();
                this.getBoton('sig_estado').disable();
                this.getBoton('edit').setVisible(true);
                // this.getBoton('del').disable();
            }
            return tb;
        },

        bdel:false,
        bsave:false,
        bnew:false,
        sortInfo:{
            field: 'id_solicitud',
            direction: 'DESC'
        }

    }

</script>