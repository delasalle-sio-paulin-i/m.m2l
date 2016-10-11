<?php
// -------------------------------------------------------------------------------------------------------------------------
//                                                 DAO : Data Access Object
//                   Cette classe fournit des méthodes d'accès à la bdd mrbs (projet Réservations M2L)
//                                                 Elle utilise l'objet PDO
//                       Auteur : JM Cartron                       Dernière modification : 1/9/2016
// -------------------------------------------------------------------------------------------------------------------------

// liste des méthodes de cette classe (dans l'ordre alphabétique) :

// __construct                   : le constructeur crée la connexion $cnx à la base de données
// __destruct                    : le destructeur ferme la connexion $cnx à la base de données
// annulerReservation            : enregistre l'annulation de réservation dans la bdd
// aPasseDesReservations         : recherche si l'utilisateur ($name) a passé des réservations à venir
// confirmerReservation          : enregistre la confirmation de réservation dans la bdd
// creerLesDigicodesManquants    : mise à jour de la table mrbs_entry_digicode (si besoin) pour créer les digicodes manquants
// creerUtilisateur              : enregistre l'utilisateur dans la bdd
// envoyerMdp                    : envoie un mail à l'utilisateur avec son nouveau mot de passe
// estLeCreateur                 : teste si un utilisateur ($nomUser) est le créateur d'une réservation ($idReservation)
// existeReservation             : fournit true si la réservation ($idReservation) existe, false sinon
// existeUtilisateur             : fournit true si l'utilisateur ($nomUser) existe, false sinon
// genererUnDigicode             : génération aléatoire d'un digicode de 6 caractères hexadécimaux
// getLesReservations            : fournit la liste des réservations à venir d'un utilisateur ($nomUser)
// getLesSalles                  : fournit la liste des salles disponibles à la réservation
// getNiveauUtilisateur          : fournit le niveau d'un utilisateur identifié par $nomUser et $mdpUser
// getReservation                : fournit un objet Reservation à partir de son identifiant $idReservation
// getUtilisateur                : //fournit un objet Utilisateur à partir de son nom $nomUser
// modifierMdpUser               : enregistre le nouveau mot de passe de l'utilisateur dans la bdd après l'avoir hashé en MD5
// supprimerUtilisateur          : supprime l'utilisateur dans la bdd
// testerDigicodeBatiment        : teste si le digicode saisi ($digicodeSaisi) correspond bien à une réservation de salle quelconque
// testerDigicodeSalle           : teste si le digicode saisi ($digicodeSaisi) correspond bien à une réservation

// certaines méthodes nécessitent les fichiers Reservation.class.php, Utilisateur.class.php, Salle.class.php et Outils.class.php
include_once ('Utilisateur.class.php');
include_once ('Reservation.class.php');
include_once ('Salle.class.php');
include_once ('Outils.class.php');

// inclusion des paramètres de l'application
include_once ('parametres.localhost.php');

// début de la classe DAO (Data Access Object)
class DAO
{
	// ------------------------------------------------------------------------------------------------------
	// ---------------------------------- Membres privés de la classe ---------------------------------------
	// ------------------------------------------------------------------------------------------------------
		
	private $cnx;				// la connexion à la base de données
	
	// ------------------------------------------------------------------------------------------------------
	// ---------------------------------- Constructeur et destructeur ---------------------------------------
	// ------------------------------------------------------------------------------------------------------
	public function __construct() {
		global $PARAM_HOTE, $PARAM_PORT, $PARAM_BDD, $PARAM_USER, $PARAM_PWD;
		try
		{	$this->cnx = new PDO ("mysql:host=" . $PARAM_HOTE . ";port=" . $PARAM_PORT . ";dbname=" . $PARAM_BDD,
							$PARAM_USER,
							$PARAM_PWD);
			return true;
		}
		catch (Exception $ex)
		{	echo ("Echec de la connexion a la base de donnees <br>");
			echo ("Erreur numero : " . $ex->getCode() . "<br />" . "Description : " . $ex->getMessage() . "<br>");
			echo ("PARAM_HOTE = " . $PARAM_HOTE);
			return false;
		}
	}
	
	public function __destruct() {
		unset($this->cnx);
	}

