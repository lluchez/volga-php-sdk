<?php

	function getPagesTable()
	{
	
		/** =========== Not admin =========== */
		if( ! IS_ADMIN_CONTEXT )
		{
			return Array
			(
				// -------- Language English --------
				'en' => Array
				(
					'info' => 'Informations' 
				),
				
				// -------- Language  French --------
				'fr' => Array
				(
					'info' => "Page d'informations"
				)
			);
		}
		
		/** =========== Admin =========== */
		else
		{
			return Array
			(
				// -------- Language English --------
				'en' => Array
				(
					'public_api' => "Volga API"
				),
				
				// -------- Language  French --------
				'fr' => Array
				(
					'public_api' => "Volga API"
				)
			);
		}
		
	}

?>