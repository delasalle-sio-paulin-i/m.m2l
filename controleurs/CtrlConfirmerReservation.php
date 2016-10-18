<?php
include_once ('modele/DAO.class.php');
$dao = new DAO();
$msg="";
if(!empty($_POST["idRes"])){
	$reservation= $dao->getReservation($_POST["idRes"]);
	if($dao->existeReservation($_POST["idRes"])==false){
		$msg="Numéro de réservation inexistant !";
	}elseif($dao->estLeCreateur($_SESSION["nom"], $_POST["idRes"])== false)
	{
		$msg="Vous n'êtes pas l'auteur de cette réservation !";
	}elseif ($reservation->getStatus()==0){
		$msg="Cette reservation est deja confirmée !";
	}elseif($reservation->getEnd_time()<time()){
		$msg="Cette reservation est deja passée !";
	}else {
		$dao->confirmerReservation($_POST["idRes"]);
		$utilisateur=$dao->getUtilisateur($_POST["idRes"]);
		$mail=$utilisateur->getEmail();
		
		$subject= 'Confirmation reservation';
		$msg='Bonjour, votre reservation a bien été confirmée'.$mdp;
		try{
			mail($mail, $subject, $msg);
			$msg="Enregistrement effectué.<br>Vous allez recevoir un mail de confirmation.";
			return $msg;
		}
		catch(Exception $ex){
			$msg="Enregistrement effectué.<br>L'envoi du mail de confirmation a rencontré un problème.";
			return $msg;		
		}
		
	}
	
}
include_once ('vues/VueConfirmerReservation.php');


?>