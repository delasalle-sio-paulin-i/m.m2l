<?php
	// Projet Réservations M2L - version web mobile
	// fichier : vues/VueCreerUtilisateur.php
	// Rôle : visualiser la demande de création d'un nouvel utilisateur
	// cette vue est appelée par le contôleur controleurs/CtrlCreerUtilisateur.php
	// Création : 12/10/2015 par JM CARTRON
	// Mise à jour : 2/6/2016 par JM CARTRON
?>
<!doctype html>
<html>
	<head>
		<?php include_once ('vues/head.php'); ?>
		
		<script>
			// associe une fonction à l'événement pageinit
			$(document).bind('pageinit', function() {
				<?php if ($typeMessage != '') { ?>
					// affiche la boîte de dialogue 'affichage_message'
					$.mobile.changePage('#affichage_message', {transition: "<?php echo $transition; ?>"});
				<?php } ?>
			} );
		</script>
	</head>
<?php 				
if ( $niveauUtilisateur == "administrateur" ) 
			{
				

try
{
    $bdd = new PDO('mysql:host=localhost;dbname=mrbs', '', '');
}
catch(Exception $e)
{
    die('Erreur : '.$e->getMessage());
}
?>
<form method="post" action=".php">
 
    <label for="Utilisateur">Supprimer utilisateur</label><br />
     <select name="name" id="name">
 
<?php
 
$reponse = $bdd->query('SELECT name FROM mrbs_users');
 
while ($donnees = $reponse->fetch())
{
?>
           <option value="<?php echo $donnees['Name']; ?>"> <?php echo $donnees['Name']; ?></option>
<?php
}
 
?>
</select>
 
</form>
			<?php } 

?>

		</div>
		</div>
	</body>
</html>