	// ------------------------------------------------------------------------------------------------------
	// -------------------------------------- Méthodes d'instances ------------------------------------------
	// ------------------------------------------------------------------------------------------------------



	// mise à jour de la table mrbs_entry_digicode (si besoin) pour créer les digicodes manquants
	// cette fonction peut dépanner en cas d'absence des triggers chargés de créer les digicodes
	// modifié par Jim le 5/5/2015
	public function creerLesDigicodesManquants()
	{	// préparation de la requete de recherche des réservations sans digicode
		$txt_req1 = "Select id from mrbs_entry where id not in (select id from mrbs_entry_digicode)";
		$req1 = $this->cnx->prepare($txt_req1);
		// extraction des données
		$req1->execute();
		// extrait une ligne du résultat :
		$uneLigne = $req1->fetch(PDO::FETCH_OBJ);
		// tant qu'une ligne est trouvée :
		while ($uneLigne)
		{	// génération aléatoire d'un digicode de 6 caractères hexadécimaux
			$digicode = $this->genererUnDigicode();
			// préparation de la requete d'insertion
			$txt_req2 = "insert into mrbs_entry_digicode (id, digicode) values (:id, :digicode)";
			$req2 = $this->cnx->prepare($txt_req2);
			// liaison de la requête et de ses paramètres
			$req2->bindValue("id", $uneLigne->id, PDO::PARAM_INT);
			$req2->bindValue("digicode", $digicode, PDO::PARAM_STR);
			// exécution de la requête
			$req2->execute();
			// extrait la ligne suivante
			$uneLigne = $req1->fetch(PDO::FETCH_OBJ);
		}
		// libère les ressources du jeu de données
		$req1->closeCursor();
		return;
	}
	
	/*
	 // mise à jour de la table mrbs_entry_digicode (si besoin) pour créer les digicodes manquants
	 // cette fonction peut dépanner en cas d'absence des triggers chargés de créer les digicodes
	 // modifié par Jim le 23/9/2015
	 public function creerLesDigicodesManquants()
	 {	// récupération de la date du jour
		 $dateCreation = date('Y-m-d H:i:s', time());
		 // préparation de la requete de recherche des réservations sans digicode
		 $txt_req1 = "Select id from mrbs_entry where id not in (select id from mrbs_entry_digicode)";
		 $req1 = $this->cnx->prepare($txt_req1);
		 // extraction des données
		 $req1->execute();
		 // extrait une ligne du résultat :
		 $uneLigne = $req1->fetch(PDO::FETCH_OBJ);
		 // tant qu'une ligne est trouvée :
		
		 while ($uneLigne)
		 {	// génération aléatoire d'un digicode de 6 caractères hexadécimaux
			 $digicode = $this->genererUnDigicode();
			 // préparation de la requete d'insertion
			 $txt_req2 = "insert into mrbs_entry_digicode (id, digicode, dateCreation) values (:id, :digicode, :dateCreation)";
			 $req2 = $this->cnx->prepare($txt_req2);
			 // liaison de la requête et de ses paramètres
			 $req2->bindValue("id", $uneLigne->id, PDO::PARAM_INT);
			 $req2->bindValue("digicode", $digicode, PDO::PARAM_STR);
			 $req2->bindValue("dateCreation", $dateCreation, PDO::PARAM_INT);
			 // exécution de la requête
			 $req2->execute();
			 // extrait la ligne suivante
			 $uneLigne = $req1->fetch(PDO::FETCH_OBJ);
		 }
		 // libère les ressources du jeu de données
		 $req1->closeCursor();
		 return;
	 }
	 */

	// enregistre l'utilisateur dans la bdd
	// modifié par Jim le 26/5/2016
	public function creerUtilisateur($unUtilisateur)
	{	// préparation de la requete
		$txt_req = "insert into mrbs_users (level, name, password, email) values (:level, :name, :password, :email)";
		$req = $this->cnx->prepare($txt_req);
		// liaison de la requête et de ses paramètres
		$req->bindValue("level", utf8_decode($unUtilisateur->getLevel()), PDO::PARAM_STR);
		$req->bindValue("name", utf8_decode($unUtilisateur->getName()), PDO::PARAM_STR);
		$req->bindValue("password", utf8_decode(md5($unUtilisateur->getPassword())), PDO::PARAM_STR);
		$req->bindValue("email", utf8_decode($unUtilisateur->getEmail()), PDO::PARAM_STR);
		// exécution de la requete
		$ok = $req->execute();
		return $ok;
	}

