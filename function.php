<?php
date_default_timezone_set('Asia/Kolkata');
include_once 'config.php';
include_once 'response.php';
$date = date('Y-m-d h:i:s');

class DB_con {

    protected $conn;

    function __construct() {
        $this->conn = mysqli_connect(DB_SERVER, DB_USER, DB_PASS, DB_NAME) or die(mysqli_connection_error());
    }

    /*  MM   *********************login API starts here**********************/

    public function login($ssTableName, $asFields) {
        if (strcmp($asFields['action'], "login") == 0) {
            $lang = (isset($asFields['language']) && $asFields['language'] != '') ? $asFields['language']:DEFAULT_LANG;
            $query_select = mysqli_query($this->conn, "Select id FROM $ssTableName where phoneno='" . $asFields['mobile'] . "' and password='" . md5($asFields['password']) . "' ");
        //   echo "Select id FROM $ssTableName where phoneno='" . $asFields['mobile'] . "' and password='" . md5($asFields['password']) . "' ";
            $matchFound = mysqli_num_rows($query_select) > 0 ? 'yes' : 'no';
            if ((strcmp($matchFound, 'yes') == 0)) {
                $update_query = mysqli_query($this->conn,"UPDATE $ssTableName SET $query_str last_login_from='" .$asFields['last_login'] . "',last_login_at='" . DATE . "', language='".$asFields['language']."' where phoneno = '" . $asFields['mobile'] . "' ");
                
                $query_fetch = mysqli_query($this->conn, "Select id,name,country_code,mobile,platform,last_login_from,last_login_at,user_type,status,IF(profile_img='',null, CONCAT('".DB_URL."',profile_img)) AS profile_img FROM $ssTableName where phoneno='" . $asFields['mobile'] . "' and password='" . md5($asFields['password']) . "' ");
                $row = mysqli_fetch_assoc($query_fetch);
                
            if($asFields['platform'] != '0') {   
            $query_fetch_notify = mysqli_query($this->conn, "Select * FROM notification_ids WHERE device_id='" . $asFields['device_id'] . "' ");
            if (mysqli_num_rows($query_fetch_notify) > 0) {
                $Query_update = mysqli_query($this->conn, "UPDATE notification_ids SET `user_id`='" . $row['id'] . "', notification_id='" . $asFields['notification_id'] . "', `platform`='" . $asFields['platform'] . "' WHERE device_id='" . $asFields['device_id'] . "' ");
            } else {
                $Query_insert = mysqli_query($this->conn, "INSERT INTO notification_ids (`device_id`, `notification_id`, `user_id`, `status`, `platform`) VALUES ('" . $asFields['device_id'] . "', '" . $asFields['notification_id'] . "', '" . $row['id'] . "', '1', '" . $asFields['platform'] . "') ");
            } }
                
                if($row['status'] == '1'){
                    if($row['platform'] == '0'){
                        $row['platform'] = 'Web';
                    } else if($row['platform'] == '1'){
                        $row['platform'] = 'Android';
                    } else {
                        $row['platform'] = 'Ios';
                    }
                    $response = LOGIN_SUCCESS;
                    $msg = array('result' => '201', 'msg' => $response[$lang], 'status' => '1', 'data' =>  $row);  // success
                    return $msg;
                } else {
                    $response = LOGIN_STATUS_0;
                    $msg = array('result' => '204', 'msg' => $response[$lang], 'status' => '0');  // failure
                    return $msg;
                }
            } else {
                    $response = LOGIN_FAILURE;
                    $msg = array('result' => '204', 'msg' => $response[$lang]);  // failure
                    return $msg;
            }
        } else {
            $msg = array('result' => '207');  // wrong action
            return $msg;
        }
    }

    /*     *********************login API starts here**********************/
    
    
    /*     *********************Signup API starts here**********************/

