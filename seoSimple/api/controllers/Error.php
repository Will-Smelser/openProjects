<?php
class Error extends Controller  implements Control{
	
	public function no_method(){
		(new ApiResponseJSON())->failure("Invalid Request - No Method or Class");
	}
}
?>