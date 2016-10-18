<?php
if ( $_SESSION['niveauUtilisateur'] != 'utilisateur' && $_SESSION['niveauUtilisateur'] != 'administrateur') {
	// si le demandeur n'est pas authentifié, il s'agit d'une tentative d'accès frauduleux
	// dans ce cas, on provoque une redirection vers la page de connexion
	header ("Location: index.php?action=Deconnecter");
}
else {
	// connexion du serveur web à la base MySQL
	include_once ('modele/DAO.class.php');
	$dao = new DAO();

	// mise à jour de la table mrbs_entry_digicode (si besoin) pour créer les digicodes manquants
	$dao->creerLesDigicodesManquants();

	// récupération des réservations à venir créées par l'utilisateur à l'aide de la méthode getLesReservations de la classe DAO
	$lesReservations = $dao->annulerReservation($idReservation);

	// mémorisation du nombre de réservations
	$nbReponses = sizeof($lesReservations);

	// préparation d'un message précédent la liste
	if ($nbReponses == 0) {
		$message = "Vous n'avez rien à supprimer !";
	}
	else {
		$message = "Vous avez " . $nbReponses . " réservations annulée(s) !";
	}

	// affichage de la vue
	include_once ('vues/VueConsulterReservations.php');

	unset($dao);		// fermeture de la connexion à MySQL
}