	// fournit true si l'utilisateur ($nomUser) existe, false sinon
	// modifié par Jim le 5/5/2015
	public function existeUtilisateur($nomUser)
	{	// préparation de la requete de recherche
		$txt_req = "Select count(*) from mrbs_users where name = :nomUser";
		$req = $this->cnx->prepare($txt_req);
		// liaison de la requête et de ses paramètres
		$req->bindValue("nomUser", $nomUser, PDO::PARAM_STR);
		// exécution de la requete
		$req->execute();
		$nbReponses = $req->fetchColumn(0);
		// libère les ressources du jeu de données
		$req->closeCursor();
		
		// fourniture de la réponse
		if ($nbReponses == 0)
			return false;
		else
			return true;
	}

	// génération aléatoire d'un digicode de 6 caractères hexadécimaux
	// modifié par Jim le 5/5/2015
	public function genererUnDigicode()
	{   $caracteresUtilisables = "0123456789ABCDEF";
		$digicode = "";
		// on ajoute 6 caractères
		for ($i = 1 ; $i <= 6 ; $i++)
		{   // on tire au hasard un caractère (position aléatoire entre 0 et le nombre de caractères - 1)
			$position = rand (0, strlen($caracteresUtilisables)-1);
			// on récupère le caracère correspondant à la position dans $caracteresUtilisables
			$unCaractere = substr ($caracteresUtilisables, $position, 1);
			// on ajoute ce caractère au digicode
			$digicode = $digicode . $unCaractere;
		}
		// fourniture de la réponse
		return $digicode;
	}

	// fournit la liste des réservations à venir d'un utilisateur ($nomUser)
	// le résultat est fourni sous forme d'une collection d'objets Reservation
	// modifié par Jim le 30/9/2015
	public function getLesReservations($nomUser)
	{	// préparation de la requete de recherche
		$txt_req = "Select mrbs_entry.id as id_entry, timestamp, start_time, end_time, room_name, status, digicode";
		$txt_req = $txt_req . " from mrbs_entry, mrbs_room, mrbs_entry_digicode";
		$txt_req = $txt_req . " where mrbs_entry.room_id = mrbs_room.id";
		$txt_req = $txt_req . " and mrbs_entry.id = mrbs_entry_digicode.id";
		$txt_req = $txt_req . " and create_by = :nomUser";
		$txt_req = $txt_req . " and start_time > :time";
		$txt_req = $txt_req . " order by start_time, room_name";
		
		$req = $this->cnx->prepare($txt_req);
		// liaison de la requête et de ses paramètres
		$req->bindValue("nomUser", $nomUser, PDO::PARAM_STR);
		$req->bindValue("time", time(), PDO::PARAM_INT);
		// extraction des données
		$req->execute();
		$uneLigne = $req->fetch(PDO::FETCH_OBJ);
		
		// construction d'une collection d'objets Reservation
		$lesReservations = array();
		// tant qu'une ligne est trouvée :
		while ($uneLigne)
		{	// création d'un objet Reservation
			$unId = utf8_encode($uneLigne->id_entry);
			$unTimeStamp = utf8_encode($uneLigne->timestamp);
			$unStartTime = utf8_encode($uneLigne->start_time);
			$unEndTime = utf8_encode($uneLigne->end_time);
			$unRoomName = utf8_encode($uneLigne->room_name);
			$unStatus = utf8_encode($uneLigne->status);
			$unDigicode = utf8_encode($uneLigne->digicode);
				
			$uneReservation = new Reservation($unId, $unTimeStamp, $unStartTime, $unEndTime, $unRoomName, $unStatus, $unDigicode);
			// ajout de la réservation à la collection
			$lesReservations[] = $uneReservation;
			// extrait la ligne suivante
			$uneLigne = $req->fetch(PDO::FETCH_OBJ);
		}
		// libère les ressources du jeu de données
		$req->closeCursor();
		// fourniture de la collection
		return $lesReservations;
	}

	
	/*public function getLesSalles($room_name)
=======
	//fournit un objet Utilisateur à partir de son nom $nomUser
	public function getUtilisateur($nomUser)
>>>>>>> branch 'master' of https://github.com/delasalle-sio-paulin-i/m.m2l.git
	{	// préparation de la requête de recherche
	$txt_req = "Select level from mrbs_users where name=";
	$req = $this->cnx->prepare($txt_req);
	// liaison de la requête et de ses paramètres
	$req->bindValue("nomUser", $nomUser, PDO::PARAM_STR);
	// extraction des données
	$req->execute();
	$uneLigne = $req->fetch(PDO::FETCH_OBJ);
	
<<<<<<< HEAD
		// libère les ressources du jeu de données
		$req->closeCursor();
		// fourniture de la réponse
		return $reponse;
	}*/
=======
	// libère les ressources du jeu de données
	$req->closeCursor();
	// fourniture de la réponse
	return $reponse;
	}
