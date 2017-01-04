<h2>Home !!!</h2>
<hr />

<p>Ajouter une section Contact, pour tout problème technique...</p>
<p>Ajouter une section Aide</p>


<h3>Langages</h3>
<p><u>Super admin</u>: Ajout d'une nouvelle langue</p>

<h3>Textes</h3>
<p><u>Ajout</u></p>
<p><u>Recherche</u>: Modifier, supprimer</p>

<h3>Data</h3>
<p><u>Ajout</u></p>
<p><u>Recherche</u>: Modifier</p>
<p><i>Attention</i>: ajouter une clé 'admin', visible pour par les superAdmin ???</p>
<hr />

<?php
	/*
	$from = 400; // initial key index
	$to   = 113; // ... changed to this new index
	$nb   = 1;  // how many keys to change 
	for($i=0; $i<$nb; $i++)
	{
		$sql1 = "UPDATE `loc_key`  SET `locID` =$to WHERE `locID` =$from";
		$sql2 = "UPDATE `loc_text` SET `locIdx`=$to WHERE `locIdx`=$from";
		if( DB::update($sql1) && DB::update($sql2) )
			echo "<b>Key $from changed to $to</b><br />\n";
		else
			echo "<b>An error has occured !!!</b><br />\n";
		$from += 1; $to += 1;
	}
	*/

	echo "<hr /><br />\n";
	Common::print_r($_SESSION);
	//var_dump(Authentication::isSuperAdmin());
	Common::print_r($_SERVER);
?>