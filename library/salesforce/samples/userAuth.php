<?php
/*--------------------------------------------------------\
|  Assigned your USER ID & PASSWORD with TOKEN 
|  This file is included in each of the SAMPLE Example
\--------------------------------------------------------*/
$conexao =  mysql_connect('184.107.79.67','user_sinscricoes','c48jk301!@#');
mysql_select_db("db_superinscricoes", $conexao);

$USERNAME = "thiago.segatto@esferabr.com.br";
$PASSWORD = "Esfera20133vngMZWZRrcJq1OgWpdLLwsqJQ";
/*--------------------------------------------------------\
|  Create a Lead using the salseforce account
|  Get the LEADID and modify it in following file
|  userAuth.php in samples directory
\--------------------------------------------------------*/
//Used for sample convertLead from file convertLead.php
$convertLEADID = '00Q5000000DO0gJEAT';
//Used for sample fieldsToNull from file fieldsToNull.php
$LEADID = '00Q5000000DO0gs';
//Used for sample loginScopeHeader from file loginScopeHeader.php
$LOGINSCOPEHEADER = '00E200000004wk3EAA';
//Used in sample processSubmitRequest from file processSubmitRequest.php
$OBJECTID1 = '00Q5000000G7YV8';
$OBJECTID2 = '00Q5000000J4pQp';
$NEXTOBJECTID = '00530000000tH4t';
//Used for sending email from file sendEmail.php
$EMAILID = 'email1@test.com';
//Used for updating object from file update.php
$UPDATEOBJECTID1 = '00Q5000000K0KjM';
$UPDATEOBJECTID2 = '00Q5000000K1sL1';
//Used in callOptions
$YOURCLIENTID = 'YourClientId';
$NAMESPACE = 'aNamespace';
//Used for emailHeader from file emailHeader.php
$EMAILIDFORHEADER = 'email1@test.com';
/*--------------------------------------------------------\
|
|  For Enterprise Samples
|
\--------------------------------------------------------*/
//Need to login on account then create the lead
//Assign that id here and check the sample
$eLEADID = "00Q5000000DO0gJEAT";
?>
