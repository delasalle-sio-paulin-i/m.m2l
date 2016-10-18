<?php
// on vérifie si le demandeur de cette action a le niveau administrateur ou utilisateur
if ($_SESSION['niveauUtilisateur'] != 'administrateur' && $_SESSION['niveauUtilisateur'] != 'utilisateur') {
	// si l'utilisateur n'a pas le niveau administrateur, il s'agit d'une tentative d'accès frauduleux
	// dans ce cas, on provoque une redirection vers la page de connexion
	header ("Location: index.php?action=Deconnecter");
}
else {
	if(!isset ($_POST["mdp1"]) && !isset ($_POST["mdp2"])){
		$msg="";
	}
	elseif( !isset ($_POST["mdp1"]) || !isset ($_POST["mdp2"]) ) {
		// si les données n'ont pas été postées, c'est le premier appel du formulaire : affichage de la vue sans message d'erreur
		$msg= "Merci de remplir tous les champs";
	}
	elseif($_POST["mdp1"] != $_POST["mdp2"]){
		$msg= "Les deux mots de passes rentrés ne sont pas identiques";
	}elseif($_SESSION["mdp"] == $_POST["mdp1"]){
		$msg= "Le nouveau mot de passe doit etre different de l'ancien";	
	}else{
		include_once ('modele/DAO.class.php');
		$dao = new DAO();
		$dao->modifierMdpUser($_SESSION["nom"], $_SESSION["mdp"]);
		$msg= "Mot de passe mis à jour !";
	}
	include_once ('vues/VueChangerDeMdp.php');
	
	
}


?>