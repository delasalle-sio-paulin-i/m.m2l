<?php
	// Projet Réservations M2L - version web mobile
	// Rôle : change de mdp
	// cette vue est appelée par le contôleur controleurs/CtrlChangeDeMdp.php
	// Création : 12/10/2015 par JM CARTRON
	// Mise à jour : 31/5/2016 par JM CARTRON
?>
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
				<h4 style="text-align: center; margin-top: 0px; margin-bottom: 0px;">Modifier mon mot de passe:</h4>
				<p style="text-align: center;"><?php echo $msg; ?></p>
				<form name="form1" id="form1" action="index.php?action=ChangerDeMdp" data-ajax="false" method="post" data-transition="<?php echo $transition; ?>">
							<div data-role="fieldcontain" class="ui-hide-label">
								<label for="mdp1">Nouveau mot de passe :</label>
								<input type="<?php if($afficherMdp == 'on') echo 'text'; else echo 'password'; ?>" name="mdp1" id="mdp1" data-mini="true" 
									required placeholder="Retaper le mot de passe">
		
								<label for="mdp2">Retaper le mot de passe :</label>
								<input type="<?php if($afficherMdp == 'on') echo 'text'; else echo 'password'; ?>" name="mdp2" id="mdp2" data-mini="true" 
									required placeholder="Retaper le mot de passe">
							</div>														
							<div data-role="fieldcontain" data-type="horizontal" class="ui-hide-label">
								<label for="caseAfficherMdp">Afficher le mot de passe en clair</label>
								<input type="checkbox" name="caseAfficherMdp" id="caseAfficherMdp" onclick="afficherMdp();" data-mini="true" <?php if ($afficherMdp == 'on') echo 'checked'; ?>>
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