=======
>>>>>>> branch 'master' of https://github.com/delasalle-sio-paulin-i/m.m2l.git
>>>>>>> branch 'master' of https://github.com/delasalle-sio-paulin-i/m.m2l.git
	
	
	// fournit le niveau d'un utilisateur identifié par $nomUser et $mdpUser
	// renvoie "utilisateur" ou "administrateur" si authentification correcte, "inconnu" sinon
	// modifié par Jim le 5/5/2015
	public function getNiveauUtilisateur($nomUser, $mdpUser)
	{	// préparation de la requête de recherche
		$txt_req = "Select level from mrbs_users where name = :nomUser and password = :mdpUserCrypte and level > 0";
		$req = $this->cnx->prepare($txt_req);
		// liaison de la requête et de ses paramètres
		$req->bindValue("nomUser", $nomUser, PDO::PARAM_STR);
		$req->bindValue("mdpUserCrypte", md5($mdpUser), PDO::PARAM_STR);		
		// extraction des données
		$req->execute();
		$uneLigne = $req->fetch(PDO::FETCH_OBJ);
		// traitement de la réponse
		$reponse = "inconnu";
		if ($uneLigne)
		{	$level = $uneLigne->level;
			if ($level == "1") $reponse = "utilisateur";
			if ($level == "2") $reponse = "administrateur";
		}
		// libère les ressources du jeu de données
		$req->closeCursor();
		// fourniture de la réponse
		return $reponse;
	}	

	// teste si le digicode saisi ($digicodeSaisi) correspond bien à une réservation
	// de la salle indiquée ($idSalle) pour l'heure courante
	// fournit la valeur 0 si le digicode n'est pas bon, 1 si le digicode est bon
	// modifié par Jim le 18/5/2015
	public function testerDigicodeSalle($idSalle, $digicodeSaisi)
	{	global $DELAI_DIGICODE;
		// préparation de la requete de recherche
		$txt_req = "Select count(*)";
		$txt_req = $txt_req . " from mrbs_entry, mrbs_entry_digicode";
		$txt_req = $txt_req . " where mrbs_entry.id = mrbs_entry_digicode.id";
		$txt_req = $txt_req . " and room_id = :idSalle";
		$txt_req = $txt_req . " and digicode = :digicodeSaisi";
		$txt_req = $txt_req . " and (start_time - :delaiDigicode) < " . time();
		$txt_req = $txt_req . " and (end_time + :delaiDigicode) > " . time();
		
		$req = $this->cnx->prepare($txt_req);
		// liaison de la requête et de ses paramètres
		$req->bindValue("idSalle", $idSalle, PDO::PARAM_STR);
		$req->bindValue("digicodeSaisi", $digicodeSaisi, PDO::PARAM_STR);	
		$req->bindValue("delaiDigicode", $DELAI_DIGICODE, PDO::PARAM_INT);	
				
		// exécution de la requete
		$req->execute();
		$nbReponses = $req->fetchColumn(0);
		// libère les ressources du jeu de données
		$req->closeCursor();
		
		// fourniture de la réponse
		if ($nbReponses == 0)
			return "0";
		else
			return "1";
	}
