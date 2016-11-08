<?php
// Projet Réservations M2L - version web mobile
// fichier : controleurs/CtrlCreerUtilisateur.php
// Rôle : traiter la demande de création d'un nouvel utilisateur
// Création : 21/10/2015 par JM CARTRON
// Mise à jour : 2/6/2016 par JM CARTRON

// on vérifie si le demandeur de cette action a le niveau administrateur
include_once ('modele/DAO.class.php');
$dao = new DAO();

include_once ('modele/Outils.class.php');


if ($_SESSION['niveauUtilisateur'] != 'administrateur') {
	// si l'utilisateur n'a pas le niveau administrateur, il s'agit d'une tentative d'accès frauduleux
	// dans ce cas, on provoque une redirection vers la page de connexion
	header ("Location: index.php?action=Deconnecter");
}
	else {
		if (! isset ($_POST ["txtName"])){
			$message = "Suppression impossible, données incorrectes ou incompletes.";
			$typeMessage = 'avertissement';
			$themeFooter = $themeNormal;
			}
			else {
				if (!$dao->getUtilisateur($_POST ["txtName"])){
					$message = "Nom d'utilisateur inexistant !";
				}
				else { $lesReservations=$dao->getLesReservations($_POST ["txtName"]);
						if (!empty($lesReservations)) {
							$message = "Suppression impossible, l'utilisateur à déjà passé des reéservations.";
							$typeMessage = 'avertissement';
							$themeFooter = $themeNormal;
						}else {
							$user=$dao->getUtilisateur($_POST ["txtName"]);
							$mail=$user->getEmail();
							if ($dao->supprimerUtilisateur($_POST ["txtName"])==false) {
								$message = "Problème lors de la suppression de l'utilisateur !";
								$typeMessage = 'avertissement';
								$themeFooter = $themeNormal;
							}else{
								try{
									$sujet="Suppression de votre compte";
									$msg="Votre compte a été supprimé";
									$message="Suppression effectuée. <br> Un mail va être envoyé à l'utilisateur !";
									Outils::envoyerMail($mail,$sujet, $msg, $ADR_MAIL_EMETTEUR);
								}catch(Exception $ex){
									$message="Suppression effectuée.<br>L'envoi du mail de confirmation a rencontré un problème.";
								}
							}
							
						}
					}
			
			}
			include_once ('vues/VueSupprimerUtilisateur.php');
				
	}

	