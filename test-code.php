<?php
/**
 * Instructions:
 * Write a solution in PHP:
 *
 * How your implementation works:
 * Your function will take two arguments, ($prevArray, $currArray), flattens the objects inside of prevArray and currArray to 1 level of
 * depth, and return an HTML Table in string form of the values.  The HTML table you return has a column header which is a superset of all keys in 
 * all the objects in the currArray.  Any values that have changed from the prevArray to the currArray (ie field value changed or is a 
 * new key altogether) should be bolded. In the case that the value has been removed altogether from the prevArray to the currArravy, 
 * you will write out the key in bold DELETED.
 * 
 * Rules:
 * 1. The arrays are arbitrarily deep (see common questions for explanation of arbitrarily deep).
 * 2. The currArray could have more or potentially even be in a different index order.  You cannot depend solely on array index for  
 * comparison.  However, you can assume that each object in the arrays will have an "_id" parameter.  Unless the currArray has no  
 * object with the matching "_id" parameter (for example if the whole row has changed).
 * 3. Do not create global scope.  We have a test runner that will iterate on your function and run many fixtures through it.  If you 
 * create global scope for 1 individual diff between prevArray to currArray you could cause other tests to fail.  
 *
 * Common Questions:
 * 1. Can I use outside packages to solve (e.g. Composer)?  Yes.  You can use any packages you want to solve the solution.  
 * 2. Can I use google or outside resources (e.g. StackOverflow, GitHub)?  Yes.  Act as you would in your day job.
 * 3. What does arbitrarily deep mean? The $prevArray or $currArray can have objects inside of objects at different levels of depth. 
 *    You will not know how many levels of depth the objects could have, meaning your code must handle any kind of object.  Your 
 *    solution  must account for this.  Do not assume the examples below are the only fixtures we will use to test your code. 
 * 
 * @param $prevArray is a JSON string containing an array of objects
 * @param $currArray is a JSON string containing an array of objects
 * @return a string with HTML markup in it, should return null if error occurs.
 */

// Example, Given the following data set://
//        echo arrayDiffToHtmlTable( $prevArray, $currArray);
//
//  OUTPUT (Note this is a text representation... output should be an HTML table):
//
//          _id               someKey          meta_subKey1        meta_subKey2        meta_subKey3
//            1              **HANGUP**             1234              **DELETED**
//          **2**            **RINGING**          **5678**             **207**             **52**
//
//  ** implies this field should be bold or highlighted.
//  !!! analyze the example carefully as it demonstrates expected cases that need to be handled. !!!
//
 
$prevArray = '[{"_id":1,"someKey":"RINGING","meta":{"subKey1":1234,"subKey2":52}}]';
$currArray = '[{"_id":1,"someKey":"HANGUP","meta":{"subKey1":1234}},{"_id":2,"someKey":"RINGING","meta":{"subKey1":5678,"subKey2":207,"subKey3":52}}]';
 
