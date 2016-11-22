<?php
// Service web du projet Réservations M2L
// Ecrit le 31/3/2016 par Isi
// Modifié le 3/6/2016 par Isi

// Ce service web permet à un utilisateur de consulter les salles
// et fournit un flux XML contenant un compte-rendu d'exécution

// Le service web doit recevoir 2 paramètres : nom, mdp
// Les paramètres peuvent être passés par la méthode GET (pratique pour les tests, mais à éviter en exploitation) :
//     http://<hébergeur>/Consultersalles.php?nom=zenelsy&mdp=passe

// Les paramètres peuvent être passés par la méthode POST (à privilégier en exploitation pour la confidentialité des données) :
//     http://<hébergeur>/Consultersalles.php

// inclusion de la classe Outils
include_once ('../modele/Outils.class.php');
// inclusion des paramètres de l'application
include_once ('../modele/parametres.localhost.php');
	
// Récupération des données transmises
// la fonction $_GET récupère une donnée passée en paramètre dans l'URL par la méthode GET
if ( empty ($_GET ["nom"]) == true)  $nom = "";  else   $nom = $_GET ["nom"];
if ( empty ($_GET ["mdp"]) == true)  $mdp = "";  else   $mdp = $_GET ["mdp"];
if ( empty ($_GET ["numRes"]) == true)  $numRes = "";  else   $mdp = $_GET ["numRes"];


// si l'URL ne contient pas les données, on regarde si elles ont été envoyées par la méthode POST
// la fonction $_POST récupère une donnée envoyées par la méthode POST
if ( $nom == "" && $mdp == ""  && $numRes == "")
{	if ( empty ($_POST ["nom"]) == true)  $nom = "";  else   $nom = $_POST ["nom"];
	if ( empty ($_POST ["mdp"]) == true)  $mdp = "";  else   $mdp = $_POST ["mdp"];
	if ( empty ($_POST ["numRes"]) == true)  $numRes = "";  else   $mdp = $_POST ["numRes"];
}


// Contrôle de la présence des paramètres
if ( $nom == "" || $mdp == "" || $numRes == "")
{	$msg = "Erreur : données incomplètes.";
}
else
{	// connexion du serveur web à la base MySQL ("include_once" peut être remplacé par "require_once")
	include_once ('../modele/DAO.class.php');
	$dao = new DAO();
	
	if ( $dao->getNiveauUtilisateur($nom, $mdp) == "inconnu" )
		$msg = "Erreur : authentification incorrecte.";
	else 
	{
		// récupération des réservations à venir créées par l'utilisateur
		if($dao->existeReservation($numRes)){
			if($dao->estLeCreateur($nom, $numRes)){
				$uneReservation=$dao->getReservation($numRes);
				
			}else{
				$msg="Erreur : vous n'êtes pas l'auteur de cette réservation";
			}		
		}else{
			$msg="Erreur : numéro de réservation inexistant";
		}
	}
	// ferme la connexion à MySQL
	unset($dao);
}
// création du flux XML en sortie
creerFluxXML ($msg );

// fin du programme (pour ne pas enchainer sur la fonction qui suit)
exit;


// création du flux XML en sortie
function creerFluxXML($msg, $lesSalles)
{	// crée une instance de DOMdocument (DOM : Document Object Model)
	$doc = new DOMDocument();
	
	// specifie la version et le type d'encodage
	$doc->version = '1.0';
	$doc->encoding = 'ISO-8859-1';
	
	// crée un commentaire et l'encode en ISO
	$elt_commentaire = $doc->createComment('Service web ConsulterSalles - BTS SIO - Lycée De La Salle - Rennes');
	// place ce commentaire à la racine du document XML
	$doc->appendChild($elt_commentaire);
	
	// crée l'élément 'data' à la racine du document XML
	$elt_data = $doc->createElement('data');
	$doc->appendChild($elt_data);
	
	// place l'élément 'reponse' dans l'élément 'data'
	$elt_reponse = $doc->createElement('reponse', $msg);
	$elt_data->appendChild($elt_reponse);
	
	// place l'élément 'donnees' dans l'élément 'data'
	$elt_donnees = $doc->createElement('donnees');
	$elt_data->appendChild($elt_donnees);
	
	// traitement des réservations
	if (sizeof($lesSalles) > 0) {
		foreach ($lesSalles as $uneSalle)
		{
			// crée un élément vide 'Salle'
			$elt_salle = $doc->createElement('salle');
			// place l'élément 'salle' dans l'élément 'donnees'
			$elt_donnees->appendChild($elt_salle);
		
			// crée les éléments enfants de l'élément 'salle'
			$elt_id         = $doc->createElement('id', $uneSalle->getId());
			$elt_salle->appendChild($elt_id);
			$elt_room_name  = $doc->createElement('room_name', $uneSalle->getRoom_name());
			$elt_salle->appendChild($elt_room_name);
			$elt_capacity = $doc->createElement('capacity', $uneSalle->getCapacity());
			$elt_salle->appendChild($elt_capacity);
			$elt_AreaName  = $doc->createElement('AreaName', $uneSalle->getAreaName());
			$elt_salle->appendChild($elt_AreaName);
		}
	}
	
	// Mise en forme finale
	$doc->formatOutput = true;
	
	// renvoie le contenu XML
	echo $doc->saveXML();
	return;
}
?>
