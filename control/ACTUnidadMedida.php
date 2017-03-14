<?php
/**
*@package pXP
*@file gen-ACTUnidadMedida.php
*@author  (admin)
*@date 14-03-2017 16:18:47
*@description Clase que recibe los parametros enviados por la vista para mandar a la capa de Modelo
*/

class ACTUnidadMedida extends ACTbase{    
			
	function listarUnidadMedida(){
		$this->objParam->defecto('ordenacion','id_unidad_medida');

		$this->objParam->defecto('dir_ordenacion','asc');
		if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
			$this->objReporte = new Reporte($this->objParam,$this);
			$this->res = $this->objReporte->generarReporteListado('MODUnidadMedida','listarUnidadMedida');
		} else{
			$this->objFunc=$this->create('MODUnidadMedida');
			
			$this->res=$this->objFunc->listarUnidadMedida($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
				
	function insertarUnidadMedida(){
		$this->objFunc=$this->create('MODUnidadMedida');	
		if($this->objParam->insertar('id_unidad_medida')){
			$this->res=$this->objFunc->insertarUnidadMedida($this->objParam);			
		} else{			
			$this->res=$this->objFunc->modificarUnidadMedida($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
						
	function eliminarUnidadMedida(){
			$this->objFunc=$this->create('MODUnidadMedida');	
		$this->res=$this->objFunc->eliminarUnidadMedida($this->objParam);
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
			
}

?>