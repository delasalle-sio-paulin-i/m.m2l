<?php
include_once ('modele/DAO.class.php');
$dao = new DAO();
if ( $_SESSION['niveauUtilisateur'] != 'utilisateur' && $_SESSION['niveauUtilisateur'] != 'administrateur') {
	// si le demandeur n'est pas authentifié, il s'agit d'une tentative d'accès frauduleux
	// dans ce cas, on provoque une redirection vers la page de connexion
	header ("Location: index.php?action=Deconnecter");
}
else {
	
	if ( ! isset ($_POST ["btnAnnulerReservation"]) == true) {
	
		$idReservation = '';
		include_once ('vues/VueAnnulerReservation.php');
	}
	
	else
	{
		$idReservation = $_POST ["txtReservation"];
		$nomUtilisateur = $_SESSION['nom'];
	
	

	// mise à jour de la table mrbs_entry_digicode (si besoin) pour créer les digicodes manquants
		if (!$dao->existeReservation($idReservation)){
			$message = "Annulation impossible, vous n'avez pas de réservation.";
			$typeMessage = 'avertissement';
			$themeFooter = $themeNormal;
			include_once ('vues/VueAnnulerReservation.php');
		}
		else {

			$laReservation = $dao->getReservation($idReservation);
			$laDateReservation = $laReservation->getEnd_time();
			
			if ($laDateReservation <= time()){
				$message = "Annulation impossible, la réservation est passée.";
				$typeMessage = 'avertissement';
				$themeFooter = $themeNormal;
				include_once ('vues/VueAnnulerReservation.php');
			}
			else {
				// On teste si l'utilisateur est le créateur de la réservation
				if ( !$dao->estLeCreateur($nomUtilisateur,$idReservation)){
					$message = "Annulation impossible, vous n'êtes pas le créateur de la réservation.";
					$typeMessage = 'avertissement';
					$themeFooter = $themeNormal;
					include_once ('vues/VueAnnulerReservation.php');
				}
			
				else {
					// Si la réservation existe et a été faite par l'utilisateur elle est annulée
					$ok = $dao->annulerReservation($idReservation);
			
					if ($ok) {
						$message = 'Réservation correctement annulée.';
						$typeMessage = 'information';
						$themeFooter = $themeNormal;
						include_once ('vues/VueAnnulerReservation.php');
					}
				}
			}
			
			}
			}
}