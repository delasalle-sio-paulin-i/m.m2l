<?php
// Projet Réservations M2L - version web mobile
// fichier : 
// Rôle : 
// Création : 
// Mise à jour : 

class Salle
{
	// ------------------------------------------------------------------------------------------------------
	// ---------------------------------- Membres privés de la classe ---------------------------------------
	// ------------------------------------------------------------------------------------------------------

	private $id;				// identifiant de la salle (numéro automatique dans la BDD)
	private $Room_name;				// nom de la salle
	private $Capacity;				// capacité
	private $AreaName;			// nom de la zone

	// ------------------------------------------------------------------------------------------------------
	// ----------------------------------------- Constructeur -----------------------------------------------
	// ------------------------------------------------------------------------------------------------------

	public function Utilisateur($unId, $unRoom_name, $unCapacity, $unAreaName) {
		$this->id = $unId;
		$this->Room_name = $unRoom_name;
		$this->Capacity = $unCapacity;
		$this->AreaName = $unAreaName;
	}

	// ------------------------------------------------------------------------------------------------------
	// ---------------------------------------- Getters et Setters ------------------------------------------
	// ------------------------------------------------------------------------------------------------------

	public function getId()	{return $this->id;}
	public function setId($unId) {$this->id = $unId;}

	public function getRoom_name()	{return $this->Room_name;}
	public function setRoom_name($unRoom_name) {$this->Room_name = $unRoom_name;}

	public function getCapacity()	{return $this->Capacity;}
	public function setCapacity($unCapacity) {$this->Capacity = $unCapacity;}

	public function getAreaName()	{return $this->AreaName;}
	public function setAreaName($unAreaName) {$this->AreaName = $unAreaName;}

	// ------------------------------------------------------------------------------------------------------
	// -------------------------------------- Méthodes d'instances ------------------------------------------
	// ------------------------------------------------------------------------------------------------------

	public function toString() {
		$msg = 'Salle : <br>';
		$msg .= 'id : ' . $this->getId() . '<br>';
		$msg .= 'room name : ' . $this->getRoom_name() . '<br>';
		$msg .= 'Capacity : ' . $this->getCapacity() . '<br>';
		$msg .= 'Area Name : ' . $this->getAreaName() . '<br>';
		$msg .= '<br>';

		return $msg;
	}

} // fin de la classe Salle







// ATTENTION : on ne met pas de balise de fin de script pour ne pas prendre le risque
// d'enregistrer d'espaces après la balise de fin de script !!!!!!!!!!!!