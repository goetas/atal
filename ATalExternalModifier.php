<?php
interface ATalExternalModifier{
	public function modify($value, array $params=array(), ATal $tal);
}
?>