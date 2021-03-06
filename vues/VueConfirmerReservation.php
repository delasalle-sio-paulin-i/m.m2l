<?php
	// Projet Réservations M2L - version web mobile
	// fichier : vues/VueConsulterReservations.php
	// Rôle : visualiser la liste des réservations à venir d'un utilisateur
	// cette vue est appelée par le contôleur controleurs/CtrlConsulterReservations.php
	// Création : 12/10/2015 par JM CARTRON
	// Mise à jour : 31/5/2016 par JM CARTRON
?>
<!doctype html>
<html>
	<head>
		<?php include_once ('vues/head.php'); ?>
	</head>
	 
	<body>
	<?php if($msg!=""){?>
		<div data-role="dialog" id="affichage_message" data-close-btn="none">
			<div data-role="header" data-theme="a">
				<h3>Information...</h3>
			</div>
		
			<div data-role="content">
				<p style="text-align: center;">
					<img src="images/information.png" class="image" />
				</p>
				<p style="text-align: center;"><?php echo $msg; ?></p>
			</div>
			<?php $msg=""; ?>
			<div data-role="footer" class="ui-bar" data-theme="a">
				<a href="index.php?action=ConfirmerReservation" data-transition="flip">Fermer</a>
			</div>
		</div>
		<?php  }?>
		<div data-role="page">
			<div data-role="header" data-theme="<?php echo $themeNormal; ?>">
				<h4>M2L-GRR</h4>
				<a href="index.php?action=Menu" data-transition="<?php echo $transition; ?>">Retour menu</a>
			</div>
			<div data-role="content">
				<h4 style="text-align: center; margin-top: 0px; margin-bottom: 0px;">Confirmer mes réservations</h4>
				<form name="form1" id="form1" action="index.php?action=ConfirmerReservation" data-ajax="false" method="post" data-transition="<?php echo $transition; ?>">
					<div data-role="fieldcontain" class="ui-hide-label">
						<label for="mdp1">Numero de reservation :</label>
						<input type="text" name="idRes" id="idRes" data-mini="true" required placeholder="Numero de reservation">
					</div>
					<div data-role="fieldcontain" style="margin-top: 0px; margin-bottom: 0px;">
						<p style="margin-top: 0px; margin-bottom: 0px;">
							<input type="submit" name="btnConnecter" id="btnConnecter" data-mini="true" data-ajax="false" value="Valider">
						</p>
					</div>
				</form>
				

			</div>
			<div data-role="footer" data-position="fixed" data-theme="<?php echo $themeNormal;?>">
				<h4>Suivi des réservations de salles<br>Maison des ligues de Lorraine (M2L)</h4>
			</div>
		</div>
		
	</body>
</html>