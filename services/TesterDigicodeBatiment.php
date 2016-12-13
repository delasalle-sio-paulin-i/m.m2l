<?php
include_once ('../modele/Outils.class.php');
// inclusion des paramètres de l'application
include_once ('../modele/parametres.localhost.php');

if ( empty ($_GET  ["digicode"]) == true)  $digicode = "";  else   $digicode = $_GET  ["digicode"];

if (  $digicode == "" )
{	
if ( empty ($_POST ["digicode"]) == true)  $digicode = "";  else   $digicode = $_POST ["digicode"];
}
if (  $digicode == "" )
{	$msg = "0";		// Erreur : données incomplètes
}
else
{	// connexion du serveur web à la base MySQL ("include_once" peut être remplacé par "require_once")
include_once ('../modele/DAO.class.php');
$dao = new DAO();

$msg = $dao->testerDigicodeBatiment( $digicode);		// la fonction testerDigicodeSalle fournit "0" ou "1"

// ferme la connexion à MySQL :
unset($dao);
}
creerFluxXML ($msg);

// fin du programme (pour ne pas enchainer sur la fonction qui suit)
exit;

function creerFluxXML($msg)
{	// crée une instance de DOMdocument (DOM : Document Object Model)
$doc = new DOMDocument();

// specifie la version et le type d'encodage
$doc->version = '1.0';
$doc->encoding = 'ISO-8859-1';

// crée un commentaire et l'encode en ISO
$elt_commentaire = $doc->createComment('Service web TesterDigicodeBatiment - BTS SIO - Lycée De La Salle - Rennes');
// place ce commentaire à la racine du document XML
$doc->appendChild($elt_commentaire);

// crée l'élément 'data' à la racine du document XML
$elt_data = $doc->createElement('data');
$doc->appendChild($elt_data);

// place l'élément 'reponse' juste après l'élément 'data'
$elt_reponse = $doc->createElement('reponse', $msg);
$elt_data->appendChild($elt_reponse);

// Mise en forme finale
$doc->formatOutput = true;

// renvoie le contenu XML
echo $doc->saveXML();
return;
}
?>
