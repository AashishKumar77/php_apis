<?php
date_default_timezone_set('Asia/Kolkata');\

error_reporting('E_NOTICE ^ E_ALL');

include_once 'function.php';

$con = new DB_con();

if($_POST['action']!="")
{
$filename = $_FILES['filename']['name'];
$filedata = $_FILES['filename']['tmp_name'];


if($_POST['action']=='edit_profile')
{
   
// profile image upload for edit profile   
if($filename!="")
{
$profile_image=time().$filename;
$imgpath='../images/profile_img/'.$profile_image;
move_uploaded_file($filedata,$imgpath);
$imgname='images/profile_img/'.$profile_image;
}
else
{
$imgname = '';
}
// profile image upload for edit profile

$data=array("action"=>$_POST['action'],"image"=>$imgname,"name"=>$_POST['name'],"userid"=>$_POST['userid']);
$insert = $con->edit_profile("user",$data);
$msg = array("response" => $insert);
header('content-type: application/json');
echo json_encode($msg,true); 
}


//Single Image inserted
if($_POST['action']=='add_image')
{
// profile image upload for Add Image   
if($filename!="")
{
$profile_image=time().$filename;
if($_POST['type'] == 1)
{
$imgpath='../images/property_images/'.$profile_image;
$imgdest='../images/property_thumb_images/';
move_uploaded_file($filedata,$imgpath);
$imgname='images/property_images/'.$profile_image;
$final_image=$imgdest.$upload_img;
}else if($_POST['type'] == 2){
  $imgpath='../images/lister_images/'.$profile_image;
move_uploaded_file($filedata,$imgpath);
$imgname='images/lister_images/'.$profile_image;  
}else if($_POST['type'] == 3){
  $imgpath='../images/comment_images/'.$profile_image;
move_uploaded_file($filedata,$imgpath);
$imgname='images/comment_images/'.$profile_image;  
}else if($_POST['type'] == 4){
  $imgpath='../images/report_images/'.$profile_image;
move_uploaded_file($filedata,$imgpath);
$imgname='images/report_images/'.$profile_image;  
}

function cwUpload($field_name = '', $target_folder = '', $file_name = '', $thumb = FALSE, $thumb_folder = '', $thumb_width = '', $thumb_height = '',$random = ''){

    //folder path setup
    $target_path = $target_folder;
    $thumb_path = $thumb_folder;
    
    //file name setup
    $filename_err = explode(".",$_FILES[$field_name]['name']);
    $filename_err_count = count($filename_err);
    $file_ext = $filename_err[$filename_err_count-1];
    if($file_name != ''){
        $fileName = $random.$file_name.'.'.$file_ext;
    }else{
        $fileName = $random.$_FILES[$field_name]['name'];
    }
    
    //upload image path
    $upload_image = $target_path.basename($fileName);
    
    //upload image
    if(copy($_FILES[$field_name]['tmp_name'],$upload_image))
    {
        //thumbnail creation
        if($thumb == TRUE)
        {
            $thumbnail = $thumb_path.$fileName;
            list($width,$height) = getimagesize($upload_image);
            $thumb_create = imagecreatetruecolor($thumb_width,$thumb_height);
            switch($file_ext){
                case 'jpg':
                    $source = imagecreatefromjpeg($upload_image);
                    break;
                case 'jpeg':
                    $source = imagecreatefromjpeg($upload_image);
                    break;

                case 'png':
                    $source = imagecreatefrompng($upload_image);
                    break;
                case 'gif':
                    $source = imagecreatefromgif($upload_image);
                    break;
                default:
                    $source = imagecreatefromjpeg($upload_image);
            }

            imagecopyresized($thumb_create,$source,0,0,0,0,$thumb_width,$thumb_height,$width,$height);
            switch($file_ext){
                case 'jpg' || 'jpeg':
                    imagejpeg($thumb_create,$thumbnail,100);
                    break;
                case 'png':
                    imagepng($thumb_create,$thumbnail,100);
                    break;

                case 'gif':
                    imagegif($thumb_create,$thumbnail,100);
                    break;
                default:
                    imagejpeg($thumb_create,$thumbnail,100);
            }

        }

        return $fileName;
    }
    else
    {
        return false;
    }
}
            
} 
else
{
$imgname = '';
}
// profile image upload for Add Image

$data=array("action"=>$_POST['action'],"image"=>$imgname,"type"=>$_POST['type']);
$insert = $con->add_image("image_ids",$data);

$msg = array("response" => $insert);
header('content-type: application/json');
echo json_encode($msg,true); 
}



    
}
else
{
header("Content-type: application/json; charset=iso-8859-1");
$inputdata = file_get_contents('php://input');
$data = json_decode($inputdata, TRUE);
$action = $data['action'];
}