//<<<<<<<<<<<< HEAD
	
	public function testerDigicodeBatiment ($digicodeSaisi)
	{
		global $DELAI_DIGICODE;
		
		$txt_req = "Select count(*)";
		$txt_req = $txt_req . " from mrbs_entry, mrbs_entry_digicode";
		$txt_req = $txt_req . " where mrbs_entry.id = mrbs_entry_digicode.id";
		
		$txt_req = $txt_req . " and digicode = :digicodeSaisi";
		$txt_req = $txt_req . " and (start_time - :delaiDigicode) < " . time();
		$txt_req = $txt_req . " and (end_time + :delaiDigicode) > " . time();
		

		$req = $this->cnx->prepare($txt_req);
		// liaison de la requête et de ses paramètres
		
		$req->bindValue("digicodeSaisi", $digicodeSaisi, PDO::PARAM_STR);
		$req->bindValue("delaiDigicode", $DELAI_DIGICODE, PDO::PARAM_INT);
		
		$req->execute();
		$nbReponses = $req->fetch(PDO::FETCH_OBJ);
		// libère les ressources du jeu de données
		//$req->closeCursor();
		
		// fourniture de la réponse
		if ($nbReponses == 0)
			return "0";
			else
				return "1";
		
	}
	
	
	
	
	
	
	
	
	
	
	
//=======
//>>>>>>> branch 'master' of https://github.com/delasalle-sio-paulin-i/m.m2l.git
	

	
	// Fonction qui va supprimer l'utilisateur
	public function supprimerUtilisateur($name) {
	
		$txt_req = "Delete From mrbs_users  Where name=:name  ";
		$req = $this->cnx->prepare($txt_req);
		// liaison de la requête et de ses paramètres
		$req->bindValue("name", utf8_decode($name), PDO::PARAM_STR);
		// exécution de la requete
		$ok = $req->execute();
		return $ok;
	}


	public function aPasseDesReservations($nomUser)
	{
// 		include_once ('DAO.class.php');
// 		$dao = new DAO();
// 		$testResa= $dao->getLesReservations($nomUser);
// 		if ($testResa== !null)
// 			return TRUE;
// 		else 
// 			return FALSE;

		// préparation de la requete de recherche
		$txt_req = "Select *";
		$txt_req = $txt_req . " from mrbs_entry";
		$txt_req = $txt_req . " where  create_by = :nomUser";
// 		$txt_req = $txt_req . " and start_time > :time";
		
		$req = $this->cnx->prepare($txt_req);
		// liaison de la requête et de ses paramètres
		$req->bindValue("nomUser", $nomUser, PDO::PARAM_STR);
// 		$req->bindValue("time", time(), PDO::PARAM_INT);
		// extraction des données
		$req->execute();
		$uneLigne = $req->fetch(PDO::FETCH_OBJ);
		if (empty($uneLigne) )
 			return FALSE;
 		else
 			return TRUE;
		
	}
	
	public function getReservation($idReservation)
	{	// préparation de la requête de recherche
		$txt_req = "Select * from mrbs_entry where id = :id ";
		$req = $this->cnx->prepare($txt_req);
		// liaison de la requête et de ses paramètres
		$req->bindValue("id", $idReservation, PDO::PARAM_STR);
		// extraction des données
		$req->execute();
		$uneLigne = $req->fetch(PDO::FETCH_OBJ);
		// traitement de la réponse
		$unId = utf8_encode($uneLigne->id_entry);
		$unTimeStamp = utf8_encode($uneLigne->timestamp);
		$unStartTime = utf8_encode($uneLigne->start_time);
		$unEndTime = utf8_encode($uneLigne->end_time);
		$unRoomName = utf8_encode($uneLigne->room_name);
		$unStatus = utf8_encode($uneLigne->status);
		$unDigicode = utf8_encode($uneLigne->digicode);
		
		$uneReservation = new Reservation($unId, $unTimeStamp, $unStartTime, $unEndTime, $unRoomName, $unStatus, $unDigicode);
		// fourniture de la réponse
		return $uneReservation;
	}
	
	// annuler reservation
	
	public function annulerReservation($idReservation){
		$txt_req = "Delete From mrbs_entry Where id=:idRes  ";
		$req = $this->cnx->prepare($txt_req);
		// liaison de la requête et de ses paramètres
		$req->bindValue("idRes", utf8_decode($idReservation), PDO::PARAM_STR);
		// exécution de la requete
		$ok = $req->execute();
		return $ok;
	}
	
	// Confirmer reservation
	
	public function confirmerReservation($idReservation){
		$txt_req = "Update From mrbs_entry Set status = '1' Where id=:idRes  ";
		$req = $this->cnx->prepare($txt_req);
		// liaison de la requête et de ses paramètres
		$req->bindValue("idRes", utf8_decode($idReservation), PDO::PARAM_STR);
		// exécution de la requete
		$ok = $req->execute();
		return $ok;
	}
	
	//Envoyer mdp
	
	public function envoyerMdp($mail){
		$chaine = 'azertyuiopqsdfghjklmwxcvbn123456789';
		$nb_car= 8;
		$nb_lettres = strlen($chaine) - 1;
		$mdp = '';
		
		for($i=0; $i < $nb_car; $i++)
		{
			$pos = mt_rand(0, $nb_lettres);
			$car = $chaine[$pos];
			$mdp .= $car;
		}
		
		$txt_req = "Select id From mrbs_user Where email=:mail  ";
		$req = $this->cnx->prepare($txt_req);
		// liaison de la requête et de ses paramètres
		$req->bindValue("mail", utf8_decode($mail), PDO::PARAM_STR);
		// exécution de la requete
		$id = $req->execute();
		
		modifierMdpUser($id, $mdp);
		$subject= 'Votre nouveau mot de passe';
		$msg='Bonjour, voici votre nouveau mot de passe : '.$mdp;
		mail($mail, $subject, $msg);
		
	}
	
	public function modifierMdpUser($id, $mdp){
		$mdp=md5($mdp);
		$txt_req = "Update From mrbs_user Set password=:mdp Where id=:id  ";
		$req = $this->cnx->prepare($txt_req);
		// liaison de la requête et de ses paramètres
		$req->bindValue("mdp", utf8_decode($mdp), PDO::PARAM_STR);
		$req->bindValue("id", utf8_decode($id), PDO::PARAM_STR);
		// exécution de la requete
		$id = $req->execute();
	}
	
	
