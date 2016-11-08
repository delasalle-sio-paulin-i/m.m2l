<?php
include_once ('modele/DAO.class.php');
$dao = new DAO();
$msg="Données incomplètes ou incorrectes !";

if(!empty($_POST["idUser"])){
	if($dao->existeUtilisateur($_POST["idUser"])==false){
		$msg="Nom d'utilisateur inexistant !";
	}else {
		
		$utilisateur=$dao->getUtilisateur($_POST["idUser"]);
		$mail=$utilisateur->getEmail();
		$msg=$dao->envoyerMdp($mail);

	}
}else{
	$msg="";
}

include_once ('vues/VueDemanderMdp.php');


?>