/***************************Edit profile web page************/
 if($action == 'edit_profile_web')
{
	$insert = $con->edit_profile_web("user",$data);
	
	 $msg = array("response" => $insert);

         header('content-type: application/json');
         echo json_encode($msg,true);
}
/**********************User Login**********************/
    if($action == 'login')
{
	$insert = $con->login("user",$data);
	
	 $msg = array("response" => $insert);

         header('content-type: application/json');
         echo json_encode($msg,true);
}
/**********************User Login************************/


/**********************User signup**********************/
    if($action == 'signup')
{
	$insert = $con->signup("user",$data);
	
	 $msg = array("response" => $insert);

         header('content-type: application/json');
         echo json_encode($msg,true);
}
/*********************User signup************************/


/**********************forget password**********************/
    if($action == 'forget_password')
{
	$insert = $con->forget_password("user",$data);
	
	 $msg = array("response" => $insert);

         header('content-type: application/json');
         echo json_encode($msg,true);
}
/*********************forget password************************/


/**********************change Password**********************/
    if($action == 'change_password')
{
	$insert = $con->change_password("user",$data);
	
	 $msg = array("response" => $insert);

         header('content-type: application/json');
         echo json_encode($msg,true);
}
/**********************change Password************************/


/**********************Fetch Countries API**********************/
    if($action == 'fetch_countries')
{
	$insert = $con->fetch_countries("country",$data);
	
	 $msg = array("response" => $insert);

         header('content-type: application/json');
         echo json_encode($msg,true);
}
/**********************Fetch Countries API************************/


/**********************Fetch Property Type API**********************/
    if($action == 'fetch_property_type')
{
	$insert = $con->fetch_property_type("property_type",$data);
	
	 $msg = array("response" => $insert);

         header('content-type: application/json');
         echo json_encode($msg,true);
}
/**********************Fetch Property Type API************************/


/**********************Add  Property **********************/
    if($action == 'add_property')
{
	$insert = $con->add_property("property_list",$data);
	
	 $msg = array("response" => $insert);

         header('content-type: application/json');
         echo json_encode($msg,true);
}
/**********************Add  Property************************/


/**********************Fetch my Property list with user id**********************/
    if($action == 'fetch_property')
{
    
   
	$insert = $con->fetch_property("property_list",$data);
	
	 $msg = array("response" => $insert);

         header('content-type: application/json');
         echo json_encode($msg,true);
}
/**********************Fetch my Property list with user id************************/


/**********************logout api starts here**********************/
    if($action == 'logout')
{
	$insert = $con->logout("user",$data);
	
	 $msg = array("response" => $insert);

         header('content-type: application/json');
         echo json_encode($msg,true);
}
/**********************logout api ends here************************/


/**********************send push notification details starts here**********************/
    if($action == 'send_push_notification')
{
	$insert = $con->send_push_notification("notification_ids",$data);
	
	 $msg = array("response" => $insert);

         header('content-type: application/json');
         echo json_encode($msg,true);
}
/**********************send push notification details ends here************************/


/**********************fetch push notification details starts here**********************/
    if($action == 'fetch_push_notification')
{
	$insert = $con->fetch_push_notification("notification_ids",$data);
	
	 $msg = array("response" => $insert);

         header('content-type: application/json');
         echo json_encode($msg,true);
}
/**********************fetch push notification details ends here************************/


/**********************change property status starts here**********************/
    if($action == 'change_property_status')
{
	$insert = $con->change_property_status("property_list",$data);
	
	 $msg = array("response" => $insert);

         header('content-type: application/json');
         echo json_encode($msg,true);
}
/**********************change property status ends here************************/


