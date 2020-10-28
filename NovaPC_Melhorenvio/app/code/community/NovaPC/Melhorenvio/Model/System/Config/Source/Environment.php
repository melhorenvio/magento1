<?php

class NovaPC_Melhorenvio_Model_System_Config_Source_Environment
{
	public function toOptionArray()
	{
		return array(
			array("value" => 1, "label" => "Produção"),
			array("value" => 0, "label" => "Homologação"),
		);
	}
}

?>