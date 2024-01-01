<?php
session_start();
if (isset($_SESSION['login']) && ($_SESSION['login'] === '1' )){ 
    
} else {
    header("HTTP/1.1 401 Unauthorized");
    die;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        // show 405 error response
        header('HTTP/1.1 405 Method Not Allowed');
        die;
} else {
        $pw1 = $_POST['pw1'] ?? '';
        $pw2 = $_POST['pw2'] ?? '';

        $err ='';

        
        if ($pw1 === '' || $pw2 === ''){
                $err .= 'All fields are required.<br/>';
        }elseif ($pw1 !== $pw2) {
                $err .= 'Passwords do not match.<br/>';
        }
        
        if ($err === ''){
            // save user details
            $users = unserialize(file_get_contents(__DIR__ . '/../../data/users.ser'));
            // foreach($users as $user){
            foreach($users as $key=>$value){
                if ($users[$key]['id'] === $_SESSION['uid']){
                    $users[$key]['pw'] = sha1($pw1);
                }
            }
            
            file_put_contents(__DIR__ . '/../../data/users.ser',  serialize($users));
                            
            //Create Success message
            
            $_SESSION['msg'] = 'Changes saved successfully.';
            $_SESSION['msgType'] = 'green';
           
            // echo '<pre>';
            // print_r($users);
            // echo '</pre>';
            // Go to login page
            header('location: http://localhost/bank/index.php?p=userchangepw');
    } else {
            // create error message
            
            $_SESSION['msg'] = 'Error: ' . $err;
            $_SESSION['msgType'] = 'red';
            // echo 'Klaida: ' . $err;
            // echo '<pre>';
            // print_r($users);
            // echo '</pre>';
            // Go back to form page
            
            header('location: http://localhost/bank/index.php?p=userchangepw');
    }
}

die;

?>