/**********************terms and condition api starts here**********************/
    if($action == 'term_condition')
{
	$insert = $con->term_condition("terms_condition",$data);
	
	 $msg = array("response" => $insert);

         header('content-type: application/json');
         echo json_encode($msg,true);
}
/**********************terms and condition api starts here************************/


/**********************privacy policy api starts here**********************/
    if($action == 'privacy_policy')
{
	$insert = $con->privacy_policy("privacy_policy",$data);
	
	 $msg = array("response" => $insert);

         header('content-type: application/json');
         echo json_encode($msg,true);
}
/**********************privacy policy api starts here************************/


/**********************Edit  Property with user id And Property id **********************/
    if($action == 'edit_property')
{
	$insert = $con->edit_property("property_list",$data);
	
	 $msg = array("response" => $insert);

         header('content-type: application/json');
         echo json_encode($msg,true);
}
/**********************Edit  Property with user id And Property id ************************/


/**********************Delete  image **********************/
    if($action == 'delete_image')
{
	$insert = $con->delete_image("image_ids",$data);
	
	 $msg = array("response" => $insert);

         header('content-type: application/json');
         echo json_encode($msg,true);
}
/**********************Delete  image************************/


/**********************Delete  Property By Property id **********************/
    if($action == 'delete_property')
{
	$insert = $con->delete_property("property_list",$data);
	
	 $msg = array("response" => $insert);

         header('content-type: application/json');
         echo json_encode($msg,true);
}
/**********************Delete  Property By Property id************************/


/**********************Start Select particular Property By Property id **********************/
    if($action == 'select_property')
{
	$insert = $con->select_property("property_list",$data);
	
	 $msg = array("response" => $insert);

         header('content-type: application/json');
         echo json_encode($msg,true);
}
/**********************Start Random particular Property By Property id **********************/
    if($action == 'nearby_property')
{
	$insert = $con->select_nearby_property("property_list",$data);
	
	 $msg = array("response" => $insert);

         header('content-type: application/json');
         echo json_encode($msg,true);
}
/**********************End Select particular Property By Property id************************/


/**********************Blog List **********************/
    if($action == 'fetch_blog')
{
	$insert = $con->fetch_blog("tbl_blog",$data);
	
	 $msg = array("response" => $insert);

         header('content-type: application/json');
         echo json_encode($msg,true);
}
/**********************End Blog List************************/

/**********************Multiple Property list **********************/
    if($action == 'multiple_properties')
{
	$insert = $con->multiple_properties("property_list",$data);
	
	 $msg = array("response" => $insert);

         header('content-type: application/json');
         echo json_encode($msg,true);
}
/**********************Multiple Property list************************/

/*********************Fetch languages list**********************/
    if($action == 'language')
{
   
	$insert = $con->language("languages",$data);
	
	 $msg = array("response" => $insert);

         header('content-type: application/json');
         echo json_encode($msg,true);
}
/**********************Fetch languages************************/
/*********************Fetch currency **********************/
    if($action == 'currency')
{
   
	$insert = $con->currency("currency",$data);
	
	 $msg = array("response" => $insert);

         header('content-type: application/json');
         echo json_encode($msg,true);
}
/**********************Fetch languages************************/
/*********************Fetch currency **********************/
    if($action == 'latitude')
{
   
	$insert = $con->latitude("property_list",$data);
	
	 $msg = array("response" => $insert);

         header('content-type: application/json');
         echo json_encode($msg,true);
}
/**********************Fetch languages************************/


/*********************suggestion api **********************/
    if($action == 'suggest')
{
   
	$insert = $con->suggest("suggestions",$data);
	
	 $msg = array("response" => $insert);

         header('content-type: application/json');
         echo json_encode($msg,true);
}
/**********************suggestion api************************/
/*********************List Expire api **********************/
    if($action == 'expire')
{
   
	$insert = $con->expire("property_list",$data);
	
	 $msg = array("response" => $insert);

         header('content-type: application/json');
         echo json_encode($msg,true);
}
/**********************List Expire api************************/

