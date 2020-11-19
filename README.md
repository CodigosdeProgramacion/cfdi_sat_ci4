# cfdi_sat
Script en PHP para la generación de XML y timbrado de facturas electrónicas correspondientes al SAT/SHCP/Mexico

**cfdi:** Almacena los cfdi (XML timbrados).

**pem:** Almacena los certificados del SAT para sellar los XML.

**tmp:** Almacena temporalmente los xml generados para timbrar.

**xsd:** Esquemas para validacion de forma y sintaxis.

**xslt:** Reglas de transformacion para generar cadena original usando xsltproc.

**index.php:** Programa en PHP que envia los datos para generación y timbrado de XML.

**satxmlvs32.php:** Programa en PHP para generacion de XML de CFDI y sellarlo para la versión 3.2.

**satxmlvs33.php:** Programa en PHP para generacion de XML de CFDI y sellarlo para la versión 3.3.

**timbra.php:** Programa en PHP para consumir webservice para timbrado de Factuación Moderna

Fork de [albertotrece/sat](https://github.com/albertotrece/sat). 