    public function signup($ssTableName, $asFields) {
        if (strcmp($asFields['action'], "signup") == 0) {
            $lang = (isset($asFields['language']) && $asFields['language'] != '') ? $asFields['language']:DEFAULT_LANG;
            $query_fetch = mysqli_query($this->conn, "Select * FROM $ssTableName where mobile='" . $asFields['mobile'] . "'");
            $matchFound = mysqli_num_rows($query_fetch) > 0 ? 'yes' : 'no';
            if ((strcmp($matchFound, 'no') == 0)) {
                $row = mysqli_fetch_assoc($query_fetch);
                    if(isset($asFields['language']) && $asFields['language'] != '' ){
                        $language = $asFields['language'];
                    } else {
                        $language = $lang;
                    }
                    $Query_insert = mysqli_query($this->conn, "INSERT INTO $ssTableName (`name`, `country_code`, `mobile`, `phoneno`, `password`, `device_id`, `notification_id`, `platform`, `last_login_from`, `last_login_at`, `language`, `user_type`, `status`, `created_at`) 
                    VALUES ('" . $asFields['name'] . "', '" . $asFields['country_code'] . "', '" . $asFields['mobile'] . "', '" . $asFields['phoneno'] . "', '" . md5($asFields['password']) . "', '" . $asFields['device_id'] . "', '" . $asFields['notification_id'] . "', '" . $asFields['platform'] . "', '" . $asFields['platform'] . "', '".DATE."', '" . $language . "', '" . $asFields['user_type'] . "', '1', '".DATE."') ");
                   $insert_id = mysqli_insert_id($this->conn);   
                
                
            if($asFields['platform'] != '0') {   
            $query_fetch_notify = mysqli_query($this->conn, "Select * FROM notification_ids WHERE device_id='" . $asFields['device_id'] . "' ");
            if (mysqli_num_rows($query_fetch_notify) > 0) {
                $Query_update = mysqli_query($this->conn, "UPDATE notification_ids SET `user_id`='" . $insert_id . "', notification_id='" . $asFields['notification_id'] . "', `platform`='" . $asFields['platform'] . "' WHERE device_id='" . $asFields['device_id'] . "' ");
            } else {
                $Query_insert = mysqli_query($this->conn, "INSERT INTO notification_ids (`device_id`, `notification_id`, `user_id`, `status`, `platform`) VALUES ('" . $asFields['device_id'] . "', '" . $asFields['notification_id'] . "', '" . $insert_id . "', '1', '" . $asFields['platform'] . "') ");
            } }
                
                $query_select = mysqli_query($this->conn, "Select id,name,country_code,mobile,platform,last_login_from,last_login_at,user_type,IF(profile_img='',null, CONCAT('".DB_URL."',profile_img)) AS profile_img FROM $ssTableName where id='" . $insert_id . "' ");
                $fetch = mysqli_fetch_assoc($query_select);
                
                    $response = SINGUP_SUCCESS;
                    
                    $msg = array('result' => '201', 'msg' => $response[$lang], 'data' => $fetch);  // success
                    return $msg;
            } else {
                    $response = MOBILE_EXIST;
                    $msg = array('result' => '204', 'msg' => $response[$lang]);  // failure
                    return $msg;
            }
        } else {
            $msg = array('result' => '207');  // wrong action
            return $msg;
        }
    }   

    /*     *********************Signup API ends here**********************/



    /*     *********************Forget Password**********************/

    public function forget_password($ssTableName, $asFields) {
        if (strcmp($asFields['action'], "forget_password") == 0) {
            $lang = (isset($asFields['language']) && $asFields['language'] != '') ? $asFields['language']:DEFAULT_LANG;
            $query_select = mysqli_query($this->conn, "Select * from $ssTableName where phoneno = '" . $asFields['mobile'] . "' ");
            $matchFound = mysqli_num_rows($query_select) > 0 ? 'yes' : 'no';
            if ((strcmp($matchFound, 'yes') == 0)) {
                $row = mysqli_fetch_assoc($query_select);
                $reset_password = mysqli_query($this->conn,"UPDATE $ssTableName SET password='" . md5($asFields['password']) . "' where phoneno = '" . $asFields['mobile'] . "' ");
                
                $response = PASSWORD_UPDATE;
                $msg = array('result' => '201', 'msg' => $response[$lang]); // success
                return $msg;
            } else {
                $response = MOBILE_NOT_EXIST;
                $msg = array('result' => '204', 'msg' => $response[$lang]); //failure
                return $msg;
            }
        } else {

            $msg = array('result' => '207'); // wrong action
            return $msg;
        }
    }

    /*     * ********************Forget Password********************* */



    /*     * ********************change Password********************* */

    public function change_password($ssTableName, $asFields) {
        if (strcmp($asFields['action'], "change_password") == 0) {
            $lang = (isset($asFields['language']) && $asFields['language'] != '') ? $asFields['language']:DEFAULT_LANG;
            $query_select = mysqli_query($this->conn, "Select * from $ssTableName where id = '" . $asFields['userid'] . "' ");
            $matchFound = mysqli_num_rows($query_select) > 0 ? 'yes' : 'no';
            if ((strcmp($matchFound, 'yes') == 0)) {
                $row = mysqli_fetch_assoc($query_select);
                if ($row['password'] == md5($asFields['oldpassword'])) {
                $query_success = mysqli_query($this->conn, "UPDATE $ssTableName SET password='" . md5($asFields['newpassword']) . "' WHERE id = '" . $asFields['userid'] . "' and password = '" . md5($asFields['oldpassword']) . "' ");
                
                    $response = PASSWORD_UPDATE;
                    $msg = array('result' => '201', 'msg' => $response[$lang]);
                    return $msg;
                    
                } else {
                    $response = PASSWORD_INCORRECT;
                    $msg = array('result' => '204', 'msg' => $response[$lang]);
                    return $msg;
                }
            } else {
                $response = INVALID_DETAILS;
                $msg = array('result' => '204', 'msg' => $response[$lang]);
                return $msg;
            }
        } else {

            $msg = array('result' => '207');
            return $msg;
        }
    }

    /*     * ********************change Password********************* */



    /*     *********************Fetch Countries API**********************/

    public function fetch_countries($ssTableName, $asFields) {
        if (strcmp($asFields['action'], "fetch_countries") == 0) {
            $query_fetch = mysqli_query($this->conn, "Select * FROM $ssTableName where status='1' ");
            $matchFound = mysqli_num_rows($query_fetch) > 0 ? 'yes' : 'no';
            if ((strcmp($matchFound, 'yes') == 0)) {
                while ($row = mysqli_fetch_assoc($query_fetch)) {
                    $fetch[] = $row;
                }
                $msg = array('result' => '201'); // success
                $countries = array('countries' => $fetch);
                $latest = array_merge($msg, $countries);
                return $latest;
            } else {
                
                $msg = array('result' => '204');  // failure
                return $msg;
            }
        } else {
            $msg = array('result' => '207');  // wrong action
            return $msg;
        }
    }

    /*     *********************Fetch Countries API************************/



    /*     *********************Fetch Property Type API**********************/

    public function fetch_property_type($ssTableName, $asFields) {
            $lang = (isset($asFields['language']) && $asFields['language'] != '') ? $asFields['language']:DEFAULT_LANG;
             mysqli_query($this->conn,"SET NAMES utf8");
            if (strcmp($asFields['action'], "fetch_property_type") == 0) {
            $query_fetchc = mysqli_query($this->conn, "Select * FROM country where id='".$lang."' ");
            $rec = mysqli_fetch_assoc($query_fetchc);
            $query_fetch = mysqli_query($this->conn, "Select id,type,status,extra_fields,".$rec['country']." as translation FROM $ssTableName where status='1' ");
            $matchFound = mysqli_num_rows($query_fetch) > 0 ? 'yes' : 'no';
            if ((strcmp($matchFound, 'yes') == 0)) {
                while ($row = mysqli_fetch_assoc($query_fetch)) {
                    $fetch[] = $row;
                }
                $response = PROPERTY_TYPE;
                $msg = array('result' => '201','msg'=>$response[$lang]); // success
                $properties = array('properties' => $fetch);
                $latest = array_merge($msg, $properties);
                return $latest;
            } else {
                $msg = array('result' => '204');  // failure
                return $msg;
            }
        } else {
            $msg = array('result' => '207');  // wrong action
            return $msg;
        }
    }

    /*     *********************Fetch Property Type API************************/
    

    /*     * ********************update profile details API starts here********************* */

    public function edit_profile($ssTableName, $asFields) {
        
        $lang = (isset($asFields['language']) && $asFields['language'] != '') ? $asFields['language']:DEFAULT_LANG;
        if (strcmp($asFields['action'], "edit_profile") == 0) {
           
            $query_fetch = mysqli_query($this->conn, "Select * FROM $ssTableName where id='" . $asFields['userid'] . "' ");
            $matchFound = mysqli_num_rows($query_fetch) > 0 ? 'yes' : 'no';
            if ((strcmp($matchFound, 'yes') == 0)) {
                $row = mysqli_fetch_assoc($query_fetch);
                $profile = $row['profile_img'];
                if($asFields['image'] != ''){
                    $asFields['image'];
                    $unlink_profile = unlink("../" . $profile);
                } else {
                    $asFields['image'] = $profile;
                }
                
                $query_update = mysqli_query($this->conn, "UPDATE $ssTableName SET profile_img='" . $asFields['image'] . "', name='" . $asFields['name'] . "' where id='" . $asFields['userid'] . "' ");
                $query_display = mysqli_query($this->conn, "Select id,name,country_code,mobile,platform,user_type,phoneno,CONCAT('".DB_URL."',profile_img) AS profile_img FROM $ssTableName where id='" . $asFields['userid'] . "' ");
                $rec = mysqli_fetch_assoc($query_display);
                $response = EDIT_PROFILE;
                $msg = array('result' => '201','msg' => $response[$lang], 'data' => $rec); // successfull
                $detail = array('details' => $rec); // successfull
                $latest = array_merge($msg, $detail);
                return $msg;
             
            } else {
                $response = USER_NOT_EXIST;
                $msg = array('result' => '204','msg'=> $response[$lang]);  // failure
                return $msg;
            }
        } else {
            $msg = array('result' => '207','msg'=>'Action Is Required' );  // wrong action
            return $msg;
        }
    }
    public function edit_profile_web($ssTableName, $asFields) {
        $lang = (isset($asFields['language']) && $asFields['language'] != '') ? $asFields['language']:DEFAULT_LANG;
        if (strcmp($asFields['action'], "edit_profile_web") == 0) {
            $query_fetch = mysqli_query($this->conn, "Select * FROM $ssTableName where id='" . $asFields['userid'] . "' ");
            $matchFound = mysqli_num_rows($query_fetch) > 0 ? 'yes' : 'no';
            if ((strcmp($matchFound, 'yes') == 0)) {
                $row = mysqli_fetch_assoc($query_fetch);
                $profile = $row['profile_img'];
                if($asFields['image'] != ''){
                    $asFields['image'];
                    $unlink_profile = unlink("../" . $profile);
                } else {
                    $asFields['image'] = $profile;
                }
                
                $query_update = mysqli_query($this->conn, "UPDATE $ssTableName SET profile_img='" . $asFields['image'] . "', name='" . $asFields['name'] . "' where id='" . $asFields['userid'] . "' ");
                $query_display = mysqli_query($this->conn, "Select id,name,country_code,mobile,platform,user_type,phoneno,CONCAT('".DB_URL."',profile_img) AS profile_img FROM $ssTableName where id='" . $asFields['userid'] . "' ");
                $rec = mysqli_fetch_assoc($query_display);
                $response = EDIT_PROFILE;
                $msg = array('result' => '201','msg' => $response[$lang], 'data' => $rec); // successfull
                $detail = array('details' => $rec); // successfull
                $latest = array_merge($msg, $detail);
                return $msg;
             
            } else {
                $msg = array('result' => '204');  // failure
                return $msg;
            }
        } else {
            $msg = array('result' => '207');  // wrong action
            return $msg;
        }
    }

    /*     * ********************update profile details API Ends here*********************** * MM/


    /*     * ********************delete image api starts here*********************** */

    public function delete_image($ssTableName, $asFields) {
        $lang = (isset($asFields['language']) && $asFields['language'] != '') ? $asFields['language']:DEFAULT_LANG;
        if (strcmp($asFields['action'], "delete_image") == 0) {
            $query_fetch = mysqli_query($this->conn, "Select * FROM $ssTableName WHERE id='" . $asFields['image_id'] . "' ");
            $matchFound = mysqli_num_rows($query_fetch) > 0 ? 'yes' : 'no';
            if ((strcmp($matchFound, 'yes') == 0)) {
                $row = mysqli_fetch_assoc($query_fetch);
                $profile = $row['image'];
                $unlink_profile = unlink("../" . $profile);
                $Query_update = mysqli_query($this->conn, "DELETE FROM $ssTableName WHERE id='" . $asFields['image_id'] . "' ");
                $response = DELETE_IMAGE;
                $msg = array('result' => '201', 'msg' => $response[$lang]);  // success
                return $msg;
            } else {
                $response = IMAGE_NOT_EXIST;
                $msg = array('result' => '204', 'msg'=>$response[$lang]);  // failure
                return $msg;
            }
        } else {
            $msg = array('result' => '207','msg'=>'Action is Required');  // wrong action
            return $msg;
        }
    }

    /*     * ********************delete image API Ends here*********************** MM*/


    /*     * ********************log out api starts here*********************** */

    public function logout($ssTableName, $asFields) {
        $lang = (isset($asFields['language']) && $asFields['language'] != '') ? $asFields['language']:DEFAULT_LANG;
        if (strcmp($asFields['action'], "logout") == 0) {
            $query_fetch = mysqli_query($this->conn, "Select * FROM $ssTableName WHERE id='" . $asFields['userid'] . "' ");
            $matchFound = mysqli_num_rows($query_fetch) > 0 ? 'yes' : 'no';
            if ((strcmp($matchFound, 'yes') == 0)) {

                $Query_update = mysqli_query($this->conn, "UPDATE $ssTableName SET `last_logout`='".DATE."' WHERE `id`='" . $asFields['userid'] . "' ");
                $Query_update_notify = mysqli_query($this->conn, "UPDATE notification_ids SET `user_id`='' WHERE device_id='" . $asFields['device_id'] . "' ");
            
                $response = LOGOUT;
                $msg = array('result' => '201', 'msg' => $response[$lang]);  // success
                return $msg;
            } else {
                $msg = array('result' => '204', 'msg' => 'no user exist');  // success
                return $msg;
            }
        } else {
            $msg = array('result' => '207');  // wrong action
            return $msg;
        }
    }

    /*     * ********************log out api API Ends here*********************** MM*/


    /*     * ********************change property status api starts here*********************** */

    public function change_property_status($ssTableName, $asFields) {
     $lang = (isset($asFields['language']) && $asFields['language'] != '') ? $asFields['language']:DEFAULT_LANG;
        if (strcmp($asFields['action'], "change_property_status") == 0) {
            $query_fetch = mysqli_query($this->conn, "Select * FROM $ssTableName WHERE id='" . $asFields['property_id'] . "' and user_id='" . $asFields['userid'] . "' ");
           //echo "Select * FROM $ssTableName WHERE id='" . $asFields['property_id'] . "' and user_id='" . $asFields['userid'] . "' ";
            $matchFound = mysqli_num_rows($query_fetch) > 0 ? 'yes' : 'no';
            if ((strcmp($matchFound, 'yes') == 0)) {
                $status = $asFields['status'];
                $Query_update = mysqli_query($this->conn, "UPDATE $ssTableName SET `status`='".$asFields['status']."' WHERE `id`='" . $asFields['property_id'] . "' ");
                $response = STATUS_CHANGED;
                $msg = array('result' => '201', 'msg' => $response[$lang], 'status' => $status);  // success
                return $msg;
            } else {
                $response = PROPERTY_NOT_EXIST;
                $msg = array('result' => '204', 'msg' => $response[$lang]);  // faliure
                return $msg;
            }
        } else {
            $msg = array('result' => '207','msg'=>'Action is Required');  // wrong action
            return $msg;
        }
    }

    /*     * ********************change property status api API Ends here*********************** MM*/


    /*     * ********************terms and condition api starts here*********************** */

    public function term_condition($ssTableName, $asFields) {
        if (strcmp($asFields['action'], "term_condition") == 0) {
            $lang = (isset($asFields['language']) && $asFields['language'] != '') ? $asFields['language']:DEFAULT_LANG;
            $query_fetch = mysqli_query($this->conn, "Select * FROM country WHERE id='".$lang."'");
            $matchFound = mysqli_num_rows($query_fetch) > 0 ? 'yes' : 'no';
            if ((strcmp($matchFound, 'yes') == 0)) {
                $rec = mysqli_fetch_assoc($query_fetch);
                $lang = $rec['language'];
                $query_select = mysqli_query($this->conn, "Select ".$rec['language']." FROM $ssTableName WHERE id='1' ");
                $row = mysqli_fetch_assoc($query_select);
                $terms = $row[$lang];
                $msg = array('result' => '201', 'msg' => 'Terms found', 'terms' => $terms);  // success
                return $msg;
            } else {
                $msg = array('result' => '204', 'msg' => 'Language id not found');  // success
                return $msg;
            }
        } else {
            $msg = array('result' => '207');  // wrong action
            return $msg;
        }
    }

    /*     * ********************terms and condition api API Ends here*********************** MM*/


    /*     * ********************terms and condition api starts here*********************** */

    public function privacy_policy($ssTableName, $asFields) {
        if (strcmp($asFields['action'], "privacy_policy") == 0) {
            $lang = (isset($asFields['language']) && $asFields['language'] != '') ? $asFields['language']:DEFAULT_LANG;
            $query_fetch = mysqli_query($this->conn, "Select * FROM country WHERE id='".$lang."'");
            $matchFound = mysqli_num_rows($query_fetch) > 0 ? 'yes' : 'no';
            if ((strcmp($matchFound, 'yes') == 0)) {
                $rec = mysqli_fetch_assoc($query_fetch);
                $lang = $rec['language'];
                $query_select = mysqli_query($this->conn, "Select ".$rec['language']." FROM $ssTableName WHERE id='1' ");
                $row = mysqli_fetch_assoc($query_select);
                $policy = $row[$lang];
                $msg = array('result' => '201', 'msg' => 'policy found', 'policy' => $policy);  // success
                return $msg;
            } else {
                $msg = array('result' => '204', 'msg' => 'Language id not found');  // success
                return $msg;
            }
        } else {
            $msg = array('result' => '207');  // wrong action
            return $msg;
        }
    }

    /*     * ********************terms and condition api API Ends here*********************** MM*/
    
    
    /*     * ********************send push notification details api start here*********************** */

    public function send_push_notification($ssTableName, $asFields) {
     $lang = (isset($asFields['language']) && $asFields['language'] != '') ? $asFields['language']:DEFAULT_LANG;
        if (strcmp($asFields['action'], "send_push_notification") == 0) {
            $query_fetch = mysqli_query($this->conn, "Select * FROM $ssTableName WHERE device_id='" . $asFields['device_id'] . "' ");
            $matchFound = mysqli_num_rows($query_fetch) > 0 ? 'yes' : 'no';
            if ((strcmp($matchFound, 'yes') == 0)) {

                $Query_update = mysqli_query($this->conn, "UPDATE $ssTableName SET `user_id`='" . $asFields['userid'] . "', `status`='" . $asFields['status'] . "',notification_id='" . $asFields['notification_id'] . "', `platform`='" . $asFields['platform'] . "' WHERE device_id='" . $asFields['device_id'] . "' ");
                $query_fetch = mysqli_query($this->conn, "Select * FROM $ssTableName WHERE device_id='" . $asFields['device_id'] . "' ");
                $row = mysqli_fetch_assoc($query_fetch);
                $response = NOTIFICATION_UPDATE;
                $msg = array('result' => '201', 'msg' =>$response[$lang], 'data' => $row);  // success
                return $msg;
            } else {

                $Query_insert = mysqli_query($this->conn, "INSERT INTO $ssTableName (`device_id`, `notification_id`, `user_id`, `status`, `platform`) VALUES ('" . $asFields['device_id'] . "', '" . $asFields['notification_id'] . "', '" . $asFields['userid'] . "', '" . $asFields['status'] . "', '" . $asFields['platform'] . "') ");
                $query_fetch = mysqli_query($this->conn, "Select * FROM $ssTableName WHERE device_id='" . $asFields['device_id'] . "' ");
                $row = mysqli_fetch_assoc($query_fetch);
                 $response = NOTIFICATION_INSERT;
                $msg = array('result' => '201', 'msg' =>$response[$lang], 'data' => $row);  // success
                return $msg;
            }
        } else {
            $msg = array('result' => '207');  // wrong action
            return $msg;
        }
    }

    /*     * ********************send push notification details api ends here*********************** MM*/
    
    public function send_currency($ssTableName, $asFields) {
     $lang = (isset($asFields['language']) && $asFields['language'] != '') ? $asFields['language']:DEFAULT_LANG;
        if (strcmp($asFields['action'], "send_currency") == 0) {
            $query_fetch = mysqli_query($this->conn, "Select * FROM currency WHERE name='" . $asFields['currency'] . "' ");
            
            $matchFound = mysqli_num_rows($query_fetch) > 0 ? 'yes' : 'no';
            if ((strcmp($matchFound, 'yes') == 0)) {

                $Query_update = mysqli_query($this->conn, "UPDATE $ssTableName SET `currency`='" . $asFields['currency'] . "'  WHERE device_id='" . $asFields['device_id'] . "' ");
                
                $query_fetch = mysqli_query($this->conn, "Select * FROM $ssTableName WHERE device_id='" . $asFields['device_id'] . "' ");
                $row = mysqli_fetch_assoc($query_fetch);
                $response = CURRENCY_UPDATE;
                $msg = array('result' => '201', 'msg' =>$response[$lang], 'data' => $row);  // success
                return $msg;
            } else {

                $Query_insert = mysqli_query($this->conn, "INSERT INTO $ssTableName (`device_id`, `currency`) VALUES ('" . $asFields['device_id'] . "', '" . $asFields['currency'] . "') ");
                echo "INSERT INTO $ssTableName (`device_id`, `currency`) VALUES ('" . $asFields['device_id'] . "', '" . $asFields['currency'] . "') ";
                $query_fetch = mysqli_query($this->conn, "Select * FROM $ssTableName WHERE device_id='" . $asFields['device_id'] . "' ");
                $row = mysqli_fetch_assoc($query_fetch);
                 $response = CURRENCY_INSERT;
                $msg = array('result' => '201', 'msg' =>$response[$lang], 'data' => $row);  // success
                return $msg;
            }
        } else {
            $msg = array('result' => '207');  // wrong action
            return $msg;
        }
    }
    
    /*     * ********************fetch push notification details api start here*********************** */

    public function fetch_push_notification($ssTableName, $asFields) {
                    $lang = (isset($asFields['language']) && $asFields['language'] != '') ? $asFields['language']:DEFAULT_LANG;
        if (strcmp($asFields['action'], "fetch_push_notification") == 0) {
            $query_fetch = mysqli_query($this->conn, "Select * FROM $ssTableName WHERE device_id='" . $asFields['device_id'] . "' and notification_id='" . $asFields['notification_id'] . "' ");
            $matchFound = mysqli_num_rows($query_fetch) > 0 ? 'yes' : 'no';
            if ((strcmp($matchFound, 'yes') == 0)) {
                $row = mysqli_fetch_assoc($query_fetch);
                $msg = array('result' => '201', 'msg' => 'notification data found', 'data' => $row);  // success
                return $msg;
            } else {
                $response = NOTIFICATION;
                $msg = array('result' => '204', 'msg' => $response[$lang]);  // failure
                return $msg;
            }
        } else {
            $msg = array('result' => '207');  // wrong action
            return $msg;
        }
    }

    /*     * ********************fetch push notification details api ends here*********************** MM*/
    
    
    
    
    /*  MM   *********************Fetch Comment list ************************/
      public function fetch_comment($ssTableName, $asFields) {
        $lang = (isset($asFields['language']) && $asFields['language'] != '') ? $asFields['language']:DEFAULT_LANG;
            if (strcmp($asFields['action'], "fetch_comment") == 0) {
                
                    /* Pagination Code is Here */
                    $rowsPerPage = "2";
                    if ($asFields['page'] != "" && $asFields['page'] != 1) {
                        $setpage = $asFields['page'] - 1;
                        $records = $rowsPerPage * $setpage;
                    } else {
                        $records = 0;
                    }
                    $count = $records + $rowsPerPage;
                    /* Pagination Code is Here */
                
                $query_select = mysqli_query($this->conn, "Select $ssTableName.*,user.name,user.profile_img from $ssTableName inner join user on $ssTableName.userid=user.id where lister_user_id = '".$asFields['lister_user_id']."' order by $ssTableName.id DESC limit $records,$rowsPerPage");
            $matchFound = mysqli_num_rows($query_select) > 0 ? 'yes' : 'no';
            if ((strcmp($matchFound, 'yes') == 0)) {
                while($rec = mysqli_fetch_assoc($query_select)){
                    $data[] = $rec;
                }
                $query_count = mysqli_query($this->conn, "Select $ssTableName.*,user.name,user.profile_img from $ssTableName inner join user on $ssTableName.userid=user.id where lister_user_id = '".$asFields['lister_user_id']."'");
                $number_of_pages = ceil(mysqli_num_rows($query_count)/$rowsPerPage);
                
                
                 $response = LIST_COUNT;
                $msg = array('result' => '201','msg'=>$response[$lang], 'number_of_pages' => "".$number_of_pages."" , 'total_comments' => "".mysqli_num_rows($query_count)."" ,'comment'=>$data); 
                return $msg;
                
            } else {
                $response = ID_INVALID;
                $msg = array('result' => '204','msg'=>$response[$lang]);  // failure
                return $msg;
            }
    }else{
        $msg = array('result' => '207','msg'=>'Action Required');  // failure
                return $msg;
    }
}

    /*     *********************Fetch Comment list ************************MM/
    
    
    
    
    /*  MM   *********************Fetch Reviews list ************************/
      public function fetch_reviews($ssTableName, $asFields) {
        $lang = (isset($asFields['language']) && $asFields['language'] != '') ? $asFields['language']:DEFAULT_LANG;
            if (strcmp($asFields['action'], "fetch_reviews") == 0) {
                
                    /* Pagination Code is Here */
                    $rowsPerPage = "10";
                    if ($asFields['page'] != "" && $asFields['page'] != 1) {
                        $setpage = $asFields['page'] - 1;
                        $records = $rowsPerPage * $setpage;
                    } else {
                        $records = 0;
                    }
                    $count = $records + $rowsPerPage;
                    /* Pagination Code is Here */
                
                $query_count = mysqli_query($this->conn, "Select $ssTableName.* from $ssTableName inner join user on $ssTableName.userid=user.id where $ssTableName.lister_user_id = '".$asFields['lister_user_id']."'");
                $number_of_review_pages = ceil(mysqli_num_rows($query_count)/$rowsPerPage);
                
                $query_select = mysqli_query($this->conn, "Select $ssTableName.*,user.name,user.profile_img,user.phoneno from $ssTableName inner join user on $ssTableName.userid=user.id where $ssTableName.lister_user_id = '".$asFields['lister_user_id']."' order by $ssTableName.id DESC limit $records,$rowsPerPage");
                // echo "Select $ssTableName.*,user.name,user.profile_img,user.phoneno from $ssTableName inner join user on $ssTableName.userid=user.id where $ssTableName.lister_user_id = '".$asFields['lister_user_id']."' order by $ssTableName.id DESC limit $records,$rowsPerPage";
                $matchFound = mysqli_num_rows($query_select) > 0 ? 'yes' : 'no';
                if ((strcmp($matchFound, 'yes') == 0)) {
                    
                $query_avr = mysqli_query($this->conn, "Select AVG($ssTableName.rating_star) as Average_rating,(Select count(*) from $ssTableName inner join user on $ssTableName.userid=user.id where $ssTableName.lister_user_id = '".$asFields['lister_user_id']."' and  $ssTableName.rating_star='1') as avegrage_1_stars,(Select count(*) from $ssTableName inner join user on $ssTableName.userid=user.id where $ssTableName.lister_user_id = '".$asFields['lister_user_id']."' and  $ssTableName.rating_star='2') as avegrage_2_stars,(Select count(*) from $ssTableName inner join user on $ssTableName.userid=user.id where $ssTableName.lister_user_id = '".$asFields['lister_user_id']."' and  $ssTableName.rating_star='3') as avegrage_3_stars,(Select count(*) from $ssTableName inner join user on $ssTableName.userid=user.id where $ssTableName.lister_user_id = '".$asFields['lister_user_id']."' and  $ssTableName.rating_star='4') as avegrage_4_stars,(Select count(*) from $ssTableName inner join user on $ssTableName.userid=user.id where $ssTableName.lister_user_id = '".$asFields['lister_user_id']."' and  $ssTableName.rating_star='5') as avegrage_5_stars from $ssTableName inner join user on $ssTableName.userid=user.id where $ssTableName.lister_user_id = '".$asFields['lister_user_id']."'");
                $detail = mysqli_fetch_Assoc($query_avr);    
                    //print_R($detail);
                $data1['average_rating'] = number_format((float)$detail['Average_rating'], 2, '.', ''); 
                $data1['average_1_stars'] = $detail['avegrage_1_stars'];
                $data1['average_2_stars'] = $detail['avegrage_2_stars'];
                $data1['average_3_stars'] = $detail['avegrage_3_stars'];
                $data1['average_4_stars'] = $detail['avegrage_4_stars'];
                $data1['average_5_stars'] = $detail['avegrage_5_stars'];
                $reviews = array();
                while($rec = mysqli_fetch_assoc($query_select)){
                    $data['id'] = $rec['id'];
                    $data['created_at'] = $rec['created_at'];
                    $data['comment'] = $rec['comment'];
                    $data['rating_star'] = $rec['rating_star'];
                    $data['userid'] = $rec['userid'];
                    $data['whatsapp_allow'] = $rec['whatsapp_allow'];
                    $data['lister_user_id'] = $rec['lister_user_id'];
                    $data['user_name'] = $rec['name'];
                    $data['user_profile_img'] = DB_URL.$rec['profile_img'];
                    $data['mobile'] = $rec['phoneno'];
                    
                    $query_report = mysqli_query($this->conn, "Select * from report where user_id='".$rec['userid']."' and lister_user_id = '".$rec['lister_user_id']."'");
                    if(mysqli_num_rows($query_report) > 0){
                        $isReport = '1';
                    } else {
                        $isReport = '0';
                    }
                    $data['IsReported'] = $isReport;
                    $imageids = explode(',',$rec['image_ids']);
                    
                    $image_Array = array();
                    for($i=0;$i<count($imageids);$i++){
                    //   print_r($imageids[$i]) ;
                           $query_images = mysqli_query($this->conn, "Select * from  image_ids where id='".$imageids[$i]."'");
                           
                           $img_rec = mysqli_fetch_Assoc($query_images);
                           if($img_rec['id']){
                           $img_data['id'] = $img_rec['id'];
                           $img_data['image'] = DB_URL.$img_rec['image'];
                           $sizedata = getimagesize(DB_URL.$img_rec['image']);
                           $img_data['image_width'] = $sizedata[0];
                           $img_data['image_height'] = $sizedata[1];
                           array_push($image_Array,$img_data);
                           }
                    }
                    $data['image_ids'] =$image_Array;
                    
                    $query_reply_count = mysqli_query($this->conn, "Select comment.* from comment inner join user on comment.userid=user.id where comment.review_id = '".$rec['id']."'");
                    $number_of_reply = mysqli_num_rows($query_reply_count);
                    $data['number_of_reply'] = $number_of_reply;
                    if($number_of_reply > 0){
                    $reply_array = array();
                    $query_select_reply = mysqli_query($this->conn, "Select comment.*,user.name,user.profile_img,user.phoneno from comment inner join user on comment.userid=user.id where comment.review_id = '".$rec['id']."' order by comment.id DESC limit 2");
                    while($reply_rec = mysqli_fetch_assoc($query_select_reply)){
                    $rdata['reply_id'] = $reply_rec['id'];
                    $rdata['created_at'] = $reply_rec['created_at'];
                    $rdata['comment'] = $reply_rec['comment'];
                    $rdata['userid'] = $reply_rec['userid'];
                    $rdata['lister_user_id'] = $reply_rec['lister_user_id'];
                    $rdata['user_name'] = $reply_rec['name'];
                    $rdata['user_profile_img'] = DB_URL.$reply_rec['profile_img'];
                    $rdata['mobile'] = $reply_rec['phoneno'];
                    
                    $query_report1 = mysqli_query($this->conn, "Select * from report where user_id='".$reply_rec['userid']."' and lister_user_id = '".$reply_rec['lister_user_id']."'");
                    if(mysqli_num_rows($query_report1) > 0){
                        $isReport_reply = '1';
                    } else {
                        $isReport_reply = '0';
                    }
                    $rdata['IsReported'] = $isReport_reply;
                    $imageids1 = explode(',',$reply_rec['image_ids']);
                    $image_Array1 = array();
                    for($k=0;$k<count($imageids1);$k++){
                           $query_images1 = mysqli_query($this->conn, "Select * from  image_ids where id='".$imageids1[$k]."'");
                           $img_rec1 = mysqli_fetch_Assoc($query_images1);
                           if($img_rec1['id']){
                           $img_data1['id'] = $img_rec1['id'];
                           $img_data1['image'] = DB_URL.$img_rec1['image'];
                           $sizedata1 = getimagesize(DB_URL.$img_rec1['image']);
                           $img_data1['image_width'] = $sizedata1[0];
                           $img_data1['image_height'] = $sizedata1[1];
                           array_push($image_Array1,$img_data1);
                           }
                    }
                    $rdata['image_ids'] =$image_Array1;
                    array_push($reply_array,$rdata);
                    }
                    $data['replies'] =$reply_array;
                } else {
                    $data['replies'] =[];
                }
                array_push($reviews,$data);
                } 
                
                 $response = LIST_COUNT;
                $msg = array('result' => '201','msg'=>$response[$lang], 'number_of_review_pages' => "".$number_of_review_pages."" , 'total_reviews' => "".mysqli_num_rows($query_count)."" ,'review'=>$reviews); 
                $msg1 = array_merge($data1,$msg);
                return $msg1;
                
            } else {
                $response = ID_INVALID;
                $msg = array('result' => '204','msg'=>$response[$lang]);  // failure
                return $msg;
            }
    }else{
        $msg = array('result' => '207','msg'=>'Action Required');  // failure
                return $msg;
    }
}

    /*     *********************Fetch Reviews list ************************MM/
    
    
    
    
    
    
    /*  MM   *********************Fetch Single Reviews Comment list ************************/
      public function fetch_single_review_comments($ssTableName, $asFields) {
        $lang = (isset($asFields['language']) && $asFields['language'] != '') ? $asFields['language']:DEFAULT_LANG;
            if (strcmp($asFields['action'], "fetch_single_review_comments") == 0) {
                
                
                    /* Pagination Code is Here */
                    $rowsPerPage = "10";
                    if ($asFields['page'] != "" && $asFields['page'] != 1) {
                        $setpage = $asFields['page'] - 1;
                        $records = $rowsPerPage * $setpage;
                    } else {
                        $records = 0;
                    }
                    $count = $records + $rowsPerPage;
                    /* Pagination Code is Here */
                    
                $query_count = mysqli_query($this->conn, "Select $ssTableName.* from $ssTableName inner join user on $ssTableName.userid=user.id where $ssTableName.id = '".$asFields['review_id']."'");
                $number_of_review_pages = ceil(mysqli_num_rows($query_count)/$rowsPerPage);
                
                $query_select = mysqli_query($this->conn, "Select $ssTableName.*,user.name,user.profile_img,user.phoneno from $ssTableName inner join user on $ssTableName.userid=user.id where $ssTableName.id = '".$asFields['review_id']."'");
                $matchFound = mysqli_num_rows($query_select) > 0 ? 'yes' : 'no';
                if ((strcmp($matchFound, 'yes') == 0)) {
                    
                while($rec = mysqli_fetch_assoc($query_select)){
                    $data['id'] = $rec['id'];
                    $data['created_at'] = $rec['created_at'];
                    $data['comment'] = $rec['comment'];
                    $data['rating_star'] = $rec['rating_star'];
                    $data['userid'] = $rec['userid'];
                    $data['whatsapp_allow'] = $rec['whatsapp_allow'];
                    $data['lister_user_id'] = $rec['lister_user_id'];
                    $data['user_name'] = $rec['name'];
                    $data['user_profile_img'] = DB_URL.$rec['profile_img'];
                    $data['mobile'] = $rec['phoneno'];
                    
                    $query_report = mysqli_query($this->conn, "Select * from report where user_id='".$rec['userid']."' and lister_user_id = '".$rec['lister_user_id']."'");
                    if(mysqli_num_rows($query_report) > 0){
                        $isReport = '1';
                    } else {
                        $isReport = '0';
                    }
                    $data['IsReported'] = $isReport;
                    $imageids = explode(',',$rec['image_ids']);
                    $image_Array = array();
                    for($i=0;$i<count($imageids);$i++){
                           $query_images = mysqli_query($this->conn, "Select * from  image_ids where id='".$imageids[$i]."'");
                           $img_rec = mysqli_fetch_Assoc($query_images);
                           $img_data['id'] = $img_rec['id'];
                           $img_data['image'] = DB_URL.$img_rec['image'];
                           $sizedata = getimagesize(DB_URL.$img_rec['image']);
                           $img_data['image_width'] = $sizedata[0];
                           $img_data['image_height'] = $sizedata[1];
                           array_push($image_Array,$img_data);
                    }
                    $data['image_ids'] =$image_Array;
                    
                    $query_reply_count = mysqli_query($this->conn, "Select comment.* from comment inner join user on comment.userid=user.id where comment.review_id = '".$rec['id']."'");
                    $number_of_reply = mysqli_num_rows($query_reply_count);
                    $data['number_of_reply'] = $number_of_reply;
                    if($number_of_reply > 0){
                        
                        
                    $data['total_pages'] = ceil(mysqli_num_rows($query_reply_count)/$rowsPerPage);    
                    $reply_array = array();
                    $query_select_reply = mysqli_query($this->conn, "Select comment.*,user.name,user.profile_img,user.phoneno from comment inner join user on comment.userid=user.id where comment.review_id = '".$rec['id']."' order by comment.id DESC limit $records,$rowsPerPage");
                    while($reply_rec = mysqli_fetch_assoc($query_select_reply)){
                    $rdata['reply_id'] = $reply_rec['id'];
                    $rdata['created_at'] = $reply_rec['created_at'];
                    $rdata['comment'] = $reply_rec['comment'];
                    $rdata['userid'] = $reply_rec['userid'];
                    $rdata['lister_user_id'] = $reply_rec['lister_user_id'];
                    $rdata['user_name'] = $reply_rec['name'];
                    $rdata['user_profile_img'] = DB_URL.$reply_rec['profile_img'];
                    $rdata['mobile'] = $reply_rec['phoneno'];
                    
                    $query_report1 = mysqli_query($this->conn, "Select * from report where user_id='".$reply_rec['userid']."' and lister_user_id = '".$reply_rec['lister_user_id']."'");
                    if(mysqli_num_rows($query_report1) > 0){
                        $isReport_reply = '1';
                    } else {
                        $isReport_reply = '0';
                    }
                    $rdata['IsReported'] = $isReport_reply;
                    $imageids1 = explode(',',$reply_rec['image_ids']);
                    $image_Array1 = array();
                    for($k=0;$k<count($imageids1);$k++){
                           $query_images1 = mysqli_query($this->conn, "Select * from  image_ids where id='".$imageids1[$k]."'");
                           $img_rec1 = mysqli_fetch_Assoc($query_images1);
                           $img_data1['id'] = $img_rec1['id'];
                           $img_data1['image'] = DB_URL.$img_rec1['image'];
                           $sizedata1 = getimagesize(DB_URL.$img_rec1['image']);
                           $img_data1['image_width'] = $sizedata1[0];
                           $img_data1['image_height'] = $sizedata1[1];
                           array_push($image_Array1,$img_data1);
                    }
                    $rdata['image_ids'] =$image_Array1;
                    array_push($reply_array,$rdata);
                    }
                    $data['replies'] =$reply_array;
                } else {
                    $data['replies'] =[];
                }
                } 
                if(count($data['replies']) > 0){
                $response = LIST_COUNT;
                $msg = array('result' => '201','msg'=>$response[$lang] ,'review'=>$data); 
                return $msg;
                } else {
                $response = NO_COMMENT;
                $msg = array('result' => '204','msg'=>$response[$lang]); 
                return $msg;
                }
            } else {
                $response = ID_INVALID;
                $msg = array('result' => '204','msg'=>$response[$lang]);  // failure
                return $msg;
            }
    }else{
        $msg = array('result' => '207','msg'=>'Action Required');  // failure
                return $msg;
    }
}

    /*     *********************Fetch Single Reviews Comment list *************************MM/
    
    
      /*     * ********************latitude*********************** */

    public function latitude($ssTableName, $asFields) {
        $lang = (isset($asFields['language']) && $asFields['language'] != '') ? $asFields['language']:DEFAULT_LANG;
        if (strcmp($asFields['action'], "latitude") == 0) {
            $query_fetch = mysqli_query($this->conn, "Select * FROM user WHERE id='" . $asFields['userid'] . "' ");
            $matchFound = mysqli_num_rows($query_fetch) > 0 ? 'yes' : 'no';
            if ((strcmp($matchFound, 'yes') == 0)) {
                $Query_insert = mysqli_query($this->conn, "insert into property_list(user_id,latitude,longitude,created_at) values ('" . $asFields['userid'] . "' ,'" . $asFields['latitude'] . "','" . $asFields['longitude'] . "','".DATE."' ) ");
                $insertid = mysqli_insert_id($this->conn);
                $response = SUCCESS;
                $msg = array('result' => '201', 'msg' => $response[$lang], 'list_id' => $insertid);  // success
                return $msg;
            } else {
                $response = NO_USER_EXIST;
                $msg = array('result' => '204', 'msg' => $response[$lang]);  // success
                return $msg;
            }
        } else {
            $msg = array('result' => '207');  // wrong action
            return $msg;
        }
    }

    /*     * ********************latitude*********************** */
     /*     * ********************fetch full profile*********************** */

    public function fetch_profile($ssTableName, $asFields) {
    $lang = (isset($asFields['language']) && $asFields['language'] != '') ? $asFields['language']:DEFAULT_LANG;
        if (strcmp($asFields['action'], "fetch_profile") == 0) {
            mysqli_query($this->conn,"SET NAMES utf8");
            $query_fetch_translation = mysqli_query($this->conn, "Select * FROM country where id='".$lang."' ");
            $rec_translation = mysqli_fetch_assoc($query_fetch_translation);
            $query_fetch = mysqli_query($this->conn, "Select * FROM user WHERE id='" . $asFields['userid'] . "' ");
            $matchFound = mysqli_num_rows($query_fetch) > 0 ? 'yes' : 'no';
            if ((strcmp($matchFound, 'yes') == 0)) {
            $query_fetch_data = mysqli_query($this->conn,"SELECT * from user where id ='" . $asFields['userid'] . "'  ");
            //   echo "SELECT user.*,property_list.*,property_list.id as ID,property_list.latitude,property_list.longitude,property_type.type from user inner join property_list on user.id = property_list.user_id inner join property_type on property_list.type = property_type.id where user.id ='" . $asFields['userid'] . "'  ";
               $final_array= array();
              while($row = mysqli_fetch_assoc($query_fetch_data)){
                //  print_r($row);
                $data['id'] = $row['id'];
                $data['name'] = $row['name'];
                $data['mobile'] = $row['mobile'];
                $data['country_code'] = $row['country_code'];
                $data['profile_img'] = DB_URL.$row['profile_img'];
                $data['country_code'] = $row['country_code'];
                $data['phoneno'] = $row['phoneno'];
                $data['created_at'] = $row['created_at'];
                $array_current_list = array();
                $query_fetch_data_property = mysqli_query($this->conn,"SELECT ".$rec_translation['country']." as translation,property_list.*,property_type.type from property_list inner join property_type on property_list.type = property_type.id where user_id ='" . $asFields['userid'] . "' ORDER BY id desc LIMIT 3 ");
                while($row1 = mysqli_fetch_assoc($query_fetch_data_property)){
                    $data_fetch['property_id'] = $row1['id'];
                    $data_fetch['latitude'] = $row1['latitude'];
                    $data_fetch['longitude'] = $row1['longitude'];
                    $data_fetch['type'] = $row1['type'];
                     $data_fetch['translation'] = $row1['translation'];
                    $data_fetch['whatsapp_link'] = $row1['whatsapp_link'];
                    $data_fetch['wechat_account'] = $row1['wechat_account'];
                    array_push($array_current_list,$data_fetch);
                }
                
                $data['currently_listing'] = $array_current_list;
                $array_reviews = array();
                $query_fetch_reviews = mysqli_query($this->conn,"SELECT user.*,rating_lister.id as ID,rating_lister.comment,rating_lister.userid,rating_lister.image_ids,rating_lister.rating_star,rating_lister.whatsapp_allow,rating_lister.lister_user_id,rating_lister.created_at as date from rating_lister inner join user on rating_lister.userid = user.id  where lister_user_id  ='" . $asFields['userid'] . "' ");
                // echo "SELECT user.*,rating_lister.id as ID,rating_lister.comment,rating_lister.userid,rating_lister.image_ids,rating_lister.rating_star,rating_lister.whatsapp_allow,rating_lister.lister_user_id,rating_lister.created_at as date from rating_lister inner join user on rating_lister.userid = user.id  where lister_user_id  ='" . $asFields['userid'] . "' ";
                while($row2 = mysqli_fetch_assoc($query_fetch_reviews)){
                    
                    $row_fetch['userid'] = $row2['userid'];
                    $row_fetch['name'] = $row2['name'];
                    $row_fetch['mobile'] = $row2['mobile'];
                    $row_fetch['country_code'] = $row2['country_code'];
                    $row_fetch['profile_img'] = DB_URL.$row2['profile_img'];
                    $row_fetch['country_code'] = $row2['country_code'];
                    $row_fetch['phoneno'] = $row2['phoneno'];
                    $row_fetch['created_at'] = $row2['created_at'];
                    $row_fetch['rate_id'] = $row2['ID'];
                    $row_fetch['comment'] = $row2['comment'];
                    $row_fetch['rating_star'] = $row2['rating_star'];
                    $row_fetch['whatsapp_allow'] = $row2['whatsapp_allow'];
                    $row_fetch['review_date'] = $row2['date'];
                    $row_fetch['image_ids'] = $row2['image_ids'];
                    if($row2['image_ids'] != ''){
                    $image_id = explode(',',$row2['image_ids']);
                  } else {
                      $image_id = [];
                  }
                    $img_array = array();
                    for($i=0;$i<count($image_id);$i++){
                    $query_image = mysqli_query($this->conn, "SELECT id as IMAGEID,CONCAT('".DB_URL."',image) AS IMAGE FROM image_ids where id  = '" . $image_id[$i] . "' ");
                    $rec_img = mysqli_fetch_assoc($query_image);
                    $data_image = getimagesize($rec_img['IMAGE']);
                    if($rec_img['IMAGE'] != ''){
                    $imgfetch['id'] = $rec_img['IMAGEID'];
                    $imgfetch['image'] = $rec_img['IMAGE'];
                    $imgfetch['image_width'] = "" . $data_image[0] . "";
                    $imgfetch['image_height'] = "" . $data_image[1] . "";
                    
                    array_push($img_array,$imgfetch);
                    }}
                    $row_fetch['images'] = $img_array;
                    $query_reply = mysqli_query($this->conn, "SELECT * from comment where lister_user_id =  '".$asFields['userid']."' ");
                    $rec_reply = mysqli_fetch_assoc($query_reply);
                    $row_fetch['replies'][id] = $rec_reply[id];
                    $row_fetch['replies'][comment] = $rec_reply[comment];
                    $row_fetch['replies'][image_ids] = $rec_reply[image_ids];
                    if($rec_reply['image_ids'] != ''){
                    $image_ids = explode(',',$rec_reply['image_ids']);
                  } else {
                      $image_ids = [];
                  }$img_array_reply = array();
                    for($i=0;$i<count($image_ids);$i++){
                    $query_image_reply = mysqli_query($this->conn, "SELECT id as IMAGEIDs,CONCAT('".DB_URL."',image) AS IMAGEs FROM image_ids where id  = '" . $image_ids[$i] . "' ");
                    $rec_img_reply = mysqli_fetch_assoc($query_image_reply);
                    $data_image_reply = getimagesize($rec_img['IMAGE']);
                    if($rec_img['IMAGE'] != ''){
                    $imgfetch1['id'] = $rec_img_reply['IMAGEIDs'];
                    $imgfetch1['image'] = $rec_img_reply['IMAGEs'];
                    $imgfetch1['image_width'] = "" . $data_image_reply[0] . "";
                    $imgfetch1['image_height'] = "" . $data_image_reply[1] . "";
                    array_push($img_array_reply,$imgfetch1);
                    }
                    }
                    $row_fetch['images_reply'] = $img_array_reply;
                    $row_fetch['replies'][created_at] = $rec_reply[created_at];
                    array_push($array_reviews,$row_fetch);
                }
                
                // $data['average_rating'] = $row2['average_rating'];
                // $data['average_1_star'] = $row2['average_1_star'];
                // $data['average_2_star'] = $row2['average_2_star'];
                // $data['average_3_star'] = $row2['average_3_star'];
                // $data['average_4_star'] = $row2['average_4_star'];
                // $data['average_5_star'] = $row2['average_5_star'];
                // $data['total_view_count'] = $row2['total_view_count'];
                //  $data['view_all_reviews'] = $row2['view_all_reviews'];
                
                // $data['reviews'] = $array_reviews;
                array_push($final_array,$data);
           }
           
             $msg = array('result' => '201','msg'=>'Data is shown Succesfully'); // success
                $properties = array('lister_details' => $final_array[0]);
                $latest = array_merge($msg, $properties);
                return $latest;
            }else{
                 $msg = array('result' => '207','msg'=>'Invalid user id');  // wrong action
            return $msg;
            }  
    } else {
            $msg = array('result' => '207','msg'=>'parameter required');  // wrong action
            return $msg;
        }
    }

    /*     * *********************fetch full profile***********************
    
     /*     * ********************search lister*********************** */

    public function search_lister($ssTableName, $asFields) {
    $lang = (isset($asFields['language']) && $asFields['language'] != '') ? $asFields['language']:DEFAULT_LANG;
        if (strcmp($asFields['action'], "search_lister") == 0) {
              if($asFields['search'] ){
            $query_fetch = mysqli_query($this->conn,"SELECT user.*,property_list.wechat_account FROM user left join property_list on user.id = property_list.user_id WHERE lower(name) like lower('%".$asFields['search']."%')  OR mobile like '%". $asFields['search']."%' OR phoneno like '%". $asFields['search']."%' OR wechat_account like '%". $asFields['search']."%' GROUP by user.id ");
// echo "SELECT user.*,property_list.wechat_account FROM user left join property_list on user.id = property_list.user_id WHERE lower(name) like lower('%".$asFields['search']."%')  OR mobile like '%". $asFields['search']."%' OR phoneno like '%". $asFields['search']."%' OR wechat_account like '%". $asFields['search']."%' GROUP by user.id ";
              $final_array= array();
              while($row = mysqli_fetch_assoc($query_fetch)){
                $data['id'] = $row['id'];
                $data['name'] = $row['name'];
                $data['mobile'] = $row['mobile'];
                $data['phoneno'] = $row['phoneno'];
                $data['created_at'] = $row['created_at'];
                $data['profile_image'] = DB_URL.$row['profile_img'];
               $data['wechat_account'] = $row['wechat_account'];
               array_push($final_array,$data);
            }
            $response = DATA_SHOWN;
             $msg = array('result' => '201','msg'=>$response[$lang]); // success
                $properties = array('lister' => $final_array);
                $latest = array_merge($msg, $properties);
                return $latest;
        }else{
            $response = RECORD;
            $msg = array('result' => '205','msg'=>$response[$lang]);  // wrong action
            return $msg;
        }
    } else {
            $msg = array('result' => '207','msg'=>'parameter required');  // wrong action
            return $msg;
        }
    }

    /*     * *********************search lister*********************** AA*/
  /* AA    *********************Add Property**********************/

    public function add_property($ssTableName, $asFields) {
            $lang = (isset($asFields['language']) && $asFields['language'] != '') ? $asFields['language']:DEFAULT_LANG;
        if (strcmp($asFields['action'], "add_property") == 0) {
            if($asFields['user_id'] != ''){
                $days = '+'.$asFields['expire_time'].' day';
                $expire_in = date("Y-m-d h:i:s",strtotime(DATE.$days));
                $lat = $asFields['lats'];
                $lng = $asFields['longs'];
                $url = 'http://maps.googleapis.com/maps/api/geocode/json?latlng='.trim($lat).','.trim($lng).'&sensor=false';
                $json = @file_get_contents($url);
                $data=json_decode($json);
                $status = $data->status;
                if($status=="OK")
                $address = $data->results[0]->formatted_address;
                else
                $address = 'Not found';

                    $Query_insert = mysqli_query($this->conn, "INSERT INTO `property_list`(`created_at`, `type`, `header`, `no_of_rooms`,`price_per_night_curr`, `price_per_night`,`price_per_month_curr`,`price_per_month`, `minimum_stay_day`,minimum_stay_month,minimum_stay_year, `remarks`, `whatsapp_link`, `wechat_account`, `trip_advisor_url`, `expire_day`, `expire_on`, `image_ids`,`latitude`, `longitude`,`address`,  `user_id`, `status`) VALUES
                    ('".DATE."' , '" . $asFields['type'] . "', '" . $asFields['header'] . "', '" . $asFields['no_of_rooms'] . "', '" . $asFields['price_per_night_curr'] . "', '" . $asFields['price_per_night'] . "', '" . $asFields['price_per_month_curr'] . "','" . $asFields['price_per_month'] . "', '" . $asFields['minimum_stay_day'] . "', '" . $asFields['minimum_stay_month'] . "', '" . $asFields['minimum_stay_year'] . "', '" . $asFields['remarks'] . "',
                    '" . $asFields['whatsapp_link'] . "', '" . $asFields['wechat_account'] . "','" . $asFields['trip_advisor_url'] . "','" . $asFields['expire_time'] . "','" . $expire_in . "','" .implode(',',$asFields['image_ids']) . "','" . $asFields['lats'] . "','" . $asFields['longs'] . "', '".$address."' ,'" . $asFields['user_id'] . "', '1') ");
                   $insertrec = mysqli_insert_id($this->conn);
                //   $Query_update = mysqli_query($this->conn,"UPDATE `property_list` SET `type`='" . $asFields['type'] . "',`header`='" . $asFields['header'] . "',`no_of_rooms`='" . $asFields['no_of_rooms'] . "',`price_per_night_curr`='" . $asFields['price_per_night_curr'] . "',`price_per_night`='" . $asFields['price_per_night'] . "',`price_per_month_curr`='" . $asFields['price_per_month_curr'] . "',`price_per_month`='" . $asFields['price_per_month'] . "',`minimum_stay_day`='" . $asFields['minimum_stay_day'] . "',`minimum_stay_month`='" . $asFields['minimum_stay_month'] . "',`minimum_stay_year`='" . $asFields['minimum_stay_year'] . "',`remarks`='" . $asFields['remarks'] . "',`whatsapp_link`='" . $asFields['whatsapp_link'] . "',
                //   `wechat_account`='" . $asFields['wechat_account'] . "',`trip_advisor_url`='" . $asFields['trip_advisor_url'] . "',`expire_day`='" . $asFields['expire_time'] . "',`expire_on`='" . $expire_in . "',`image_ids`='" .implode(',',$asFields['image_ids']) . "',`address`='".$address."',`status`='1',`created_at`='".DATE."' WHERE userid='".$asFields['user_id']."'" );
                   
                //   echo "UPDATE `property_list` SET `type`='" . $asFields['type'] . "',`header`='" . $asFields['header'] . "',`no_of_rooms`='" . $asFields['no_of_rooms'] . "',`price_per_night_curr`='" . $asFields['price_per_night_curr'] . "',`price_per_night`='" . $asFields['price_per_night'] . "',`price_per_month_curr`='" . $asFields['price_per_month_curr'] . "',`price_per_month`='" . $asFields['price_per_month'] . "',`minimum_stay_day`='" . $asFields['minimum_stay_day'] . "',`minimum_stay_month`='" . $asFields['minimum_stay_month'] . "',`minimum_stay_year`='" . $asFields['minimum_stay_year'] . "',`remarks`='" . $asFields['remarks'] . "',`whatsapp_link`='" . $asFields['whatsapp_link'] . "',
                //   `wechat_account`='" . $asFields['wechat_account'] . "',`trip_advisor_url`='" . $asFields['trip_advisor_url'] . "',`expire_day`='" . $asFields['expire_time'] . "',`expire_on`='" . $expire_in . "',`image_ids`='" .implode(',',$asFields['image_ids']) . "',`address`='".$address."',`status`='1',`created_at`='".DATE."' WHERE userid='".$asFields['user_id']."'" ;
                   
                   if($Query_insert)
                    {
                    $response = PROPERTY_INSERT;
                    $msg = array('result' => '201', 'msg' => $response[$lang],'id'=>"".$insertrec."");  // success
                    return $msg;
                    }else{
                        $msg = array('result' => '204', 'msg' =>'Error in  Property Adding');  // success
                    return $msg;
                    }
            } else {
                    $response = PARAMETER_MISSING;
                    $msg = array('result' => '204' , 'msg' => $response[$lang]);  // failure
                    return $msg;
            }
            } else {
                    
                    $msg = array('result' => '204');  // failure
                    return $msg;
            }
        } 

    /*     *********************Add Property**********************/


 /*     *********************Add single Image**********************/

    public function add_image($ssTableName, $asFields) {
      $lang = (isset($asFields['language']) && $asFields['language'] != '') ? $asFields['language']:DEFAULT_LANG;
        if (strcmp($asFields['action'], "add_image") == 0) {
            if($asFields['image']){
                    $image_ins = mysqli_query($this->conn, "INSERT INTO `image_ids`(`image`, `created_at`,`type`) VALUES ('" . $asFields['image'] . "','".DATE."','" . $asFields['type'] . "' ) ");
                    $insertid = mysqli_insert_id($this->conn);
                      if($image_ins){
                          $url = DB_URL.$asFields['image'];
                        //   $thum_image_url = COPY_URL.$asFields['image'];
                         $data = getimagesize($url);
                        $response = IMAGE_INSERTED;
                    $msg = array('result' => '201', 'msg' =>$response[$lang], 'imageid' => "".$insertid."", 'imageurl' => $url ,'Width'=>$data[0],'Height'=>$data[1]);  // success
                    return $msg;
                     }else{
                         $response = ERROR_IMAGE_INSERTED;
                        $msg = array('result' => '204', 'msg' =>$response[$lang]);  // success
                    return $msg;
                    }
              }else{
                  $response = IMAGE_REQUIRED;
                  $msg = array('result' => '204', 'msg' => $response[$lang]);  // failure
                    return $msg;
              }
            } else {
                    $msg = array('result' => '204', 'msg' => 'Action Required');  // failure
                    return $msg;
            }
        } 
    /*     *********************Add single Image**********************/
    /*     *********************Request Lister Api**********************/

    public function request_lister($ssTableName, $asFields) {
      $lang = (isset($asFields['language']) && $asFields['language'] != '') ? $asFields['language']:DEFAULT_LANG;
        if (strcmp($asFields['action'], "request_lister") == 0) {
                    $data_ins = mysqli_query($this->conn, "insert into request_lister(`created_at`,`country`,  `country_iso`, `message`, `mobile`, `code`, `email`,`name`) VALUES ('".DATE."','" . $asFields['country'] . "','" . $asFields['country_iso'] . "','" . $asFields['message'] . "','" . $asFields['mobile'] . "','" . $asFields['code'] . "','" . $asFields['email'] . "','" . $asFields['name'] . "' ) ");
                //   echo "insert into request_lister(`created_at`,`country`,  `country_iso`, `message`, `mobile`, `code`, `email`,`language_id`) VALUES ('".DATE."','" . $asFields['country'] . "','" . $asFields['country_iso'] . "','" . $asFields['message'] . "','" . $asFields['mobile'] . "','" . $asFields['code'] . "','" . $asFields['email'] . "','" . $asFields['language_id'] . "' ) ";
                      if($data_ins){
                          $response = REQUEST;
                    $msg = array('result' => '201', 'msg' =>$response[$lang]);  // success
                    return $msg;
                     }else{
                        $response = ERROR;
                        $msg = array('result' => '204', 'msg' =>$response[$lang]);  // success
                    return $msg;
                    }
              
            } else {
                    $msg = array('result' => '204', 'msg' => 'Action Required');  // failure
                    return $msg;
            }
        } 

    /*     **********************Request Lister Api**********************/
   /*     *********************Fetch Property with user id API**********************/

    public function fetch_property($ssTableName, $asFields) {
        $lang = (isset($asFields['language']) && $asFields['language'] != '') ? $asFields['language']:DEFAULT_LANG;

           if (strcmp($asFields['action'], "fetch_property") == 0) {
                
            $query_select = mysqli_query($this->conn, "Select * from property_list where user_id = '" . $asFields['user_id'] . "' ");
            $matchFound = mysqli_num_rows($query_select) > 0 ? 'yes' : 'no';
            if ((strcmp($matchFound, 'yes') == 0)) {
                    /* Pagination Code is Here */
                    $rowsPerPage = "10";
                    if ($asFields['page'] != "" && $asFields['page'] != 1) {
                        $setpage = $asFields['page'] - 1;
                        $records = $rowsPerPage * $setpage;
                    } else {
                        $records = 0;
                    }
                    /* Pagination Code is Here */
                    
            $query_fetch = mysqli_query($this->conn, "SELECT property_list.*,property_type.type as ptype  FROM property_list inner join property_type on property_list.type=property_type.id   where user_id  = '" . $asFields['user_id'] . "' and property_list.list_type = 1   order by id DESC  limit $records,$rowsPerPage  ");
   
            $query_count = mysqli_query($this->conn, "SELECT property_list.*,property_type.type as ptype  FROM property_list inner join property_type on property_list.type=property_type.id   where user_id  = '" . $asFields['user_id'] . "' and property_list.list_type = 1  ");
            // echo "SELECT property_list.*,property_type.type as ptype ,user.name,cr1.symbol,cr2.symbol FROM property_list inner join property_type on property_list.type=property_type.id inner join currency as cr1 on property_list.price_per_night_curr = cr1.id inner join currency as cr2 on property_list.price_per_month_curr = cr2.id where user_id  = '" . $asFields['user_id'] . "' and property_list.list_type = 1  ";
            $result_count = $query_count/$rowsPerPage;
            $count=ceil($result_count);
                $final_array= array();
                while ($row = mysqli_fetch_assoc($query_fetch)) {
                  $fetch['id'] = $row['id'];
                  $fetch['created_at'] = $row['created_at'];
                  $fetch['type'] = $row['ptype'];
                  $fetch['header'] = $row['header'];
                  $fetch['no_of_rooms'] = $row['no_of_rooms'];
                  $fetch['price_per_night_curr'] = $row['actuall_curr'];
                  $fetch['price_per_night'] = $row['price_per_night_actuall_amnt'];
                  $fetch['price_per_month_curr'] = $row['actuall_curr'];
                  $fetch['price_per_month'] = $row['price_per_month_actuall_amnt'];
                  $fetch['minimum_stay_day'] = $row['minimum_stay_day'];
                  $fetch['minimum_stay_month'] = $row['minimum_stay_month'];
                  $fetch['minimum_stay_year'] = $row['minimum_stay_year'];
                  $fetch['remarks'] = $row['remarks'];
                  $fetch['whatsapp_link'] = $row['whatsapp_link'];
                  $fetch['wechat_account'] = $row['wechat_account'];
                  $fetch['wechat_account'] = $row['wechat_account'];
                  $fetch['trip_advisor_url'] = $row['trip_advisor_url'];
                  $fetch['expire_on'] = $row['expire_on'];
                //   $fetch['image_ids']= $row['image_ids'];
                  if($row['expire_on'] > DATE){
                      $status = 'ACTIVE';
                  } else {
                      $status = 'EXPIRE';
                  }
                  $fetch['status'] = $status;
                  $fetch['property_status'] = $row['status'];
                  if($row['image_ids'] != ''){
                    $image_id = explode(',',$row['image_ids']);
                  } else {
                      $image_id = [];
                  }
                    $img_array = array();
                    for($i=0;$i<count($image_id);$i++){
                    $query_image = mysqli_query($this->conn, "SELECT id,CONCAT('".DB_URL."',image) AS image FROM image_ids where id  = '" . $image_id[$i] . "' ");
                    $rec = mysqli_fetch_assoc($query_image);
                    $data = getimagesize($rec['image']);
                    
                    $imgfetch['id'] = $rec['id'];
                    $imgfetch['image'] = $rec['image'];
                    $imgfetch['image_width'] = "" . $data[0] . "";
                    $imgfetch['image_height'] = "" . $data[1] . "";
                    array_push($img_array,$imgfetch);
                    }
                    $fetch['images'] = $img_array;
                    // $array_reviews = array();
                // $query_fetch_reviews = mysqli_query($this->conn,"SELECT user.*,rating_lister.id as ID,rating_lister.comment,rating_lister.userid,rating_lister.image_ids,rating_lister.rating_star,rating_lister.whatsapp_allow,rating_lister.lister_user_id,rating_lister.created_at as date from rating_lister inner join user on rating_lister.userid = user.id  where lister_user_id  ='" . $asFields['user_id'] . "' ");
                // echo "SELECT user.*,rating_lister.id as ID,rating_lister.comment,rating_lister.userid,rating_lister.image_ids,rating_lister.rating_star,rating_lister.whatsapp_allow,rating_lister.lister_user_id,rating_lister.created_at as date from rating_lister inner join user on rating_lister.userid = user.id  where lister_user_id  ='" . $asFields['user_id'] . "' ";
                // while($row2 = mysqli_fetch_assoc($query_fetch_reviews)){
                    
                //     $row_fetch['userid'] = $row2['userid'];
                //     $row_fetch['name'] = $row2['name'];
                //     $row_fetch['mobile'] = $row2['mobile'];
                //     $row_fetch['country_code'] = $row2['country_code'];
                //     $row_fetch['profile_img'] = DB_URL.$row2['profile_img'];
                //     $row_fetch['country_code'] = $row2['country_code'];
                //     $row_fetch['phoneno'] = $row2['phoneno'];
                //     $row_fetch['created_at'] = $row2['created_at'];
                //     $row_fetch['rate_id'] = $row2['ID'];
                //     $row_fetch['comment'] = $row2['comment'];
                //     $row_fetch['rating_star'] = $row2['rating_star'];
                //     $row_fetch['whatsapp_allow'] = $row2['whatsapp_allow'];
                //     $row_fetch['review_date'] = $row2['date'];
                //     $row_fetch['image_ids'] = $row2['image_ids'];
                //     if($row2['image_ids'] != ''){
                //     $image_id = explode(',',$row2['image_ids']);
                //   } else {
                //       $image_id = [];
                //   }
                //     $img_array = array();
                //     for($i=0;$i<count($image_id);$i++){
                //     $query_image = mysqli_query($this->conn, "SELECT id as IMAGEID,CONCAT('".DB_URL."',image) AS IMAGE FROM image_ids where id  = '" . $image_id[$i] . "' ");
                //     $rec_img = mysqli_fetch_assoc($query_image);
                //     $data_image = getimagesize($rec_img['IMAGE']);
                //     if($rec_img['IMAGE'] != ''){
                //     $imgfetch['id'] = $rec_img['IMAGEID'];
                //     $imgfetch['image'] = $rec_img['IMAGE'];
                //     $imgfetch['image_width'] = "" . $data_image[0] . "";
                //     $imgfetch['image_height'] = "" . $data_image[1] . "";
                //     array_push($img_array,$imgfetch);
                //     }
                //     }
                //     $row_fetch['images'] = $img_array;
                //     $query_reply = mysqli_query($this->conn, "SELECT * from comment where lister_user_id =  '".$asFields['user_id']."' ");
                //     $rec_reply = mysqli_fetch_assoc($query_reply);
                //     $row_fetch['replies'][id] = $rec_reply[id];
                //     $row_fetch['replies'][comment] = $rec_reply[comment];
                //     $row_fetch['replies'][image_ids] = $rec_reply[image_ids];
                //     if($rec_reply['image_ids'] != ''){
                //     $image_ids = explode(',',$rec_reply['image_ids']);
                //   } else {
                //       $image_ids = [];
                //   }$img_array_reply = array();
                //     for($i=0;$i<count($image_ids);$i++){
                //     $query_image_reply = mysqli_query($this->conn, "SELECT id as IMAGEIDs,CONCAT('".DB_URL."',image) AS IMAGEs FROM image_ids where id  = '" . $image_ids[$i] . "' ");
                //     $rec_img_reply = mysqli_fetch_assoc($query_image_reply);
                //     $data_image_reply = getimagesize($rec_img['IMAGE']);
                //     if($rec_img['IMAGE'] != ''){
                //     $imgfetch1['id'] = $rec_img_reply['IMAGEIDs'];
                //     $imgfetch1['image'] = $rec_img_reply['IMAGEs'];
                //     $imgfetch1['image_width'] = "" . $data_image_reply[0] . "";
                //     $imgfetch1['image_height'] = "" . $data_image_reply[1] . "";
                //     array_push($img_array_reply,$imgfetch1);
                //     }
                //     }
                //     $row_fetch['images_reply'] = $img_array_reply;
                //     $row_fetch['replies'][created_at] = $rec_reply[created_at];
                //     array_push($array_reviews,$row_fetch);
                // }
                
                
                      
        
                //     $count_star= count($row4['ID']);
                //      $fetch['average_rating'] = $row4['rating_star'];
                //     $fetch['average_1_star'] = $row4['rating_star'];
                //     $fetch['average_2_star'] = $row4['rating_star'];
                //   $fetch['average_3_star'] =  $row4['rating_star'];
                //   $fetch['average_4_star'] =  $row4['rating_star'];
                //     $fetch['average_5_star'] =  $row4['rating_star'];
                //     $fetch['total_view_count'] = $count_star;
                //     $fetch['view_all_reviews'] =  $row4['rating_star'];
                //   $fetch['reviews'] = $array_reviews;
                    array_push($final_array,$fetch);
                }
               
                $msg = array('result' => '201','msg'=>'Data is shown Succesfully',"count"=>$count); // success
                $properties = array('properties' => $final_array);
                $latest = array_merge($msg, $properties);
                return $latest;
                }else{
                    $response = NO_PROPERTY_EXIST;
                    $msg = array('result' => '204', 'msg' =>$response[$lang]);  // failure
                    return $msg;
                }
            } else {
                $msg = array('result' => '204', 'msg' => 'Action Required');  // failure
                    return $msg;
            }
        } 

    /*     *********************Fetch Property with user id API************************/
    
    
   /*     *********************Multiple Property list Api**********************/

    public function multiple_properties($ssTableName, $asFields) {
        $lang = (isset($asFields['language']) && $asFields['language'] != '') ? $asFields['language']:DEFAULT_LANG;
           mysqli_query($this->conn,"SET NAMES utf8");
            if (strcmp($asFields['action'], "multiple_properties") == 0) {
            $id=$asFields['id'];
            $query_select = mysqli_query($this->conn, "Select * from $ssTableName where id = '" . $id. "' ");
            $matchFound = mysqli_num_rows($query_select) > 0 ? 'yes' : 'no';
            if ((strcmp($matchFound, 'yes') == 0)) {
            
            $query_fetch = mysqli_query($this->conn, "SELECT property_list.*,property_type.type as ptype FROM property_list inner join property_type on property_list.type=property_type.id  where property_list.id IN($id) order by property_list.id DESC");
            // echo "SELECT property_list.*,property_type.type as ptype FROM property_list inner join property_type on property_list.type=property_type.id  where property_list.id IN($id) order by property_list.id DESC";

                    /*$notification_select = mysqli_query($this->conn, "Select * from notification_ids where user_id = '" . $asFields['user_id']. "' ");
                    if(mysqli_num_rows($notification_select) > 0){
                    $crec =  mysqli_fetch_assoc($notification_select);
                    $currency = $crec['currency'];
                    } else {
                    $currency = 'MYR';
                    }*/
                    if($asFields['currency']){
                    $currency = $asFields['currency'];
                    }else{
                        $currency = 'MYR';
                    }
                    /* Currency converter */
                    $to =  'MYR';
                    $from  =  $currency;
                    $url = "http://free.currencyconverterapi.com/api/v5/convert?q=".$from.'_'.$to."&compact=1";
                    $json_array = file_get_contents($url);
                    $json_data = json_decode($json_array, true);
                    $value = $from.'_'.$to;
                    $currency_val =  $json_data['results'][$value]['val'];
                    /* Currency converter */
                    
                    $currency_select = mysqli_query($this->conn, "Select * from currency where name = '" . $from. "' ");
                    $cur_rec = mysqli_fetch_assoc($currency_select);
                    $currency_symbol = $cur_rec['symbol'];
                   
                $final_array= array();
                while ($row = mysqli_fetch_assoc($query_fetch)) {
                  $fetch['id'] = $row['id'];
                  $fetch['created_at'] = $row['created_at'];
                  $fetch['type'] = $row['ptype'];
                  $fetch['header'] = $row['header'];
                  $fetch['no_of_rooms'] = $row['no_of_rooms'];
                  
                  if($row['actuall_curr'] == $currency){
                  $fetch['price_per_night_curr'] = $row['actuall_curr'];
                  $fetch['price_per_night'] = $row['price_per_night_actuall_amnt'];
                  $fetch['price_per_month_curr'] = $row['actuall_curr'];
                  $fetch['price_per_month'] = $row['price_per_month_actuall_amnt'];
                  } else {
                  $fetch['price_per_night_curr'] = $currency_symbol;
                  $fetch['price_per_night'] = round($row['price_per_night_converted_amnt']/$currency_val);
                  $fetch['price_per_month_curr'] = $currency_symbol;
                  $fetch['price_per_month'] = round($row['price_per_month_converted_amnt']/$currency_val);
                  }
                   
                  $fetch['minimum_stay_day'] = $row['minimum_stay_day'];
                  $fetch['minimum_stay_month'] = $row['minimum_stay_month'];
                  $fetch['minimum_stay_year'] = $row['minimum_stay_year'];
                  $fetch['remarks'] = $row['remarks'];
                  $fetch['whatsapp_link'] = $row['whatsapp_link'];
                  $fetch['wechat_account'] = $row['wechat_account'];
                  $fetch['wechat_account'] = $row['wechat_account'];
                  $fetch['trip_advisor_url'] = $row['trip_advisor_url'];
                  $fetch['expire_on'] = $row['expire_on'];
                  if($row['expire_on'] > DATE){
                      $status = 'ACTIVE';
                  } else {
                      $status = 'EXPIRE';
                  }
                  $fetch['status'] = $status;
                  $fetch['property_status'] = $row['status'];
                  if($row['image_ids'] != ''){
                    $image_id = explode(',',$row['image_ids']);
                  } else {
                      $image_id = [];
                  }
                    $img_array = array();
                    for($i=0;$i<count($image_id);$i++){
                    $query_image = mysqli_query($this->conn, "SELECT id,CONCAT('".DB_URL."',image) AS image FROM image_ids where id  = '" . $image_id[$i] . "' ");
                    $rec = mysqli_fetch_assoc($query_image);
                    $data = getimagesize($rec['image']);
                    $imgfetch['id'] = $rec['id'];
                    $imgfetch['image'] = $rec['image'];
                    $imgfetch['image_width'] = "" . $data[0] . "";
                    $imgfetch['image_height'] = "" . $data[1] . "";
                    array_push($img_array,$imgfetch);
                    }
                    $fetch['images'] = $img_array;
                    array_push($final_array,$fetch);
                }
                $msg = array('result' => '201','msg'=>'Data is shown Succesfully'); // success
                $properties = array('properties' => $final_array);
                $latest = array_merge($msg, $properties);
                return $latest;
            }else{
                $response = PROPERTY_ID_NOT_MATCHED;
                $msg = array('result' => '204', 'msg'=>$response[$lang]);  // failure
                    return $msg;
            }
            } else {
                $msg = array('result' => '207', 'msg' => 'Action Required');  // failure
                    return $msg;
            }
        } 

    /*     *********************Multiple Property list Api************************/
    
    
   /*     *********************Edit  Property with user id And Property id **********************/

    public function edit_property($ssTableName, $asFields) {
       $lang = (isset($asFields['language']) && $asFields['language'] != '') ? $asFields['language']:DEFAULT_LANG;
        if (strcmp($asFields['action'], "edit_property") == 0) {
            $query_select = mysqli_query($this->conn, "Select * from $ssTableName where id = '" . $asFields['property_id']. "' and user_id = '".$asFields['user_id']."' ");
            $query_select = mysqli_query($this->conn, "Select * from currency where symbol = '" . $asFields['actuall_curr']. "'  ");
            $row = mysqli_fetch_assoc($query_select);
            $price_per_night_converted_amnt =   $row['RM_amount']*$asFields['price_per_night_actuall_amnt'];
            $price_per_month_converted_amnt =  $row['RM_amount']*$asFields['price_per_month_actuall_amnt'];    
            $matchFound = mysqli_num_rows($query_select) > 0 ? 'yes' : 'no';
            if ((strcmp($matchFound, 'yes') == 0)) {
                $images = implode(',',$asFields['image_ids']);
                $days = '+'.$asFields['expire_time'].' day';
                $expire_in = date("Y-m-d h:i:s",strtotime(DATE.$days));
                if($asFields['latitude'] && $asFields['longitude'])
                {
                     $query_ins = mysqli_query($this->conn, "UPDATE `property_list` SET `updated_at`='".DATE."',`type`='".$asFields['type'] ."',`header`='".$asFields['header'] ."',`no_of_rooms`='".$asFields['no_of_rooms'] ."',`actuall_curr`='".$asFields['actuall_curr'] ."',`price_per_night_actuall_amnt`='".$asFields['price_per_night_actuall_amnt'] ."',`price_per_night_converted_amnt`='".$price_per_night_converted_amnt."',`price_per_month_actuall_amnt`='".$asFields['price_per_month_actuall_amnt'] ."',`price_per_month_converted_amnt`='".$price_per_month_converted_amnt."',`minimum_stay_day`='".$asFields['minimum_stay_day'] ."',`minimum_stay_month`='".$asFields['minimum_stay_month'] ."',`minimum_stay_year`='".$asFields['minimum_stay_year'] ."',`remarks`='".$asFields['remarks'] ."',`whatsapp_link`='".$asFields['whatsapp_link'] ."',`wechat_account`='".$asFields['wechat_account'] ."',`trip_advisor_url`='".$asFields['trip_advisor_url'] ."',`expire_day`='".$asFields['expire_time'] ."',`expire_on`='".$expire_in ."',`image_ids`='".$images ."',`status`='".$asFields['list_type'] ."',`list_type`='".$asFields['list_type'] ."',`latitude`='".$asFields['latitude'] ."',`longitude`='".$asFields['longitude'] ."',`payment_curr`='".$asFields['payment_curr'] ."',`payment_amnt`='".$asFields['payment_amnt'] ."' WHERE user_id = '".$asFields['user_id'] ."'AND id = '".$asFields['property_id'] ."' ");
// echo "UPDATE `property_list` SET `updated_at`='".DATE."',`type`='".$asFields['type'] ."',`header`='".$asFields['header'] ."',`no_of_rooms`='".$asFields['no_of_rooms'] ."',`actuall_curr`='".$asFields['actuall_curr'] ."',`price_per_night_actuall_amnt`='".$asFields['price_per_night_actuall_amnt'] ."',`price_per_night_converted_amnt`='".$price_per_night_converted_amnt."',`price_per_month_actuall_amnt`='".$asFields['price_per_month_actuall_amnt'] ."',`price_per_month_converted_amnt`='".$price_per_month_converted_amnt."',`minimum_stay_day`='".$asFields['minimum_stay_day'] ."',`minimum_stay_month`='".$asFields['minimum_stay_month'] ."',`minimum_stay_year`='".$asFields['minimum_stay_year'] ."',`remarks`='".$asFields['remarks'] ."',`whatsapp_link`='".$asFields['whatsapp_link'] ."',`wechat_account`='".$asFields['wechat_account'] ."',`trip_advisor_url`='".$asFields['trip_advisor_url'] ."',`expire_day`='".$asFields['expire_time'] ."',`expire_on`='".$expire_in ."',`image_ids`='".$images ."',`status`='".$asFields['list_type'] ."',`list_type`='".$asFields['list_type'] ."',`latitude`='".$asFields['latitude'] ."',`longitude`='".$asFields['longitude'] ."' WHERE user_id = '".$asFields['user_id'] ."'AND id = '".$asFields['property_id'] ."' ";
                }else{
                    $query_ins = mysqli_query($this->conn, "UPDATE `property_list` SET `updated_at`='".DATE."',`type`='".$asFields['type'] ."',`header`='".$asFields['header'] ."',`no_of_rooms`='".$asFields['no_of_rooms'] ."',`actuall_curr`='".$asFields['actuall_curr'] ."',`price_per_night_actuall_amnt`='".$asFields['price_per_night_actuall_amnt'] ."',`price_per_night_converted_amnt`='".$price_per_night_converted_amnt."',`price_per_month_actuall_amnt`='".$asFields['price_per_month_actuall_amnt'] ."',`price_per_month_converted_amnt`='".$price_per_month_converted_amnt ."',`minimum_stay_day`='".$asFields['minimum_stay_day'] ."',`minimum_stay_month`='".$asFields['minimum_stay_month'] ."',`minimum_stay_year`='".$asFields['minimum_stay_year'] ."',`remarks`='".$asFields['remarks'] ."',`whatsapp_link`='".$asFields['whatsapp_link'] ."',`wechat_account`='".$asFields['wechat_account'] ."',`trip_advisor_url`='".$asFields['trip_advisor_url'] ."',`expire_day`='".$asFields['expire_time'] ."',`expire_on`='".$expire_in ."',`image_ids`='".$images ."',`status`='".$asFields['list_type'] ."',`list_type`='".$asFields['list_type'] ."',`payment_curr`='".$asFields['payment_curr'] ."',`payment_amnt`='".$asFields['payment_amnt'] ."' WHERE user_id = '".$asFields['user_id'] ."'AND id = '".$asFields['property_id'] ."' ");
// echo "UPDATE `property_list` SET `updated_at`='".DATE."',`type`='".$asFields['type'] ."',`header`='".$asFields['header'] ."',`no_of_rooms`='".$asFields['no_of_rooms'] ."',`actuall_curr`='".$asFields['actuall_curr'] ."',`price_per_night_actuall_amnt`='".$asFields['price_per_night_actuall_amnt'] ."',`price_per_night_converted_amnt`='".$price_per_night_converted_amnt."',`price_per_month_actuall_amnt`='".$asFields['price_per_month_actuall_amnt'] ."',`price_per_month_converted_amnt`='".$price_per_month_converted_amnt ."',`minimum_stay_day`='".$asFields['minimum_stay_day'] ."',`minimum_stay_month`='".$asFields['minimum_stay_month'] ."',`minimum_stay_year`='".$asFields['minimum_stay_year'] ."',`remarks`='".$asFields['remarks'] ."',`whatsapp_link`='".$asFields['whatsapp_link'] ."',`wechat_account`='".$asFields['wechat_account'] ."',`trip_advisor_url`='".$asFields['trip_advisor_url'] ."',`expire_day`='".$asFields['expire_time'] ."',`expire_on`='".$expire_in ."',`image_ids`='".$images ."',`status`='".$asFields['list_type'] ."',`list_type`='".$asFields['list_type'] ."' WHERE user_id = '".$asFields['user_id'] ."'AND id = '".$asFields['property_id'] ."' ";
                }
                if($query_ins){
                          $response = EDIT_PROPERTY;
                    $msg = array('result' => '201', 'msg' =>$response[$lang]);  // success
                    return $msg;
                     }else{
                         $response = ERROR_UPDTAE;
                        $msg = array('result' => '204', 'msg' =>$response[$lang]);  // success
                    return $msg;
                    }
              }else{
                  $response = ID_NOT_MATCHED;
                  $msg = array('result' => '204', 'msg' =>$response[$lang]);  // failure
                    return $msg;
              }
            }
           
            else {
                    $msg = array('result' => '204', 'msg' => 'Action Required');  // failure
                    return $msg;
            }
        } 
    /*     *********************Edit  Property with user id And Property id ************************/
    
        
   /*     *********************Delete Property By Property id **********************/

    public function delete_property($ssTableName, $asFields) {
               $lang = (isset($asFields['language']) && $asFields['language'] != '') ? $asFields['language']:DEFAULT_LANG;
        if (strcmp($asFields['action'], "delete_property") == 0) {
            $query_select = mysqli_query($this->conn, "Select * from $ssTableName where id = '" . $asFields['property_id']. "' ");
            $matchFound = mysqli_num_rows($query_select) > 0 ? 'yes' : 'no';
            if ((strcmp($matchFound, 'yes') == 0)) {
                    $query_delete = mysqli_query($this->conn, "DELETE FROM `property_list` where id = '".$asFields['property_id'] ."' ");
                      if($query_delete){
                          $response = PROPERTY_IS_DELETED;
                    $msg = array('result' => '201', 'msg' =>$response[$lang]);  // success
                    return $msg;
                     }else{
                         $response = ERROR_PROPERTY_DELETED;
                        $msg = array('result' => '204', 'msg' =>$response[$lang]);  // success
                    return $msg;
                    }
              }else{
                  $response = PROPERTY_ID_UNMATCHED;
                  $msg = array('result' => '204', 'msg' =>$response[$lang]);  // failure
                    return $msg;
              }
            } else {
                    $msg = array('result' => '204', 'msg' => 'Action Required');  // failure
                    return $msg;
            }
        } 
    /*     *********************Delete Property By Property id ************************/
/*     *********************Fetch language ************************/
 
    public function language($ssTableName, $asFields) {
        mysqli_query($this->conn,"SET NAMES utf8");
     $lang = (isset($asFields['language']) && $asFields['language'] != '') ? $asFields['language']:DEFAULT_LANG;
            if (strcmp($asFields['action'], "language") == 0) {
            $query_fetch = mysqli_query($this->conn, "Select * FROM languages");
             $final_array =array();
                while ($row = mysqli_fetch_assoc($query_fetch)) {
                    //print_r($row);
                    $data['id'] =  $row['id'];
                    $data['language'] =  $row['language'];
                    array_push($final_array,$data);
                }
                $response = LANGUAGES;
                $msg = array('result' => '201','msg'=>$response[$lang],'data'=>$final_array); 
                return $msg;
            }
         else {
                $msg = array('result' => '204','msg'=>'Action is Required');  // failure
                return $msg;
            }
    }
/*     *********************Fetch language ************************/
/*     *********************current listing language ************************/
 
    public function current_list($ssTableName, $asFields) {
     $lang = (isset($asFields['language']) && $asFields['language'] != '') ? $asFields['language']:DEFAULT_LANG;
      mysqli_query($this->conn,"SET NAMES utf8");
            if (strcmp($asFields['action'], "current_list") == 0) {
                if($asFields['user_id'] !=''){
                    $query_fetchc = mysqli_query($this->conn, "Select * FROM country where id='".$lang."' ");
                      $rec = mysqli_fetch_assoc($query_fetchc);
                $query_select = mysqli_query($this->conn, "Select * from user where id = '" . $asFields['user_id']. "'  ");
            $matchFound = mysqli_num_rows($query_select) > 0 ? 'yes' : 'no';
            if ((strcmp($matchFound, 'yes') == 0)) {
            $query_fetch = mysqli_query($this->conn, "Select ".$rec['country']." as translation,property_list.*,property_type.type from property_list inner join property_type on property_list.type = property_type.id  where property_type.status='1' and property_list.user_id = '" . $asFields['user_id']. "' ");
                $final_array =array();
                while ($row = mysqli_fetch_assoc($query_fetch)) {
                   $data['id'] =  $row['id'];
                    $data['type'] = $row['type'];
                    $data['latitude'] =  $row['latitude'];
                    $data['longitude'] =  $row['longitude'];
                    $data['translation'] = $row['translation'];
                    $data['url'] = DB_URL."accomdation.php?lat=".$row['latitude']."&long=".$row['longitude'];
                    array_push($final_array,$data);
                }
                $response = DATA_SHOWN;
                $msg = array('result' => '201','msg'=>$response[$lang],'data'=>$final_array); 
                return $msg;
             
               }else{
                    $response = ID_INVALID;
                    $msg = array('result' => '201','msg'=>$response[$lang]); 
                return $msg;
               } 
            
                } else {
                $msg = array('result' => '204','msg'=>'user not exist');  // failure
                return $msg;
            }  
            }
         else {
                $msg = array('result' => '204','msg'=>'Action is Required');  // failure
                return $msg;
            }
    }
/*     *********************current listinglanguage ************************/

/*     *********************Fetch currency ************************/
 
    public function currency($ssTableName, $asFields) {
     $lang = (isset($asFields['language']) && $asFields['language'] != '') ? $asFields['language']:DEFAULT_LANG;
        mysqli_query($this->conn,"SET NAMES utf8");
            if (strcmp($asFields['action'], "currency") == 0 ) {
            $query_currency = mysqli_query($this->conn, "Select * FROM currency");
            $final_array =array();
           
            while ($row_fetch = mysqli_fetch_assoc($query_currency)) {
                
             $data['id'] =  $row_fetch['id'];
             $data['name'] =  $row_fetch['name'];
             $data['symbol'] = $row_fetch['symbol'];
             array_push($final_array,$data);   
                }
                $response = CURRENCY;
                $msg = array('result' => '201','msg'=>$response[$lang],'data'=>$final_array); 
                return $msg;
            } else {
                $msg = array('result' => '204','msg'=>'Action is Required');  // failure
                return $msg;
            }
    }
/*     *********************Fetch currency ************************/
/*     *********************Suggestion Api ************************/
 
    public function suggest($ssTableName, $asFields) {
        $lang = (isset($asFields['language']) && $asFields['language'] != '') ? $asFields['language']:DEFAULT_LANG;
            if (strcmp($asFields['action'], "suggest") == 0) {
                $query_select = mysqli_query($this->conn, "Select * from user where  id = '".$asFields['user_id']."' ");
            //echo "Select * from $ssTableName where id = '" . $id. "' ";
            $matchFound = mysqli_num_rows($query_select) > 0 ? 'yes' : 'no';
            if ((strcmp($matchFound, 'yes') == 0)) {
            $query_insert = mysqli_query($this->conn, "INSERT INTO `suggestions`( `user_id`, `suggestion`) VALUES('" . $asFields['user_id'] . "','" . $asFields['suggestion'] . "')");
                if($query_insert){
                $response = SUGGESTION;
                $msg = array('result' => '201','msg'=>$response[$lang]); 
                return $msg;
                }
            } else {
                $response = ID_INVALID;
                $msg = array('result' => '204','msg'=>$response[$lang]);  // failure
                return $msg;
            }
    }else{
        $msg = array('result' => '207','msg'=>'Action Required');  // failure
                return $msg;
    }
}
/*     *********************Suggestion Api ************************/
/*     *********************report Api ************************/
 
    public function report($ssTableName, $asFields) {
        $lang = (isset($asFields['language']) && $asFields['language'] != '') ? $asFields['language']:DEFAULT_LANG;
            if (strcmp($asFields['action'], "report") == 0) {
                if($asFields['user_id'] && $asFields['text']){
                $query_select = mysqli_query($this->conn, "Select * from user where  id = '".$asFields['user_id']."' ");
            //echo "Select * from $ssTableName where id = '" . $id. "' ";
            $matchFound = mysqli_num_rows($query_select) > 0 ? 'yes' : 'no';
            if ((strcmp($matchFound, 'yes') == 0)) {
                 $images = implode(',',$asFields['image_ids']);
            $query_insert = mysqli_query($this->conn, "INSERT INTO `report`(`user_id`, `text`,`image_ids`,`lister_user_id`,`created_at`) VALUES('" . $asFields['user_id'] . "','" . $asFields['text'] . "','" . $images . "','" . $asFields['lister_user_id'] . "','".DATE."')");
                //  echo "INSERT INTO `report`(`user_id`, `text`, `image_ids`,`lister_user_id`,`created_at`) VALUES('" . $asFields['user_id'] . "','" . $asFields['text'] . "','" . $images . "','" . $asFields['lister_user_id'] . "','".DATE."')";
                if($query_insert){
                    $response = REPORT;
                $msg = array('result' => '201','msg'=>$response[$lang]); 
                return $msg;
                }
            } else {
                $response = ID_INVALID;
                $msg = array('result' => '204','msg'=>$response[$lang]);  // failure
                return $msg;
            } }else{
                $msg = array('result' => '207','msg'=>'Required parameter');  // failure
                return $msg;
            }
    }else{
        $msg = array('result' => '207','msg'=>'Action Required');  // failure
                return $msg;
    }
}
/*     *********************report Api ************************/

/*     *********************rating lister Api ************************/
 
    public function rating_lister($ssTableName, $asFields) {
        $lang = (isset($asFields['language']) && $asFields['language'] != '') ? $asFields['language']:DEFAULT_LANG;
            if (strcmp($asFields['action'], "rating_lister") == 0) {
                if($asFields['user_id']){
                $query_select = mysqli_query($this->conn, "Select * from user where  id = '".$asFields['user_id']."' ");
            //echo "Select * from $ssTableName where id = '" . $id. "' ";
            $matchFound = mysqli_num_rows($query_select) > 0 ? 'yes' : 'no';
            if ((strcmp($matchFound, 'yes') == 0)) {
                 $images = implode(',',$asFields['image_ids']);
            $query_insert = mysqli_query($this->conn, "INSERT INTO  `rating_lister`( `created_at`, `image_ids`, `comment`, `rating_star`, `userid`, `whatsapp_allow`,`lister_user_id`) values('".DATE."','" . $images . "','" . $asFields['comment'] . "','" . $asFields['rating_star'] . "','" . $asFields['user_id'] . "','" . $asFields['whatsapp_allow'] . "','" . $asFields['lister_user_id'] . "')");
                if($query_insert){
                    $response = RATING;
                $msg = array('result' => '201','msg'=>$response[$lang]); 
                return $msg;
                }
            } else {
                $response = ID_INVALID;
                $msg = array('result' => '204','msg'=>$response[$lang]);  // failure
                return $msg;
            } }else{
                $msg = array('result' => '207','msg'=>'Required parameter');  // failure
                return $msg;
            }
    }else{
        $msg = array('result' => '207','msg'=>'Action Required');  // failure
                return $msg;
    }
}
/*     *********************rating lister Api ************************/

/*     *********************comment Api ************************/
 
    public function comment($ssTableName, $asFields) {
        $lang = (isset($asFields['language']) && $asFields['language'] != '') ? $asFields['language']:DEFAULT_LANG;
            if (strcmp($asFields['action'], "comment") == 0) {
                if($asFields['user_id'] && $asFields['comment']){
                $query_select = mysqli_query($this->conn, "Select * from user where  id = '".$asFields['user_id']."' ");
            //echo "Select * from $ssTableName where id = '" . $id. "' ";
            $matchFound = mysqli_num_rows($query_select) > 0 ? 'yes' : 'no';
            if ((strcmp($matchFound, 'yes') == 0)) {
                $images = implode(',',$asFields['image_ids']);
            $query_insert = mysqli_query($this->conn, "INSERT INTO  `comment`( `created_at`,  `comment`,`image_ids`, `userid`,`lister_user_id`) VALUES('".DATE."','" . $asFields['comment'] . "','" . $images. "','" . $asFields['user_id'] . "','" . $asFields['lister_user_id'] . "')");
                // echo "INSERT INTO  `comment`( `created_at`, `language_id`, `comment`,`image_ids`, `userid`,`rating_lister_id`) VALUES('".DATE."','" . $asFields['language_id'] . "','" . $asFields['comment'] . "','" . $asFields['image_ids'] . "','" . $asFields['user_id'] . "','" . $asFields['review_id'] . "')";
                if($query_insert){
                    $response = COMMENT;
                $msg = array('result' => '201','msg'=>$response[$lang]); 
                return $msg;
                }
            } else {
                $response = ID_INVALID;
                $msg = array('result' => '204','msg'=>$response[$lang]);  // failure
                return $msg;
            } }else{
                $msg = array('result' => '207','msg'=>'Required parameter');  // failure
                return $msg;
            }
    }else{
        $msg = array('result' => '207','msg'=>'Action Required');  // failure
                return $msg;
    }
}
/*     *********************comment Api ************************/
/*     *********************expire Api ************************/
 
    public function expire($ssTableName, $asFields) {
        $lang = (isset($asFields['language']) && $asFields['language'] != '') ? $asFields['language']:DEFAULT_LANG;
            if (strcmp($asFields['action'], "expire") == 0) {
                $query_select = mysqli_query($this->conn, "Select * from user where  id = '".$asFields['user_id']."' ");
            //echo "Select * from $ssTableName where id = '" . $id. "' ";
            $matchFound = mysqli_num_rows($query_select) > 0 ? 'yes' : 'no';
            if ((strcmp($matchFound, 'yes') == 0)) {
            $query_show= mysqli_query($this->conn, "SELECT * FROM `property_list` where user_id = '".$asFields['user_id']."' and status = '1' and expire_on >= '".DATE."' ");
            //echo "SELECT * FROM `property_list` where user_id = '".$asFields['user_id']."' and status = '1' and expire_on >= '".DATE."' ";
                 $rows = mysqli_num_rows($query_show);
                 $response = LIST_COUNT;
                $msg = array('result' => '201','msg'=>$response[$lang],'count'=>$rows); 
                return $msg;
                
            } else {
                $response = ID_INVALID;
                $msg = array('result' => '204','msg'=>$response[$lang]);  // failure
                return $msg;
            }
    }else{
        $msg = array('result' => '207','msg'=>'Action Required');  // failure
                return $msg;
    }
}
/*     *********************expire Api ************************/

/*     *********************draft api**********************/
 public function draft($ssTableName, $asFields) {
        $lang = (isset($asFields['language']) && $asFields['language'] != '') ? $asFields['language']:DEFAULT_LANG;
            if (strcmp($asFields['action'], "draft") == 0) {
               
            $query_select = mysqli_query($this->conn, "Select * from user where id = '" . $asFields['user_id']. "' ");
            $matchFound = mysqli_num_rows($query_select) > 0 ? 'yes' : 'no';
            if ((strcmp($matchFound, 'yes') == 0)) {
                  /* Pagination Code is Here */
                    $rowsPerPage = "10";
                    if ($asFields['page'] != "" && $asFields['page'] != 1) {
                        $setpage = $asFields['page'] - 1;
                        $records = $rowsPerPage * $setpage;
                    } else {
                        $records = 0;
                    }
                    /* Pagination Code is Here */
                
                
            $query_fetch = mysqli_query($this->conn, "SELECT property_list.*,user.name,user.country_code,user.phoneno,user.created_at as user_created_at,concat('".DB_URL."',user.profile_img) as profile_img,user.user_type,property_type.type as ptype FROM property_list inner join property_type on property_list.type=property_type.id inner join user on property_list.user_id=user.id where property_list.user_id = '" . $asFields['user_id'] . "' and property_list.list_type = '0' order by id DESC  limit $records,$rowsPerPage ");
            // echo "SELECT property_list.*,user.name,cr1.symbol,cr2.symbol,user.country_code,user.phoneno,user.created_at as user_created_at,concat('".DB_URL."',user.profile_img) as profile_img,user.user_type,property_type.type as ptype FROM property_list inner join property_type on property_list.type=property_type.id inner join user on property_list.user_id=user.id inner join currency as cr1 on property_list.price_per_night_curr = cr1.id inner join currency as cr2 on property_list.price_per_month_curr = cr2.id where property_list.user_id = '" . $asFields['user_id'] . "' and property_list.list_type = '0' order by id DESC  limit $records,$rowsPerPage ";
            //   if(mysqli_num_rows($query_fetch)){
                $final_array= array();
                while ($row = mysqli_fetch_assoc($query_fetch)) {
                   
                  $fetch['id'] = $row['id'];
                  $fetch['user_id'] = $row['user_id'];
                  $fetch['name'] = $row['name'];
                  $fetch['country_code'] = $row['country_code'];
                  $fetch['phoneno'] = $row['phoneno'];
                  $fetch['profile_img'] = $row['profile_img'];
                  $fetch['ptype'] = $row['ptype'];
                  $fetch['created_at'] = $row['created_at'];
                  $fetch['type'] = $row['ptype'];
                  $fetch['header'] = $row['header'];
                  $fetch['no_of_rooms'] = $row['no_of_rooms'];
                  $fetch['price_per_night_curr'] = $row['actuall_curr'];
                  $fetch['price_per_night'] = $row['price_per_night_actuall_amnt'];
                  $fetch['price_per_month_curr'] = $row['actuall_curr'];
                  $fetch['price_per_month'] = $row['price_per_month_actuall_amnt'];
                  $fetch['minimum_stay_day'] = $row['minimum_stay_day'];
                  $fetch['minimum_stay_month'] = $row['minimum_stay_month'];
                  $fetch['minimum_stay_year'] = $row['minimum_stay_year'];
                  $fetch['remarks'] = $row['remarks'];
                  $fetch['whatsapp_link'] = $row['whatsapp_link'];
                  $fetch['wechat_account'] = $row['wechat_account'];
                  $fetch['wechat_account'] = $row['wechat_account'];
                  $fetch['trip_advisor_url'] = $row['trip_advisor_url'];
                  $fetch['expire_on'] = $row['expire_on'];
                  $fetch['latitude'] = $row['latitude'];
                  $fetch['longitude'] = $row['longitude'];
                  $fetch['user_created_at'] = $row['user_created_at'];
                  $fetch['mapURl']= DB_URL."accomdation.php?lat=".$row['latitude']."&long=".$row['longitude'];
                  if($row['expire_on'] > DATE){
                      $status = 'ACTIVE';
                  } else {
                      $status = 'EXPIRE';
                  }
                  $fetch['status'] = $status;
                  $fetch['property_status'] = $row['status'];
                  if($row['image_ids'] != ''){
                    $image_id = explode(',',$row['image_ids']);
                  } else {
                      $image_id = [];
                  }
                    $img_array = array();
                    for($i=0;$i<count($image_id);$i++){
                    $query_image = mysqli_query($this->conn, "SELECT id,CONCAT('".DB_URL."',image) AS image FROM image_ids where id  = '" . $image_id[$i] . "' ");
                    $rec = mysqli_fetch_assoc($query_image);
                     $data = getimagesize($rec['image']);
                     $imgfetch['id'] = $rec['id'];
                    $imgfetch['image'] = $rec['image'];
                    $imgfetch['image_width'] = "" . $data[0] . "";
                    $imgfetch['image_height'] = "" . $data[1] . "";
                    array_push($img_array,$imgfetch);
                    }
                    $fetch['images'] = $img_array;
                    array_push($final_array,$fetch);
                }
                // print_r($final_array);
                $response = DATA_SHOWN;
                $msg = array('result' => '201','msg'=>$response[$lang]); // success
                 $properties = array('properties' => $final_array);
                 $latest = array_merge($properties,$msg);
                 return $latest;
                // }
                
                // else{
                //     // $response = ERROR_IN_SHOWN;
                //      $msg = array('result' => '204' );  // failure
                //      return $msg;
                     
                // }
                
            } else {
                $response = ID_INVALID;
                $msg = array('result' => '207', 'msg' => $response[$lang]);  // failure
                    return $msg;
            }
        } else {
                $msg = array('result' => '207', 'msg' => 'Action Required');  // failure
                    return $msg;
            }
}
    /*     *********************Select Property By Property id ************************ 


 /*     *********************Select Property By Property id **********************/
 public function select_property($ssTableName, $asFields) {
        $lang = (isset($asFields['language']) && $asFields['language'] != '') ? $asFields['language']:DEFAULT_LANG;
            if (strcmp($asFields['action'], "select_property") == 0) {
            $query_select = mysqli_query($this->conn, "Select * from $ssTableName where id = '" . $asFields['property_id']. "' ");
            $matchFound = mysqli_num_rows($query_select) > 0 ? 'yes' : 'no';
            if ((strcmp($matchFound, 'yes') == 0)) {
                
                    /*$notification_select = mysqli_query($this->conn, "Select * from notification_ids where user_id = '" . $asFields['user_id']. "' ");
                    if(mysqli_num_rows($notification_select) > 0){
                    $crec =  mysqli_fetch_assoc($notification_select);
                    $currency = $crec['currency'];
                    } else {
                    $currency = 'MYR';
                    }*/
                    if($asFields['currency'] != ''){
                    $currency = $asFields['currency'];
                    } else {
                    $currency = 'MYR';
                    }
                    /* Currency converter */
                    $to =  'MYR';
                    $from  =  $currency;
                    $url = "http://free.currencyconverterapi.com/api/v5/convert?q=".$from.'_'.$to."&compact=1";
                    $json_array = file_get_contents($url);
                    $json_data = json_decode($json_array, true);
                    $value = $from.'_'.$to;
                    $currency_val =  $json_data['results'][$value]['val'];
                    /* Currency converter */
                    
                    $currency_select = mysqli_query($this->conn, "Select * from currency where name = '" . $from. "' ");
                    $cur_rec = mysqli_fetch_assoc($currency_select);
                    $currency_symbol = $cur_rec['symbol'];
                    
            $query_fetch = mysqli_query($this->conn, "SELECT property_list.*,user.name,user.country_code,user.phoneno,user.created_at as user_created_at,concat('".DB_URL."',user.profile_img) as profile_img,user.user_type,property_type.type as ptype FROM property_list inner join property_type on property_list.type=property_type.id inner join user on property_list.user_id=user.id  where property_list.id = '" . $asFields['property_id'] . "'");
               if(mysqli_num_rows($query_fetch) > 0){
                $final_array= array();
                while ($row = mysqli_fetch_assoc($query_fetch)) {
                   
                  $fetch['id'] = $row['id'];
                  $fetch['user_id'] = $row['user_id'];
                  $fetch['name'] = $row['name'];
                  $fetch['country_code'] = $row['country_code'];
                  $fetch['phoneno'] = $row['phoneno'];
                  $fetch['profile_img'] = $row['profile_img'];
                  $fetch['ptype'] = $row['ptype'];
                  $fetch['created_at'] = $row['created_at'];
                  $fetch['type'] = $row['ptype'];
                  $fetch['header'] = $row['header'];
                  $fetch['no_of_rooms'] = $row['no_of_rooms'];
                //   $fetch['number'] = round($row['price_per_night_converted_amnt']/$currency_val);
                  if($asFields['user_id'] == $row['user_id'] || $row['actuall_curr'] == $currency){
                  $fetch['price_per_night_curr'] = $row['actuall_curr'];
                  $fetch['price_per_night'] = round($row['price_per_night_actuall_amnt']);
                  $fetch['price_per_month_curr'] = $row['actuall_curr'];
                  $fetch['price_per_month'] = round($row['price_per_month_actuall_amnt']);
                  } else {
                  $fetch['price_per_night_curr'] = $currency_symbol;
                  $fetch['price_per_night'] = round($row['price_per_night_converted_amnt']/$currency_val);
                  $fetch['price_per_month_curr'] = $currency_symbol;
                  $fetch['price_per_month'] = round($row['price_per_month_converted_amnt']/$currency_val);
                  }
                  
                  $fetch['minimum_stay_day'] = $row['minimum_stay_day'];
                  $fetch['minimum_stay_month'] = $row['minimum_stay_month'];
                  $fetch['minimum_stay_year'] = $row['minimum_stay_year'];
                  $fetch['remarks'] = $row['remarks'];
                  $fetch['whatsapp_link'] = $row['whatsapp_link'];
                  $fetch['wechat_account'] = $row['wechat_account'];
                  $fetch['wechat_account'] = $row['wechat_account'];
                  $fetch['trip_advisor_url'] = $row['trip_advisor_url'];
                  $fetch['expire_on'] = $row['expire_on'];
                  $fetch['latitude'] = $row['latitude'];
                  $fetch['longitude'] = $row['longitude'];
                  $fetch['user_created_at'] = $row['user_created_at'];
                  $fetch['mapURl']= DB_URL."accomdation.php?lat=".$row['latitude']."&long=".$row['longitude'];
                  if($row['expire_on'] > DATE){
                      $status = 'ACTIVE';
                  } else {
                      $status = 'EXPIRE';
                  }
                  $fetch['status'] = $status;
                  $fetch['property_status'] = $row['status'];
                  if($row['image_ids'] != ''){
                    $image_id = explode(',',$row['image_ids']);
                  } else {
                      $image_id = [];
                  }
                    $img_array = array();
                    for($i=0;$i<count($image_id);$i++){
                    $query_image = mysqli_query($this->conn, "SELECT id,CONCAT('".DB_URL."',image) AS image FROM image_ids where id  = '" . $image_id[$i] . "' ");
                    $rec = mysqli_fetch_assoc($query_image);
                     $data = getimagesize($rec['image']);
                     $imgfetch['id'] = $rec['id'];
                    $imgfetch['image'] = $rec['image'];
                    $imgfetch['image_width'] = "" . $data[0] . "";
                    $imgfetch['image_height'] = "" . $data[1] . "";
                    array_push($img_array,$imgfetch);
                    }
                    $fetch['images'] = $img_array;
                //     //---------------------------------
                //     $array_reviews = array();
                // $query_fetch_reviews = mysqli_query($this->conn,"SELECT user.*,rating_lister.id as ID,rating_lister.comment,rating_lister.userid,rating_lister.image_ids,rating_lister.rating_star,rating_lister.whatsapp_allow,rating_lister.lister_user_id,rating_lister.created_at as date from rating_lister inner join user on rating_lister.userid = user.id  where lister_user_id  ='" . $asFields['user_id'] . "' ");
                // echo "SELECT user.*,rating_lister.id as ID,rating_lister.comment,rating_lister.userid,rating_lister.image_ids,rating_lister.rating_star,rating_lister.whatsapp_allow,rating_lister.lister_user_id,rating_lister.created_at as date from rating_lister inner join user on rating_lister.userid = user.id  where lister_user_id  ='" . $asFields['user_id'] . "' ";
                // while($row2 = mysqli_fetch_assoc($query_fetch_reviews)){
                    
                //     $row_fetch['userid'] = $row2['userid'];
                //     $row_fetch['name'] = $row2['name'];
                //     $row_fetch['mobile'] = $row2['mobile'];
                //     $row_fetch['country_code'] = $row2['country_code'];
                //     $row_fetch['profile_img'] = DB_URL.$row2['profile_img'];
                //     $row_fetch['country_code'] = $row2['country_code'];
                //     $row_fetch['phoneno'] = $row2['phoneno'];
                //     $row_fetch['created_at'] = $row2['created_at'];
                //     $row_fetch['rate_id'] = $row2['ID'];
                //     $row_fetch['comment'] = $row2['comment'];
                //     $row_fetch['rating_star'] = $row2['rating_star'];
                //     $row_fetch['whatsapp_allow'] = $row2['whatsapp_allow'];
                //     $row_fetch['review_date'] = $row2['date'];
                //     $row_fetch['image_ids'] = $row2['image_ids'];
                //     if($row2['image_ids'] != ''){
                //     $image_id = explode(',',$row2['image_ids']);
                //   } else {
                //       $image_id = [];
                //   }
                //     $img_array = array();
                //     for($i=0;$i<count($image_id);$i++){
                //     $query_image = mysqli_query($this->conn, "SELECT id as IMAGEID,CONCAT('".DB_URL."',image) AS IMAGE FROM image_ids where id  = '" . $image_id[$i] . "' ");
                //     $rec_img = mysqli_fetch_assoc($query_image);
                //     $data_image = getimagesize($rec_img['IMAGE']);
                //     if($rec_img['IMAGE'] != ''){
                //     $imgfetch['id'] = $rec_img['IMAGEID'];
                //     $imgfetch['image'] = $rec_img['IMAGE'];
                //     $imgfetch['image_width'] = "" . $data_image[0] . "";
                //     $imgfetch['image_height'] = "" . $data_image[1] . "";
                //     array_push($img_array,$imgfetch);
                //     }
                //     }
                //     $row_fetch['images'] = $img_array;
                //     $query_reply = mysqli_query($this->conn, "SELECT * from comment where lister_user_id =  '".$asFields['user_id']."' ");
                //     $rec_reply = mysqli_fetch_assoc($query_reply);
                //     $row_fetch['replies'][id] = $rec_reply[id];
                //     $row_fetch['replies'][comment] = $rec_reply[comment];
                //     $row_fetch['replies'][image_ids] = $rec_reply[image_ids];
                //     if($rec_reply['image_ids'] != ''){
                //     $image_ids = explode(',',$rec_reply['image_ids']);
                //   } else {
                //       $image_ids = [];
                //   }$img_array_reply = array();
                //     for($i=0;$i<count($image_ids);$i++){
                //     $query_image_reply = mysqli_query($this->conn, "SELECT id as IMAGEIDs,CONCAT('".DB_URL."',image) AS IMAGEs FROM image_ids where id  = '" . $image_ids[$i] . "' ");
                //     $rec_img_reply = mysqli_fetch_assoc($query_image_reply);
                //     $data_image_reply = getimagesize($rec_img['IMAGE']);
                //     if($rec_img['IMAGE'] != ''){
                //     $imgfetch1['id'] = $rec_img_reply['IMAGEIDs'];
                //     $imgfetch1['image'] = $rec_img_reply['IMAGEs'];
                //     $imgfetch1['image_width'] = "" . $data_image_reply[0] . "";
                //     $imgfetch1['image_height'] = "" . $data_image_reply[1] . "";
                //     array_push($img_array_reply,$imgfetch1);
                //     }
                //     }
                //     $row_fetch['images_reply'] = $img_array_reply;
                //     $row_fetch['replies'][created_at] = $rec_reply[created_at];
                //     array_push($array_reviews,$row_fetch);
                // }
                
                
                      
        
                //     $count_star= count($row4['ID']);
                //      $fetch['average_rating'] = $row4['rating_star'];
                //     $fetch['average_1_star'] = $row4['rating_star'];
                //     $fetch['average_2_star'] = $row4['rating_star'];
                //   $fetch['average_3_star'] =  $row4['rating_star'];
                //   $fetch['average_4_star'] =  $row4['rating_star'];
                //     $fetch['average_5_star'] =  $row4['rating_star'];
                //     $fetch['total_view_count'] = $count_star;
                //     $fetch['view_all_reviews'] =  $row4['rating_star'];
                //   $fetch['reviews'] = $array_reviews;
                    
                    
                    
                //     //---------------------------------
                    array_push($final_array,$fetch);
                }
                $msg = array('result' => '201','msg'=>'Data is shown Succesfully'); // success
                 $properties = array('properties' => $fetch);
                 $latest = array_merge($properties,$msg);
                 return $latest;
                }
                else{
                    $response = ERROR_IN_SHOWN;
                   $msg = array('result' => '204', 'msg' =>$response[$lang] );  // failure
                    return $msg; 
                }
                }else{
                    $response = PROPERTY_ID;
                    $msg = array('result' => '204', 'msg' => $response[$lang]);  // failure
                    return $msg;
                }
            } else {
                $msg = array('result' => '207', 'msg' => 'Action Required');  // failure
                    return $msg;
            }
        } 

    /*     *********************Select Property By Property id ************************ AA/

 /*     *********************Select nearby Property By Property id **********************/
 public function select_nearby_property($ssTableName, $asFields) {
        $lang = (isset($asFields['language']) && $asFields['language'] != '') ? $asFields['language']:DEFAULT_LANG;
            if (strcmp($asFields['action'], "nearby_property") == 0) {
            $query_select = mysqli_query($this->conn, "Select * from $ssTableName ");
            $matchFound = mysqli_num_rows($query_select) > 0 ? 'yes' : 'no';
            if ((strcmp($matchFound, 'yes') == 0)) {
                
            $query_fetch = mysqli_query($this->conn, "SELECT property_list.*,user.name,user.country_code,user.phoneno,concat('".DB_URL."',user.profile_img) as profile_img,user.user_type,property_type.type as ptype FROM property_list inner join property_type on property_list.type=property_type.id inner join user on property_list.user_id=user.id   order by rand() limit 0,3");
            //   "SELECT property_list.*,user.name,user.country_code,user.phoneno,concat('".DB_URL."',user.profile_img) as profile_img,user.user_type,property_type.type as ptype FROM property_list inner join property_type on property_list.type=property_type.id inner join user on property_list.user_id=user.id order by rand() limit 0,3";
               if(mysqli_num_rows($query_fetch) > 0){
                $final_array= array();
                while ($row = mysqli_fetch_assoc($query_fetch)) {
                  $fetch['id'] = $row['id'];
                  $fetch['user_id'] = $row['user_id'];
                  $fetch['name'] = $row['name'];
                  $fetch['country_code'] = $row['country_code'];
                  $fetch['phoneno'] = $row['phoneno'];
                  $fetch['profile_img'] = $row['profile_img'];
                  $fetch['ptype'] = $row['ptype'];
                  $fetch['created_at'] = $row['created_at'];
                  $fetch['type'] = $row['ptype'];
                  $fetch['header'] = $row['header'];
                  $fetch['no_of_rooms'] = $row['no_of_rooms'];
                  $fetch['price_per_night_curr'] = $row['actuall_curr'];
                  $fetch['price_per_night'] = $row['price_per_night_actuall_amnt'];
                  $fetch['price_per_month_curr'] = $row['actuall_curr'];
                  $fetch['price_per_month'] = $row['price_per_month_actuall_amnt'];
                  $fetch['minimum_stay_day'] = $row['minimum_stay_day'];
                  $fetch['minimum_stay_month'] = $row['minimum_stay_month'];
                  $fetch['minimum_stay_year'] = $row['minimum_stay_year'];
                  $fetch['remarks'] = $row['remarks'];
                  $fetch['whatsapp_link'] = $row['whatsapp_link'];
                  $fetch['wechat_account'] = $row['wechat_account'];
                  $fetch['wechat_account'] = $row['wechat_account'];
                  $fetch['trip_advisor_url'] = $row['trip_advisor_url'];
                  $fetch['expire_on'] = $row['expire_on'];
                  $fetch['latitude'] = $row['latitude'];
                  $fetch['longitude'] = $row['longitude'];
                  $fetch['mapURl']= DB_URL."accomdation.php?lat=".$row['latitude']."&long=".$row['longitude'];
                  if($row['expire_on'] > DATE){
                      $status = 'ACTIVE';
                  } else {
                      $status = 'EXPIRE';
                  }
                  $fetch['status'] = $status;
                  $fetch['property_status'] = $row['status'];
                  if($row['image_ids'] != ''){
                    $image_id = explode(',',$row['image_ids']);
                  } else {
                      $image_id = [];
                  }
                    $img_array = array();
                    for($i=0;$i<count($image_id);$i++){
                    $query_image = mysqli_query($this->conn, "SELECT id,CONCAT('".DB_URL."',image) AS image FROM image_ids where id  = '" . $image_id[$i] . "' ");
                    $rec = mysqli_fetch_assoc($query_image);
                     $data = getimagesize($rec['image']);
                     $imgfetch['id'] = $rec['id'];
                    $imgfetch['image'] = $rec['image'];
                    $imgfetch['image_width'] = "" . $data[0] . "";
                    $imgfetch['image_height'] = "" . $data[1] . "";
                    array_push($img_array,$imgfetch);
                    }
                    $fetch['images'] = $img_array;
                    array_push($final_array,$fetch);
                    $listpro[] = $fetch;
                }
                $response = DATA_SHOWN;
                $msg = array('result' => '201','msg'=>$response[$lang]); // success
                 $properties = array('properties' => $listpro);
                 $latest = array_merge($properties,$msg);
                 return $latest;
                }
                else{
                    $response = ERROR_IN_SHOWN;
                   $msg = array('result' => '204', 'msg' =>$response[$lang] );  // failure
                    return $msg; 
                }
                }else{
                    $response = PROPERTY_ID;
                    $msg = array('result' => '204', 'msg' => $response[$lang]);  // failure
                    return $msg;
                }
            } else {
                $msg = array('result' => '207', 'msg' => 'Action Required');  // failure
                    return $msg;
            }
        } 

    /*     *********************Select Property By Property id ************************ AA/

 /*     *********************recent property**********************/
 public function recent_properties($ssTableName, $asFields) {
        $lang = (isset($asFields['language']) && $asFields['language'] != '') ? $asFields['language']:DEFAULT_LANG;
            if (strcmp($asFields['action'], "recent_properties") == 0) {
            $query_select = mysqli_query($this->conn, "Select * from $ssTableName ");
            $matchFound = mysqli_num_rows($query_select) > 0 ? 'yes' : 'no';
            if ((strcmp($matchFound, 'yes') == 0)) {
                
            $query_fetch = mysqli_query($this->conn, "SELECT property_list.*,property_type.type as ptype FROM property_list inner join property_type on property_list.type=property_type.id order by property_list.id Desc limit 0,2");
        //   echo "SELECT property_list.*,cr1.symbol,cr2.symbol,property_type.type as ptype FROM property_list inner join property_type on property_list.type=property_type.id inner join currency as cr1 on property_list.price_per_night_curr = cr1.id inner join currency as cr2 on property_list.price_per_month_curr = cr2.id  order by property_list.id Desc limit 0,3";
               if(mysqli_num_rows($query_fetch) > 0){
                $final_array= array();
                while ($row = mysqli_fetch_assoc($query_fetch)) {
                  $fetch['id'] = $row['id'];
                  $fetch['ptype'] = $row['ptype'];
                  $fetch['type'] = $row['ptype'];
                  $fetch['header'] = $row['header'];
                  $fetch['no_of_rooms'] = $row['no_of_rooms'];
                  $fetch['price_per_night_curr'] = $row['actuall_curr'];
                  $fetch['price_per_night'] = $row['price_per_night_actuall_amnt'];
                  $fetch['price_per_month_curr'] = $row['actuall_curr'];
                  $fetch['price_per_month'] = $row['price_per_month_actuall_amnt'];
                  $fetch['minimum_stay_day'] = $row['minimum_stay_day'];
                  $fetch['minimum_stay_month'] = $row['minimum_stay_month'];
                  $fetch['minimum_stay_year'] = $row['minimum_stay_year'];
                  $fetch['remarks'] = $row['remarks'];
                  $fetch['whatsapp_link'] = $row['whatsapp_link'];
                  $fetch['wechat_account'] = $row['wechat_account'];
                  $fetch['wechat_account'] = $row['wechat_account'];
                  $fetch['trip_advisor_url'] = $row['trip_advisor_url'];
                  $fetch['expire_on'] = $row['expire_on'];
                  $fetch['latitude'] = $row['latitude'];
                  $fetch['longitude'] = $row['longitude'];
                  $fetch['mapURl']= DB_URL."accomdation.php?lat=".$row['latitude']."&long=".$row['longitude'];
                  if($row['expire_on'] > DATE){
                      $status = 'ACTIVE';
                  } else {
                      $status = 'EXPIRE';
                  }
                  $fetch['status'] = $status;
                  $fetch['property_status'] = $row['status'];
                  if($row['image_ids'] != ''){
                    $image_id = explode(',',$row['image_ids']);
                  } else {
                      $image_id = [];
                  }
                    $img_array = array();
                    for($i=0;$i<count($image_id);$i++){
                    $query_image = mysqli_query($this->conn, "SELECT id,CONCAT('".DB_URL."',image) AS image FROM image_ids where id  = '" . $image_id[$i] . "' ");
                    $rec = mysqli_fetch_assoc($query_image);
                     $data = getimagesize($rec['image']);
                     $imgfetch['id'] = $rec['id'];
                    $imgfetch['image'] = $rec['image'];
                    $imgfetch['image_width'] = "" . $data[0] . "";
                    $imgfetch['image_height'] = "" . $data[1] . "";
                    array_push($img_array,$imgfetch);
                    }
                    $fetch['images'] = $img_array;
                    array_push($final_array,$fetch);
                    $listpro[] = $fetch;
                }
                $response = DATA_SHOWN;
                $msg = array('result' => '201','msg'=>$response[$lang]); // success
                 $properties = array('properties' => $listpro);
                 $latest = array_merge($properties,$msg);
                 return $latest;
                }
                else{
                    $response = ERROR_IN_SHOWN;
                   $msg = array('result' => '204', 'msg' =>$response[$lang] );  // failure
                    return $msg; 
                }
                }else{
                    $response = PROPERTY_ID;
                    $msg = array('result' => '204', 'msg' => $response[$lang]);  // failure
                    return $msg;
                }
            } else {
                $msg = array('result' => '207', 'msg' => 'Action Required');  // failure
                    return $msg;
            }
        } 

    /*     *********************recent property************************ 

 /*     *********************Select Blog List **********************/
 public function fetch_blog($ssTableName, $asFields) {
             $lang = (isset($asFields['language']) && $asFields['language'] != '') ? $asFields['language']:DEFAULT_LANG;

            if (strcmp($asFields['action'], "fetch_blog") == 0) {
                if($asFields['status']){
                if(!empty($asFields['limit']) &&  !isset($asFields['category']) ):
                        
                    $query_fetch = mysqli_query($this->conn, "SELECT * FROM `tbl_blog` where status = '" . $asFields['status'] . "' order by id desc limit " . $asFields['start_from'] . " , " . $asFields['limit'] . " " );
                elseif(isset($asFields['category']) && !empty($asFields['category'])):

                     $qry="SELECT * FROM `tbl_blog` where status = " . $asFields['status'] . " AND category_id = " . $asFields['category'] . ""."  order by id desc limit " . $asFields['start_from'] . " , " . $asFields['limit'] . " ";
                     $query_fetch = mysqli_query($this->conn, $qry);
                else:
                    $query_fetch = mysqli_query($this->conn, "SELECT * FROM `tbl_blog` where status = '" . $asFields['status'] . "' order by id desc");
                endif;

                while ($row = mysqli_fetch_assoc($query_fetch)) {
               
                  $fetch[] = $row;
                }
              $response = DATA_SHOWN;
                $msg = array('result' => '201','msg'=> $response[$lang]); // success
                $properties = array('blog' => $fetch);
                $latest = array_merge($msg, $properties);
                return $latest;
                }else{
                    $response = STATUS;
                    $msg = array('result' => '204', 'msg' => $response[$lang]);  // failure
                    return $msg;
                }
            } else {
                $msg = array('result' => '204', 'msg' => 'Action Required');  // failure
                    return $msg;
            }
        } 

    /*     *********************Select Blog List ************************/
     public function fetch_slug($ssTableName, $pid,$status) {
            
        
                    
            $query_fetch = mysqli_query($this->conn, "SELECT * FROM `tbl_slug` WHERE `pid`= '" . $pid . "' AND  `post_type`= '" . $status . "'");
            //echo  "SELECT * FROM property_list where id = '" . $asFields['property_id'] . "' ";
                while ($row = mysqli_fetch_assoc($query_fetch)) {
                  $fetch[] = $row;
                }
                $msg = array('result' => '201','msg'=>'Data is shown Succesfully'); // success
                $properties = array('slug' => $fetch);
                $latest = array_merge($msg, $properties);
                return $latest;
                
            }
         

    /*     *********************Select Property By Property id ************************/
    
    /*     *********************Select Blog COunt id for pagination ************************/
     public function fetch_blog_count($query) {
            
        
            $query_fetch = mysqli_query($this->conn, $query);
            //echo  "SELECT * FROM property_list where id = '" . $asFields['property_id'] . "' ";
                while ($row = mysqli_fetch_assoc($query_fetch)) {
                  $fetch[] = $row;
                }

                $msg = array('result' => '201','msg'=>'Data is shown Succesfully'); // success
                $properties = array('count' => $fetch);
                $latest = array_merge($msg, $properties);
                return $latest;
                
            }
         

    /*     *********************Select Blog COunt id for pagination ************************/
    
    /*     *********************currency converter************************/
 public function convert($ssTableName,$asFields) {
          if (strcmp($asFields['action'], "convert") == 0) { 
              $final_array = array();
            $to =  $asFields['to'];
            $from  =  $asFields['from'];
            $url = "http://free.currencyconverterapi.com/api/v5/convert?q=".$from.'_'.$to."&compact=1";
            $json_array = file_get_contents($url);
            $json_data = json_decode($json_array, true);
            $value = $from.'_'.$to;
            $array =  $json_data['results'][$value]['val'];
            $msg = array('result' => '201','msg'=>'Data is shown Succesfully','value'=>$array);
            return $msg;
          
 }
}
/*     *********************currency converter************************/
}
?>