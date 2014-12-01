<?php

//check password
if ($_POST["password"] == "123test"){

	if(isset($_FILES['pictures'])) {
    		$errors = array();
    
    		//set max size in bits, 1 mb = 1048576 bits
    		$maxsize = 1048576;
    
    		//set acceptable file types
    		$acceptable = array(
    		    'application/pdf',
    		    'image/jpeg',
    		    'image/jpg',
    		    'image/gif',
    		    'image/png'
    		);

		foreach ($_FILES["pictures"]["error"] as $key => $error) {
		
    			if(($_FILES['pictures']['size'][$key] >= $maxsize)) {
        			$errors[] = $_FILES['pictures']['name'][$key].' is too large. File must be less than 1 mb.';
    			}

    			if(!in_array($_FILES['pictures']['type'][$key], $acceptable) && (!empty($_FILES["pictures"]["type"][$key]))) {
    		    		$errors[] = $_FILES['pictures']['name'][$key].' is an invalid file type. Only PDF, JPG, GIF and PNG types are accepted.';
    			}
    
		}
	
		//continue with upload if no errors
    		if(count($errors) === 0) {
       
    			echo "<hr>";
						
			foreach ($_FILES["pictures"]["error"] as $key => $error) {
    
    			//check if error with file to be uploaded
    			if ($error == UPLOAD_ERR_OK) {        

				//get uploaded file info
				$tmp_name = $_FILES["pictures"]["tmp_name"][$key];
				$name = $_FILES["pictures"]["name"][$key];
		
				//upload file to local server directory "data", prepend with "simple-"
				move_uploaded_file($tmp_name, "data/simple-$name");
					
				//set uploaded file as post variable for Metadisk
				$file_name_with_full_path = "data/simple-$name";       
				$post = array('file'=>'@'.$file_name_with_full_path);
			
				//upload file from local server to Metadisk
				$target_url = 'http://node1.metadisk.org/api/upload'; 
		    		$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL,$target_url);
				curl_setopt($ch, CURLOPT_POST,1);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
		    		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
				$result=curl_exec ($ch);
				curl_close ($ch);

				//delete file from local server	
				unlink(realpath("data/simple-$name"));
    
		    		//get filehash and key from Metadisk
		    		$resultarray = json_decode($result, true);
		    
		    		//display results
		    		echo "<p><b>Filename:</b> ".$name."</p>";
		    
		    		echo "<p><b>Filehash:</b> ".$resultarray['filehash']."</p>";
		    
		    		echo "<p><b>Key:</b> ".$resultarray['key']."</p>";
    
    				echo "<p><b>Link:</b> <a href='http://node1.metadisk.org/api/download/".$resultarray['filehash']."?key=".$resultarray['key']."'>http://node1.metadisk.org/api/download/".$resultarray['filehash']."?key=".$resultarray['key']."</a></p>";
            
            			echo "<hr>";
            
				} else {
    		
    				echo "upload error";
    		
    				}
    	
			}
			
    		} else {
    			
        		foreach($errors as $error) {
        			echo '<p>'.$error.'</p>';
        		}

     			echo "<p><a href='index.html'>Go back</a></p>";
        
        		die(); //Ensure no more processing is done
        		
    		}
	}
	
} else {
	
	echo "wrong password";
	
}

?>
