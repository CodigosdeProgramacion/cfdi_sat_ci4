<?php
	//
	// +---------------------------------------------------------------------------+
	// | satxmlsv33.php Procesa el arreglo asociativo de intercambio y genera un   |
	// |               mensaje XML con los requisitos del SAT de la version 3.3    |
	// |               publicada en el ANEXO 20  ANEXO 20 de la Resolución         |
	// |               Miscelánea Fiscal para 2017, publicada el 23 de diciembre   |
	// |               de 2016.                                                    |
	// |                                                                           |
	// |  Modificado para trabajar con CodeIgniter 4                               |
	// |        No incluye addenda                                                 |
	// +---------------------------------------------------------------------------|
	// | Autor: Marco Robles <contacto@codigosdeprogramacion.com>                  |
	// +---------------------------------------------------------------------------+
	// |21/nov/2020                                                                |
	// +---------------------------------------------------------------------------+
	//
	
	class GeneraXML
	{
		// {{{  satxmlsv33
		function satxmlsv33($arr, $edidata=false, $dir="./tmp/",$nodo="") {
			
			global $xml, $cadena_original, $sello, $texto, $ret;
			
			error_reporting(E_ALL & ~(E_WARNING | E_NOTICE));
			$this->satxmlsv33_genera_xml($arr,$edidata,$dir,$nodo);
			$this->satxmlsv33_genera_cadena_original($dir);
			$this->satxmlsv33_sella($arr, $dir);
			$ret = $this->satxmlsv33_termina($arr,$dir);
			return $ret;
		}
		
		// satxmlsv33_genera_xml
		function satxmlsv33_genera_xml($arr, $edidata, $dir,$nodo) {
			global $xml, $ret;
			header("Content-Type: text/html; charset=utf-8");
			$xml = new DOMdocument("1.0","UTF-8");
			$this->satxmlsv33_generales($arr, $edidata, $dir,$nodo);
			$this->satxmlsv33_emisor($arr, $edidata, $dir,$nodo);
			$this->satxmlsv33_receptor($arr, $edidata, $dir,$nodo);
			$this->satxmlsv33_conceptos($arr, $edidata, $dir,$nodo);
			$this->satxmlsv33_impuestos($arr, $edidata, $dir,$nodo);
			$ok = $this->satxmlsv33_valida($dir);
			if (!$ok) {
				$this->display_xml_errors();
				die("Error al validar XSD\n");
			}
		}
		
		//  Datos generales del Comprobante
		function satxmlsv33_generales($arr, $edidata, $dir,$nodo) {
			global $root, $xml;
			$root = $xml->createElement("cfdi:Comprobante");
			$root = $xml->appendChild($root);
			
			$this->satxmlsv33_cargaAtt($root, array("xmlns:cfdi"=>"http://www.sat.gob.mx/cfd/3",
			//"xmlns:xs"=>"http://www.w3.org/2001/XMLSchema",
			"xmlns:xsi"=>"http://www.w3.org/2001/XMLSchema-instance",
			"xsi:schemaLocation"=>"http://www.sat.gob.mx/cfd/3  http://www.sat.gob.mx/sitio_internet/cfd/3/cfdv33.xsd"
			)
			);
			
			$this->satxmlsv33_cargaAtt($root, array("Version"=>$arr['version'],
			"Serie"=>$arr['serie'],
			"Folio"=>$arr['folio'],
			"Fecha"=>$this->satxmlsv33_xml_fech($arr['fecha']),
			"Sello"=>"@",
			"FormaPago"=>$arr['formaPago'],
			"NoCertificado"=>$arr['noCertificado'],
			"Certificado"=>"@",
			"SubTotal"=>$arr['subTotal'],
			"Descuento"=>$arr['descuento'],
			"Moneda"=>$arr['moneda'],
			"Total"=>$arr['total'],
			"TipoDeComprobante"=>$arr['tipoDeComprobante'],
			"MetodoPago"=>$arr['metodoPago'],
			"LugarExpedicion"=>$arr['lugarExpedicion'],
			"NumCtaPago"=>$arr['numCtaPago'],
			"FolioFiscalOrig"=>$arr['folioFiscalOrig'],
			"SerieFolioFiscalOrig"=>$arr['serieFolioFiscalOrig'],
			"FechaFolioFiscalOrig"=>$this->satxmlsv33_xml_fech($arr['fechaFolioFiscalOrig']),
			"MontoFolioFiscalOrig"=>$arr['montoFolioFiscalOrig']
			)
			);
		}
		
		// Datos del Emisor
		function satxmlsv33_emisor($arr, $edidata, $dir,$nodo) {
			global $root, $xml;
			$emisor = $xml->createElement("cfdi:Emisor");
			$emisor = $root->appendChild($emisor);
			$this->satxmlsv33_cargaAtt($emisor, array("Rfc"=>$arr['emisor']['rfc'],
			"Nombre"=>$arr['emisor']['nombre'],
			"RegimenFiscal"=>$arr['emisor']['regimen']
			)
			);
		}
		
		// {{{ Datos del Receptor
		function satxmlsv33_receptor($arr, $edidata, $dir,$nodo) {
			global $root, $xml;
			$receptor = $xml->createElement("cfdi:Receptor");
			$receptor = $root->appendChild($receptor);
			$this->satxmlsv33_cargaAtt($receptor, array("Rfc"=>$arr['receptor']['rfc'],
			"Nombre"=>$arr['receptor']['nombre'],
			"UsoCFDI"=>$arr['receptor']['usocfdi']
			)
			);
		}
		
		// Detalle de los conceptos/productos de la factura
		function satxmlsv33_conceptos($arr, $edidata, $dir, $nodo) {
			global $root, $xml;
			$conceptos = $xml->createElement("cfdi:Conceptos");
			$conceptos = $root->appendChild($conceptos);
			
			for ($i=0; $i<sizeof($arr['conceptos']); $i++) {
				
				$concepto = $xml->createElement("cfdi:Concepto");
				$concepto = $conceptos->appendChild($concepto);
				
				$this->satxmlsv33_cargaAtt($concepto, array("ClaveProdServ"=>$arr['conceptos'][$i]['clave'],
				"NoIdentificacion"=>$arr['conceptos'][$i]['sku'],
				"Cantidad"=>$arr['conceptos'][$i]['cantidad'],
				"ClaveUnidad"=>$arr['conceptos'][$i]['claveUnidad'],
				"Unidad"=>$arr['conceptos'][$i]['unidad'],
				"Descripcion"=>$arr['conceptos'][$i]['descripcion'],
				"ValorUnitario"=>$arr['conceptos'][$i]['precio'],
				"Importe"=>$arr['conceptos'][$i]['importe'],
				"Descuento"=>$arr['conceptos'][$i]['descuento']
				)
				);
				
				///-----Impuestos del concepto------------
				if (isset($arr['conceptos'][$i]['iBase'])) {
					
					$impuestos = $xml->createElement("cfdi:Impuestos");
					$impuestos = $concepto->appendChild($impuestos);
					$traslados = $xml->createElement("cfdi:Traslados");
					$traslados = $impuestos->appendChild($traslados);
					$traslado = $xml->createElement("cfdi:Traslado");
					$traslado = $traslados->appendChild($traslado);
					$this->satxmlsv33_cargaAtt($traslado, array("Base"=>$arr['conceptos'][$i]['iBase'],
					"Impuesto"=>$arr['conceptos'][$i]['iImpuesto'],
					"TipoFactor"=>$arr['conceptos'][$i]['iTipoFactor'],
					"TasaOCuota"=>$arr['conceptos'][$i]['iTasaOCuota'],
					"Importe"=>$arr['conceptos'][$i]['iImporte']
					)
					);
				}
			}
		}
		
		// Impuesto (IVA)
		function satxmlsv33_impuestos($arr, $edidata, $dir,$nodo) {
			global $root, $xml;
			$impuestos = $xml->createElement("cfdi:Impuestos");
			$impuestos = $root->appendChild($impuestos);
			if (isset($arr['traslados']['importe'])) {
				$traslados = $xml->createElement("cfdi:Traslados");
				$traslados = $impuestos->appendChild($traslados);
				$traslado = $xml->createElement("cfdi:Traslado");
				$traslado = $traslados->appendChild($traslado);
				$this->satxmlsv33_cargaAtt($traslado, array("Impuesto"=>$arr['traslados']['impuesto'],
				"TipoFactor"=>'Tasa',
				"TasaOCuota"=>$arr['traslados']['tasa'],
				"Importe"=>$arr['traslados']['importe']
				)
				);
			}
			$impuestos->SetAttribute("TotalImpuestosTrasladados",$arr['traslados']['importe']);
		}
		
		//  genera_cadena_original
		function satxmlsv33_genera_cadena_original($dir) {
			global $xml, $cadena_original;
			$paso = new DOMDocument("1.0","UTF-8");
			$paso->loadXML($xml->saveXML());
			$xsl = new DOMDocument("1.0","UTF-8");
			$ruta = $dir.'xslt/';
			$file=$ruta."cadenaoriginal_3_3.xslt";      // Ruta al archivo
			$xsl->load($file);
			$proc = new XSLTProcessor;
			$proc->importStyleSheet($xsl); 
			$cadena_original = $proc->transformToXML($paso);
		}
		
		//  Calculo de sello
		function satxmlsv33_sella($arr, $dir) {
			global $root, $cadena_original, $sello;
			$certificado = $arr['noCertificado'];
			$ruta = $dir.'pem/';
			$file=$ruta.$certificado.".key.pem";      // Ruta al archivo
			// Obtiene la llave privada del Certificado de Sello Digital (CSD),
			//    Ojo , Nunca es la FIEL/FEA
			$pkeyid = openssl_get_privatekey(file_get_contents($file));
			openssl_sign($cadena_original, $crypttext, $pkeyid, OPENSSL_ALGO_SHA256);
			openssl_free_key($pkeyid);
			
			$sello = base64_encode($crypttext);      // lo codifica en formato base64
			$root->setAttribute("Sello",$sello);
			
			$file=$ruta.$certificado.".cer.pem";      // Ruta al archivo de Llave publica
			$datos = file($file);
			$certificado = ""; $carga=false;
			for ($i=0; $i<sizeof($datos); $i++) {
				if (strstr($datos[$i],"END CERTIFICATE")) $carga=false;
				if ($carga) $certificado .= trim($datos[$i]);
				if (strstr($datos[$i],"BEGIN CERTIFICATE")) $carga=true;
			}
			// El certificado como base64 lo agrega al XML para simplificar la validacion
			$root->setAttribute("Certificado",$certificado);
		}
		
		//  Termina, graba en edidata o genera archivo en el disco
		function satxmlsv33_termina($arr,$dir) {
			global $xml;
			$xml->formatOutput = true;
			$todo = $xml->saveXML();
			$nufa = $arr['serie'].$arr['folio'];    // Junta el numero de factura   serie + folio
			$file=$dir.'tmp/'.$nufa.".xml";
			$xml->save($file);
			return($todo);
		}
		
		//  Funcion que carga los atributos a la etiqueta XML
		function satxmlsv33_cargaAtt(&$nodo, $attr) {
			global $xml, $sello;
			$quitar = array('sello'=>1,'noCertificado'=>1,'certificado'=>1);
			foreach ($attr as $key => $val) {
				for ($i=0;$i<strlen($val); $i++) {
					$a = substr($val,$i,1);
					if ($a > chr(127) && $a !== chr(219) && $a !== chr(211) && $a !== chr(209)) {
						$val = substr_replace($val, ".", $i, 1);
					}
				}
				$val = preg_replace('/\s\s+/', ' ', $val);   // Regla 5a y 5c
				$val = trim($val);                           // Regla 5b
				if (strlen($val)>0) {   // Regla 6
					$val = str_replace(array('"','>','<'),"'",$val);  // &...;
					$val = utf8_encode(str_replace("|","/",$val)); // Regla 1
					$nodo->setAttribute($key,$val);
				}
			}
		}
		
		// Formateo de la fecha en el formato XML requerido (ISO)
		function satxmlsv33_xml_fech($fech) {
			$ano = substr($fech,0,4);
			$mes = substr($fech,4,2);
			$dia = substr($fech,6,2);
			$hor = substr($fech,8,2);
			$min = substr($fech,10,2);
			$seg = substr($fech,12,2);
			$aux = $ano."-".$mes."-".$dia."T".$hor.":".$min.":".$seg;
			if ($aux == "--T::")
			$aux = "";
			return ($aux);
		}
		
		//  valida que el xml coincida con esquema XSD
		function satxmlsv33_valida($dir) {
			global $xml, $texto;
			$xml->formatOutput=true;
			$paso = new DOMDocument("1.0","UTF-8");
			$texto = $xml->saveXML();
			$paso->loadXML($texto);
			libxml_use_internal_errors(true);
			libxml_clear_errors();
			$ruta = $dir.'xsd/';
			
			$file=$ruta."cfdv33.xsd";  
			$ok = $paso->schemaValidate($file);
			// }
			return $ok;
		}
		
		// display_xml_errors
		function display_xml_errors() {
			global $texto;
			$lineas = explode("\n", $texto);
			$errors = libxml_get_errors();
			
			foreach ($errors as $error) {
				echo $this->display_xml_error($error, $lineas);
			}
			
			libxml_clear_errors();
		}
		
		// display_xml_error
		function display_xml_error($error, $lineas) {
			$return  = $lineas[$error->line - 1]. "\n";
			$return .= str_repeat('-', $error->column) . "^\n";
			
			switch ($error->level) {
				case LIBXML_ERR_WARNING:
				$return .= "Warning $error->code: ";
				break;
				
				case LIBXML_ERR_ERROR:
				$return .= "Error $error->code: ";
				break;
				case LIBXML_ERR_FATAL:
				$return .= "Fatal Error $error->code: ";
				break;
			}
			
			$return .= trim($error->message) .
			"\n  Linea: $error->line" .
			"\n  Columna: $error->column";
			echo "$return\n\n--------------------------------------------\n\n";
		}
	}
?>
