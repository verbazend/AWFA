<?php
class db  {


			
	public function __construct()
    {
        //var_dump(date("Y"));
		//die();
		
		//DB Configuration
		
		
		
    }
	
	
	function runQuery($query, $start = 0, $end = 250){
		
		$connection = mysql_connect($GLOBALS['db_server'], $GLOBALS['db_user'], $GLOBALS['db_password']);
		if (!$connection) {
		    die('DB Connection Error: ' . mysql_error());
		}
		mysql_select_db($GLOBALS['db_name']);
        
        $query = $query." Limit $start, $end;";

        $query = str_replace('|','`',$query);
        //echo("<span class='sql'>".$query."</span>\n\n");
        
		$result = mysql_query($query);
		if (!$result) {
		    die('Failed to Connect: ' . mysql_error());
		}
		
        //$outputarray = array();
        $i = 0;
        
        if (mysql_num_rows($result) == 0) {
            return false;   
        }
        
        while ($row = mysql_fetch_assoc($result)) {
            $outputarray[$i] = $row ;
            $i++;
        }
        
        $returnResult = $outputarray;
         
		//$returnResult = $outputarray;
		mysql_free_result($result);
        //var_dump($returnResult);.
        
        
		return self::ret($returnResult);
	}
    
    function insertQuery($query){
        
        $connection = mysql_connect($GLOBALS['db_server'], $GLOBALS['db_user'], $GLOBALS['db_password']);
        if (!$connection) {
            die('DB Connection Error: ' . mysql_error());
        }
        mysql_select_db($GLOBALS['db_name']);

        $query = str_replace('|','`',$query);
        //echo($query);
        //echo("<span class='sql'>".$query."</span>\n\n");
        $result = mysql_query($query);
        if (!$result) {
            die('Failed to Connect: ' . mysql_error());
        }
        
        
        return true;
    }
	

    
    function esc($inputValue){
        $connection = mysql_connect($GLOBALS['db_server'], $GLOBALS['db_user'], $GLOBALS['db_password']);
        if (!$connection) {
            die('DB Connection Error: ' . mysql_error());
        }
        mysql_select_db($GLOBALS['db_name']);
        /*
        $search=array("\\","\0","\n","\r","\x1a","'",'"');
        $replace=array("\\\\","\\0","\\n","\\r","\Z","\'",'\"');
        return str_replace($search,$replace,$inputValue);
        */
        return mysql_real_escape_string($inputValue);
    }
    
    function ret($input){
        
        $input = str_replace("rnrn","\n\n",$input);
        $input = str_replace(",rn",",\n",$input);
        $input = str_replace(".rn",".\n",$input);
        return str_replace("\'","'",$input);
    }
}
?>