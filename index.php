<?php
	/*
		Generación y timbrado de XML, Facturas CFDI (SAT/SHCP/Mexico)
		Autor: Marko Robles
		Códigos de Programacion - 2020
		
		Requiere las extensiones habilitadas en php.ini
		-openssl
		-xsl
		-soap
	*/
	
	date_default_timezone_set('America/Mexico_City');
	
	$datosFactura = array();
	
	$dirCfdi = APPPATH . 'Libraries/cfdi_sat/cfdi/';
	$dir = APPPATH . 'Libraries/cfdi_sat/';
	
	$nombre = "A1";
	
	//Datos generales de factura
	$datosFactura["version"] = "3.3";
	$datosFactura["serie"] = "A";
	$datosFactura["folio"] = "1";
	$datosFactura["fecha"] = date('YmdHis');
	$datosFactura["formaPago"] = "01";
	$datosFactura["noCertificado"] = "20001000000300022762";
	$datosFactura["subTotal"] = "1000.00";
	$datosFactura["descuento"] = "0.00";
	$datosFactura["moneda"] = "MXN";
	$datosFactura["total"] = "1160.00";
	$datosFactura["tipoDeComprobante"] = "I";
	$datosFactura["metodoPago"] = "PUE";
	$datosFactura["lugarExpedicion"] = "01000";
	
	//Datos del emisor
	$datosFactura['emisor']['rfc'] = 'TCM970625MB1';
	$datosFactura['emisor']['nombre'] = 'Tienda CDP';
	$datosFactura['emisor']['regimen'] = '601';
	
	//Datos del receptor
	$datosFactura['receptor']['rfc'] = 'XAXX010101000';
	$datosFactura['receptor']['nombre'] = 'Publico en general';
	$datosFactura['receptor']['usocfdi'] = 'P01';
	
	
	$datosFactura["conceptos"][] = array("clave" => "01010101", "sku" => "75654123", "descripcion" =>"Venta de productos", "cantidad" => "1", "claveUnidad" => "H87", "unidad" => "Pieza", "precio" => "1000.00", "importe" => "1000.00", "descuento" => "0.00", "iBase" => "1000.00", "iImpuesto" => "002", "iTipoFactor" => "Tasa", "iTasaOCuota" => "0.160000", "iImporte" => "160.00");
	
	$datosFactura['traslados']['impuesto'] = "002";
	$datosFactura['traslados']['tasa'] = "0.160000";
	$datosFactura['traslados']['importe'] = "160.00";
	
	$xml = new \GeneraXML();
	$xmlBase = $xml->satxmlsv33($datosFactura, '', $dir, '');	
	
	$timbra = new \Pac();
	$cfdi = $timbra->enviar("UsuarioPruebasWS","b9ec2afa3361a59af4b4d102d3f704eabdf097d4","TCM970625MB1", $xmlBase);
	
	if($xml)
	{
		file_put_contents($dirCfdi.$nombre'.xml', base64_decode($cfdi->xml));
		unlink($dirXML.$nombre'.xml');
	}
	
?>