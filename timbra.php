<?php 
	/*
		Timbrado con Facturación moderna
		Autor: Marko Robles
		Códigos de Programacion - 2020
		
		Este ejemplo está basado en el WSDL de timbrado de Facturación Moderna
		cada WSDL tiene su propia estructura y deberá modificarse la petición 
		acorde al webservice que se esté conectando.
	*/
	
	class Pac {
		
		function enviar($usuario, $password, $rfc, $xml) {			
			
			$url = "https://t1demo.facturacionmoderna.com/timbrado/wsdl";		
			$ini = ini_set("soap.wsdl_cache_enabled","1");
			$xml = base64_encode($xml);
			
			try {
				$client = new SoapClient($url, array('trace' => 1));
				$parametros = array('emisorRFC' => $rfc,'UserID' => $usuario,'UserPass' => $password, 'text2CFDI'=>$xml);
				$response = $client->requestTimbrarCFDI((object) $parametros);
				} catch (SoapFault $fault) { 
				echo "SOAPFault: ".$fault->faultcode."-".$fault->faultstring."\n";
			}
			
			return $response;
		}
	}
?>	