<?php

function generateQuery()
{
	$sql = "SELECT * FROM `program` LEFT JOIN `prog_category` ON `pCategoryIdx` = `cID`";
	$cat = intval(Vars::get(POST_CAT_ID));
	$prog = intval(Vars::get(POST_PROG_ID));
	$lang = String::safeSQL(Vars::get(POST_LANG_ID));
	$name = String::safeSQL(Vars::get(POST_PROG_NAME));
	
	$where = Array();
	if( Vars::defined(POST_CAT_ID) && $cat > 0 )
		$where[] = "`cID`=".$cat;
	if( Vars::defined(POST_LANG_ID) && $lang )
		$where[] = "INSTR(`pLanguages`,'{$lang}')>0";
	if( Vars::defined(POST_PROG_ID) && $prog )
		$where[] = "`pID` = ".$prog;
	elseif( Vars::defined(POST_PROG_NAME) && $name )
		$where[] = "`pName` = '{$name}'";
	
	if( $where )
		$sql .= " WHERE ".implode(' AND ', $where);
	
	switch( Vars::get(POST_SORT_ID) )
	{
		case SORT_NAME: $sql.= " ORDER BY `pName`"; break;
		case SORT_CAT:  $sql.= " ORDER BY `cName`, `pName`"; break;
		default: $sql.= " ORDER BY `pDownloads` DESC, `pName`"; break;
	}
	//echo "<!-- $sql -->\n";
	return $sql;
}

function generateLanguagesField($langs, $array)
{
	$ret = Array();
	$ls = explode(',',$langs);
	foreach($ls as $l)
		if( $lang = $array["lang_$l"] )
			$ret[] = $lang;
	return implode(', ', $ret);
}

function generateProgramPanelContent()
{
	if( $rows = DB::select(generateQuery()) )
	{
		echo "\t<center class='progs_found'><label>". Lang::translateQuantity('progs.nb_progs_found', count($rows)) ."</label></center>\n";
		$langs = Lang::translateKeys("^lang");
		$texts = Lang::translateKeys("^progs\.label\.");
		$dl_text = Lang::translate('progs.img_title.dl');
		foreach($rows as $row)
		{
			$row->cName = Lang::translate($row->cName);
			$row->pDescription = Lang::translate($row->pDescription);
			$file = DIR_ATTACHMENTS.'progs/'.$row->pFilename;
			if( @file_exists($file) )
			{
				$date = Lang::getDate(filemtime($file));
				$version = $row->pVersion ? "{$row->pVersion} ($date)" : $date;
				$nbImgs = DB::getField("SELECT COUNT(*) FROM `prog_photos` WHERE `phProgIDx`={$row->pID}");
				$txtNbImgs = Lang::translateQuantity('progs.label.nb_images', $nbImgs);
				if( $nbImgs )
					$txtNbImgs = "<a id='thumbs_id_{$row->pID}' href='#'class='thumbs'>{$txtNbImgs}</a>";
?>
	<table class="prog <?php echo $row->cClass; ?>" id="prog_id_<?php echo $row->pID; ?>">
		<colgroup><col /><col class="second" /><col class="third" /></colgroup>
		<tr class="title">
			<td colspan="4">
				<?php echo $row->pName; ?>
				<a href="<?php echo Media::DATA('progs/'.$row->pFilename); ?>"><img src="<?php echo Media::IMAGE('progs/download'); ?>" title="<?php echo $dl_text; ?>" /></a>
			</td>
		</tr>
		<tr class="info">
			<td rowspan="2" class="image"><img src="<?php echo Media::IMAGE('progs/'.$row->cIcon); ?>" title="<?php echo $row->cName; ?>" /></td>
			<td><label><?php echo $texts['progs.label.category']; ?></label>: <?php echo $row->cName; ?></td>
			<td><label><?php echo $texts['progs.label.langs']; ?></label>: <?php echo generateLanguagesField($row->pLanguages, $langs); ?></td>
			<td><label><?php echo $texts['progs.label.size']; ?></label>: <?php echo Common::toFileSize(filesize($file)); ?></td>
		</tr>
		<tr class="info">
			<td><label><?php echo $texts['progs.label.version']; ?></label>: <?php echo $version; ?></td>
			<td><label><?php echo $texts['progs.label.downloads']; ?></label>: <?php echo String::setQuantity($texts['progs.label.downloads.times'], $row->pDownloads); ?></td>
			<td><label><?php echo $texts['progs.label.previews']; ?></label>: <?php echo $txtNbImgs; ?></td>
		</tr>
		<tr>
			<td colspan="4" class="desc">
				<div class="info"><label><?php echo $texts['progs.label.description']; ?></label></div>
				<p><?php echo $row->pDescription; ?></p>
			</td>
		</tr>
	</table>
<?php
			}
			else
				Common::reportRunningWarning("File: '$file' doesn't exists. So we are unable to define its size and mdate !");
		}
	}
	else
	{
?>
		<center class='progs_found warning'><label><?php echo Lang::translateQuantity('progs.nb_progs_found', 0); ?></label></center>
<?php
	}
}
?>