<<<<<<< HEAD
public function getLesSalles()
	{	// préparation de la requete de recherche
		$txt_req = "Select * from mrbs_room";
		$req = $this->cnx->prepare($txt_req);
		// liaison de la requête et de ses paramètres
		
		$req->execute();
		$uneLigne = $req->fetch(PDO::FETCH_OBJ);
		// construction d'une collection d'objets Reservation
		$lesSalles = array();
		// tant qu'une ligne est trouvée :
		while ($uneLigne)
		{	// création d'un objet Reservation
			
			$nom = utf8_encode($uneLigne->room_name);
				
			// extrait la ligne suivante
			$uneLigne = $req->fetch(PDO::FETCH_OBJ);
		}
		// libère les ressources du jeu de données
		$req->closeCursor();
		// fourniture de la collection
		return $lesSalles;
	}


	
		
	
	
}  // fin de la classe DAO
=======
<<<<<<< HEAD
=======
	public function existeReservation($idRes){
		$txt_req = "Select * From mrbs_entry Where id:=idRes  ";
		$req = $this->cnx->prepare($txt_req);
		// liaison de la requête et de ses paramètres
		$req->bindValue("idRes", utf8_decode($idRes), PDO::PARAM_STR);
		// exécution de la requete
		$res = $req->execute();
		if(empty($res)){
			return FALSE;
		}else{
			return TRUE;
		}
	}
	
	public function getLesSalles(){
		$date=strtotime("now");
		$txt_req = "Select * From mrbs_room Where id NOT IN (Select room_id From mrbs_entry Where start_time < :date And end_time > :date  ";
		$req = $this->cnx->prepare($txt_req);
		// liaison de la requête et de ses paramètres
		$req->bindValue("date", utf8_decode($date), PDO::PARAM_STR);
		// exécution de la requete
		$res = $req->execute();
		return $res;
	}
>>>>>>> branch 'master' of https://github.com/delasalle-sio-paulin-i/m.m2l.git
} // fin de la classe DAO
>>>>>>> branch 'master' of https://github.com/delasalle-sio-paulin-i/m.m2l.git

// ATTENTION : on ne met pas de balise de fin de script pour ne pas prendre le risque
// d'enregistrer d'espaces après la balise de fin de script !!!!!!!!!!!!