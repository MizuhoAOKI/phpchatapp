<?php
    // avoid the cross-domain constraint
    header("Access-Control-Allow-Origin: *");  
    // avoid the cross-origin resource sharing constraint
    header('Access-Control-Allow-Credentials: true');

    $count = 0;
    $strMsg   = '';

    //// xmlhttprequest is invalid on cross-domain communication
    // $request = '';
    // if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
    //     $request = strtolower($_SERVER['HTTP_X_REQUESTED_WITH']);
    // }
    // if ($request !== 'xmlhttprequest') {
    //     exit;
    // }

    // keep messages as local variables
    $name = '';
    $icon = '';
    $color = '';
    $message = '';
    $sender  = '';
    if (isset($_POST['name']) && is_string($_POST['name'])) {
        $name = htmlspecialchars($_POST['name'], ENT_QUOTES);
    }
    if (isset($_POST['icon']) && is_string($_POST['icon'])) {
        $icon = htmlspecialchars($_POST['icon'], ENT_QUOTES);
    }
    if (isset($_POST['color']) && is_string($_POST['color'])) {
        $color = htmlspecialchars($_POST['color'], ENT_QUOTES);
    }
    if (isset($_POST['message']) && is_string($_POST['message'])) {
        $message = $_POST['message']; // non-sanitize
        // $message = htmlspecialchars($_POST['message'], ENT_QUOTES); //sanitized
        $sender_ip = $_SERVER['REMOTE_ADDR'];  
    }
    if (isset($_POST['sender']) && is_string($_POST['sender'])) {
        $sender = htmlspecialchars($_POST['sender'], ENT_QUOTES);
    }
    // if message was invalid :
    if ($name == '' || $icon == '' || $color == '' || $message == '' || $sender == '') {
        exit;
    }

    $previous_id = 0;
    $fp = fopen('message.log', 'r');
    if (flock($fp, LOCK_SH)) {
        while (!feof($fp)) {
            if ($count > 200) {
                break;
            }
            $strMsg = $strMsg . fgets($fp);
            if($count==0){
                $elements = explode(",", $strMsg);
                $previous_id = (int)$elements[0];
            }
            $count = $count + 1;
        }
    }
    // calc latest_id referring to the previous one.
    $latest_id = $previous_id + 1;
    // close file
    flock($fp, LOCK_UN);
    fclose($fp);
    // update $strMsg 
    $strMsg = $latest_id . ',' . $name . ',' . $icon . ',' . $color . ',' . $sender . ',' . date("Y-m-d H:i:s") . ',' . $message .",\n". $strMsg;
    file_put_contents('message.log', $strMsg, LOCK_EX);

    // tell javascript that the process have successfully finished.
    echo "succeed";
?>