function arrayDiffToHtmlTable( $prevArray, $currArray) {
    // start tbale html tag.
    $htmlTableString = '<table id="diff_table"><tr>';

    //contert data to array
    $prevArray = jsonToArray( $prevArray );
    $currArray = jsonToArray( $currArray );

    
    // start table heading 
    $counter = 0;
    for($i = 0; $i < count($currArray); $i++){
    	if($i > $counter){
    		$keys = array_keys($currArray[$i]);     	

	      	for( $j=0; $j < count($keys); $j++) { 

	      		//check $keys type is array or not.
	      		if(gettype($currArray[0][$keys[$j]]) === "array"){
	            	//get array keys of $keys
	            	$subkeys = array_keys($currArray[$i][$keys[$j]]);
	            	for( $subkey_index = 0; $subkey_index < count($subkeys); $subkey_index++){
	              		$htmlTableString .= '<th>'.$keys[$j].'_'.$subkeys[$subkey_index].'</th>';
	            	}
	          	}else{
	            	$htmlTableString .= '<th>'.$keys[$j].'</th>';
	          	}
	          	$counter++;
	      	}//end loop of $keys
        }
  	}// end for loop for $currArray

  	$htmlTableString .= '</tr>';
  	// end table heading

  	// start print values 
	for( $x = 0; $x < count($currArray); $x++){
    	$htmlTableString .= '<tr>';
    	
    	// get $prevArray array keys
    	$prev_keys;
	    if(count($prevArray) > $x){
	      $prev_keys = array_keys($prevArray[$x]);
	    }else{
	      $prev_keys = "index_not_defined";
	    }

	    // get $currArray array keys
	    $curr_keys = array_keys($currArray[$x]);	 
	    
	    for ( $key_index = 0; $key_index < count($curr_keys); $key_index++ ) {

	    	//check $curr_keys type is 'array' or not
	    	if( gettype($currArray[$x][$curr_keys[$key_index]]) === "array"){

	    		if ($prev_keys !== "index_not_defined") {
	    			$psubkeys = array_keys($prevArray[$x][$curr_keys[$key_index]]);
	    			$subkeys = array_keys($currArray[$x][$curr_keys[$key_index]]);	    			
	    			for( $subkey_index = 0; $subkey_index < count($psubkeys); $subkey_index++){
	    				if( ($prevArray[$x][$prev_keys[$key_index]][$psubkeys[$subkey_index]] != '' )) {
			        		if (in_array( $psubkeys[$subkey_index], $subkeys )) {	    						
	    						$htmlTableString .=  '<td><b>'. $currArray[$x][$curr_keys[$key_index]][$psubkeys[$subkey_index]] . '</b></td>';
		    				}else{		    					
			          			$htmlTableString .=  '<td><b>DELETED</b></td>';
		    				}
			          	}else{
			            	$htmlTableString .=  '<td>'.$currArray[$x][$curr_keys[$key_index]][$psubkeys[$subkey_index]].'</td>';
			          	}
			        }
	    		}
	    		else{
	    			$subkeys = array_keys($currArray[$x][$curr_keys[$key_index]]);
	    			for( $subkey_index = 0; $subkey_index < count($subkeys); $subkey_index++){		          	
			        	if($prev_keys === "index_not_defined"){
			            	$htmlTableString .=  '<td><b>'.$currArray[$x][$curr_keys[$key_index]][$subkeys[$subkey_index]].'</b></td>';
			          	}else if(($currArray[$x][$curr_keys[$key_index]][$subkeys[$subkey_index]] != $prevArray[$x][$prev_keys[$key_index]][$subkeys[$subkey_index]]) && ($prevArray[$x][$prev_keys[$key_index]][$subkeys[$subkey_index]] === null)){
			          		$htmlTableString .=  '<td><b>'. $currArray[$x][$curr_keys[$key_index]][$subkeys[$subkey_index]] . '</b></td>';
			          	}else{
			            	$htmlTableString .=  '<td>'.$currArray[$x][$curr_keys[$key_index]][$subkeys[$subkey_index]].'</td>';
			          	}
			        }
	    		}		        
		    }else{
		        if( $prev_keys !== "index_not_defined"){
		        	$htmlTableString .= '<td><b>'. $currArray[$x][$curr_keys[$key_index]] . '</b></td>';
		        }else if( $prev_keys === "index_not_defined") {
		        	$htmlTableString .= '<td><b>'. $currArray[$x][$curr_keys[$key_index]] . '</b></td>';
		        }else{
		        	$htmlTableString .= '<td>'. $currArray[$x][$curr_keys[$key_index]] . '</td>';
		        }
		    }
	    }

    	$htmlTableString .= '</tr>';
    }

    // end table html tag
	$htmlTableString .= '</table>';


    return $htmlTableString;
}

//convert json data to array 
function jsonToArray( $jsondata ){
	$arrData = json_decode($jsondata, true);
	return $arrData;
} 

echo arrayDiffToHtmlTable( $prevArray, $currArray);

?>
