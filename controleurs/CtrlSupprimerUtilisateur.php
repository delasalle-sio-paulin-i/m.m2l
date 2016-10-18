<?php
// Projet Réservations M2L - version web mobile
// fichier : controleurs/CtrlCreerUtilisateur.php
// Rôle : traiter la demande de création d'un nouvel utilisateur
// Création : 21/10/2015 par JM CARTRON
// Mise à jour : 2/6/2016 par JM CARTRON

// on vérifie si le demandeur de cette action a le niveau administrateur
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
			include_once ('vues/VueAnnulerReservation.php');
			}
			else {
				if (!$dao->getUtilisateur($nomUser)){
					$message = "Suppression impossible, l'utilisateur n'existe pas !";
				}
					else { $dao->getLesReservations($nomUser);
						if ($lesReservations >= 0) {
							$message = "Suppression impossible, l'utilisateur à déjà passé des reéservations.";
							$typeMessage = 'avertissement';
							$themeFooter = $themeNormal;
							include_once ('vues/VueAnnulerReservation.php');
						}
						else {
							if (!$dao->supprimerUtilisateur) {
								$message = "Suppression impossible !!";
								$typeMessage = 'avertissement';
								$themeFooter = $themeNormal;
								include_once ('vues/VueAnnulerReservation.php');
							}
							// Plus qu'a envoyer un mail à l'utilisateur et compléter la vue.
							
						}
					}
			
			}
	}

	