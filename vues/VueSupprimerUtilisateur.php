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
		
		
	</head>
	
	<body>
	<?php if($message!=""){?>
		<div data-role="dialog" id="affichage_message" data-close-btn="none">
			<div data-role="header" data-theme="a">
				<h3>Information...</h3>
			</div>
		
			<div data-role="content">
				<p style="text-align: center;">
					<img src="images/information.png" class="image" />
				</p>
				<p style="text-align: center;"><?php echo $message; ?></p>
			</div>
			<?php $message=""; ?>
			<div data-role="footer" class="ui-bar" data-theme="a">
				<a href="index.php?action=ConfirmerReservation" data-transition="flip">Fermer</a>
			</div>
		</div>
		<?php  }?>
		<div data-role="page" id="page_principale">
			<div data-role="header" data-theme="<?php echo $themeNormal; ?>">
				<h4>M2L-GRR</h4>
				<a href="index.php?action=Menu" data-transition="<?php echo $transition; ?>">Retour menu</a>
			</div>
			<div data-role="content">
				<h4 style="text-align: center; margin-top: 0px; margin-bottom: 0px;">Supprimer un compte utilisateur</h4>
				<form action="index.php?action=CreerUtilisateur" method="post" data-ajax="false">
					<div data-role="fieldcontain" class="ui-hide-label">
						<input type="text" name="txtName" id="txtName" required placeholder="Entrez le nom de l'utilisateur" value="<?php echo $name; ?>">
					</div>
					
					
					
					<div data-role="fieldcontain">
						<input type="submit" name="btnSupprimerUtilisateur" id="btnSupprimerUtilisateur" value="Supprimer l'utilisateur" data-mini="true">
					</div>
				</form>
					</div>
			
			<div data-role="footer" data-position="fixed" data-theme="<?php echo $themeNormal; ?>">
				<h4>Suivi des réservations de salles<br>Maison des ligues de Lorraine (M2L)</h4>
			</div>
		</div>
		
		
	</body>
</html>