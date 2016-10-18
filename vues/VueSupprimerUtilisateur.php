<!doctype html>
<html>
	<head>
		<?php include_once ('vues/head.php'); ?>
	</head>

<body>
		<div data-role="page">
			<div data-role="header" data-theme="<?php echo $themeNormal; ?>">
				<h4>M2L-GRR</h4>
				<a href="index.php?action=Menu" data-transition="<?php echo $transition; ?>">Retour menu</a>
			</div>
			<div data-role="content">
				<h4 style="text-align: center; margin-top: 0px; margin-bottom: 0px;">Consulter mes réservations</h4>
				<p style="text-align: center;"><?php echo $message; ?></p>
				<ul data-role="listview" style="margin-top: 5px;">
				</ul>
				
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