<?php
// Service web du projet Réservations M2L
// Ecrit le 21/5/2015 par Jim
// Modifié le 2/6/2016 par Jim

// Ce service web permet à un administrateur authentifié d'enregistrer un nouvel utilisateur
// et fournit un compte-rendu d'exécution

// Le service web doit être appelé avec 5 paramètres : nomAdmin, mdpAdmin, name, level, email
// Les paramètres peuvent être passés par la méthode GET (pratique pour les tests, mais à éviter en exploitation) :
//     http://<hébergeur>/CreerUtilisateur.php?nomAdmin=admin&mdpAdmin=admin&name=jim&level=1&email=jean.michel.cartron@gmail.com

// Les paramètres peuvent être passés par la méthode POST (à privilégier en exploitation pour la confidentialité des données) :
//     http://<hébergeur>/CreerUtilisateur.php

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
	
	if ( !$dao->getNiveauUtilisateur($nom, $mdp) == 'administrateur' )
		$msg = "Erreur : Authentification incorrecte.";
	
	else 
	{
		// récupération des réservations à venir créées par l'utilisateur
		if(!$dao->getUtilisateur($nomUser))
		{
			$msg = "Nom d'utilisateur inexistant !";
		}
		
			else 
			{ $lesReservations=$dao->getLesReservations($nomUser);
				if (!empty($lesReservations)) {
					$msg = "Suppression impossible, l'utilisateur à déjà passé des reéservations.";
					
				}
				else
				{
					$user=$dao->getUtilisateur($nomUser);
					$mail=$user->getEmail();
					if ($dao->supprimerUtilisateur($nomUser) == false)
					{
						$msg = "Problème lors de la suppression de l'utilisateur !";
					}
					else 
					{
						$dao->supprimerUtilisateur($nomUser);
						try {
							mail($mail, 'Suppression de votre compte', 'Votre compte a été supprimé');
							$msg="Suppression effectuée. Un mail va être envoyé à l'utilisateur !";
						} catch (Exception $e) {
							$msg= "Suppression effectuée. L'envoi du mail à l'utilisateur a rencontré un problème !";
						}	

					}
				}
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
function creerFluxXML($msg)
{	// crée une instance de DOMdocument (DOM : Document Object Model)
	$doc = new DOMDocument();	

	// specifie la version et le type d'encodage
	$doc->version = '1.0';
	$doc->encoding = 'ISO-8859-1';
	
	// crée un commentaire et l'encode en ISO
	$elt_commentaire = $doc->createComment('Service web CreerUtilisateur - BTS SIO - Lycée De La Salle - Rennes');
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