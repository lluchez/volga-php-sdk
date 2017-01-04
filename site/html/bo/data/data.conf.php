<?php
	if( ! Authentication::isSuperAdmin() && ! Authentication::getPrivileges('editData') )
		Common::error404();
	
	
	if( Vars::pDefined(KEY_FORM_HIDDEN) )
	{
		$fields = Vars::match("/id_(.*)$/", 1);
		if( count($fields) )
		{
			$success = Array();
			$errors  = Array();
			foreach($fields as $key => $val)
			{
				if( DataDAL::updateDataValue($key, $val) )
					$success[] = $key;
				else
					$errors[] = $key;
			}
			echo "OK:".implode(';', $success). "|";
			echo "KO:".implode(';', $errors) . "\n";
			//echo "KO:regexp_isRobot\n";
		}
		die();
	}
	
	Head::activateErrorPanel();

?>