/*********************search lister **********************/
    if($action == 'search_lister')
{
   
	$insert = $con->search_lister("user",$data);
	
	 $msg = array("response" => $insert);

         header('content-type: application/json');
         echo json_encode($msg,true);
}

/**********************search lister************************/
/*********************report api **********************/
    if($action == 'report')
{
   
	$insert = $con->report("report",$data);
	
	 $msg = array("response" => $insert);

         header('content-type: application/json');
         echo json_encode($msg,true);
}

/**********************report api************************/
/*********************fetch full profile api **********************/
    if($action == 'fetch_profile')
{
   
	$insert = $con->fetch_profile("user",$data);
	
	 $msg = array("response" => $insert);

         header('content-type: application/json');
         echo json_encode($msg,true);
}

/**********************fetch full profile api************************/
/*********************rating api **********************/
    if($action == 'rating_lister')
{
   
	$insert = $con->rating_lister("rating_lister",$data);
	
	 $msg = array("response" => $insert);

         header('content-type: application/json');
         echo json_encode($msg,true);
}

/**********************rating  api************************/
/*********************comment api **********************/
    if($action == 'comment')
{
   
	$insert = $con->comment("comment",$data);
	
	 $msg = array("response" => $insert);

         header('content-type: application/json');
         echo json_encode($msg,true);
}

/**********************comment api************************/
/*********************Draft list api **********************/
    if($action == 'draft')
{
   
	$insert = $con->draft("property_list",$data);
	
	 $msg = array("response" => $insert);

         header('content-type: application/json');
         echo json_encode($msg,true);
}

/**********************Draft list api***********************/
/*********************current listing api **********************/
    if($action == 'current_list')
{
   
	$insert = $con->current_list("property_list",$data);
	
	 $msg = array("response" => $insert);

         header('content-type: application/json');
         echo json_encode($msg,true);
}

/**********************current listing api***********************/
/*********************Request lister api **********************/
    if($action == 'request_lister')
{
	$insert = $con->request_lister("request_lister",$data);
	 $msg = array("response" => $insert);
       header('content-type: application/json');
         echo json_encode($msg,true);
}

/**********************Request lister api***********************/
/*********************Request lister api **********************/
    if($action == 'recent_properties')
{
	$insert = $con->recent_properties("property_list",$data);
	 $msg = array("response" => $insert);
       header('content-type: application/json');
         echo json_encode($msg,true);
}

/**********************Request lister api***********************/



/**********************Fetch reviews**********************/
    if($action == 'fetch_reviews')
{
	$insert = $con->fetch_reviews("rating_lister",$data);
	
	 $msg = array("response" => $insert);

         header('content-type: application/json');
         echo json_encode($msg,true);
}
/**********************Fetch reviews************************/


/**********************Fetch Single Reviews Comment list**********************/
    if($action == 'fetch_single_review_comments')
{
	$insert = $con->fetch_single_review_comments("rating_lister",$data);
	
	 $msg = array("response" => $insert);

         header('content-type: application/json');
         echo json_encode($msg,true);
}
/**********************Fetch Single Reviews Comment list************************/


/**********************Fetch comment**********************/
    if($action == 'fetch_comment')
{
	$insert = $con->fetch_comment("rating_lister",$data);
	
	 $msg = array("response" => $insert);

         header('content-type: application/json');
         echo json_encode($msg,true);
}
/**********************Fetch comment************************/

/**********************convert api**********************/
    if($action == 'convert')
{
	$insert = $con->convert("currency",$data);
	
	 $msg = array("response" => $insert);

         header('content-type: application/json');
         echo json_encode($msg,true);
}
/**********************convert api************************/
/**********************convert api**********************/
    if($action == 'send_currency')
{
	$insert = $con->send_currency("notification_ids",$data);
	
	 $msg = array("response" => $insert);

         header('content-type: application/json');
         echo json_encode($msg,true);
}
/**********************convert api************************/
/**********************fetch currency api**********************/
    if($action == 'fetch_currency')
{
	$insert = $con->fetch_currency("notification_ids",$data);
	
	 $msg = array("response" => $insert);

         header('content-type: application/json');
         echo json_encode($msg,true);
}
/**********************fetch currency api************************/
?>