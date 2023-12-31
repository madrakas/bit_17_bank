<?php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        // show 405 error response
        header('HTTP/1.1 405 Method Not Allowed');
        die;
} else {
    
    session_start();
    if (isset($_SESSION['login']) && ($_SESSION['login'] === '1' )){ 
        if (isset($_POST['usr'])){
            $uid = intval($_POST['usr']);
            //Check if user is admin
            $isAdmin = false;
            $admins = unserialize(file_get_contents(__DIR__ . '/../../data/admins.ser'));
            if (in_array($_SESSION['uid'], $admins)){
                $isAdmin = true;
            }
            if (!$isAdmin){
                header('Location: http://localhost/bank/index.php');
                die;
            } else {
                $returnVar = '&usr=' . $uid;
            }
        } else {
            $uid = $_SESSION['uid'];
            $returnVar = '';
        }

        $firstname = $_POST['firstname'] ?? '';
        $lastname = $_POST['lastname'] ?? '';
        $ak = $_POST['ak'] ?? '';
        $email = $_POST['email'] ?? '';
        $pw1 = $_POST['pw1'] ?? '';
        $pw2 = $_POST['pw2'] ?? '';
        
        $err ='';
        
        $users = unserialize(file_get_contents(__DIR__ . '/../../data/users.ser'));
        $users = array_filter($users, fn($user) => ($user['id'] !== $uid));

        if ($firstname === '' || $lastname === '' || $ak === '' || $email === ''){
                $err .= 'All fields are required.<br/>';
        }elseif ($pw1 !== $pw2) {
                $err .= 'Passwords do not match.<br/>';
        } elseif(!validPersonalCode($ak)){
                $err .= 'Invalid Personal identification number.<br/>';
        } elseif (count(array_filter($users, fn($user) => ($user['email'] === $email))) > 0) {
                $err .= 'User with same Email already exists.<b/r>.';
        } elseif (count(array_filter($users, fn($user) => ($user['ak'] === $ak))) > 0){
                $err .= 'User with same Personal identification number already exists.<b/r>';
        } elseif (strlen($firstname) < 4){
            $err .= 'First name must be 4 letters or longer';
        } elseif (strlen($lastname) < 4){
                $err .= 'Last name must be 4 letters or longer';
        }
        
        if ($err === ''){
                // save user details
                $users = unserialize(file_get_contents(__DIR__ . '/../../data/users.ser'));
                // foreach($users as $user){
                foreach($users as $key=>$value){
                    if ($users[$key]['id'] === $uid){
                        $users[$key]['firstname'] = $firstname;
                        $users[$key]['lastname'] = $lastname;
                        $users[$key]['email'] = $email;
                        $users[$key]['ak'] = $ak;
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
                header('location: http://localhost/bank/index.php?p=useredit' . $returnVar);
        } else {
                // create error message
                
                $_SESSION['msg'] = 'Error: ' . $err;
                $_SESSION['msgType'] = 'red';
                // echo 'Klaida: ' . $err;
                // echo 'uid: '. $uid;
                // echo '<pre>';
                // print_r($users);
                // echo '</pre>';
                // Go back to form page
                
                header('location: http://localhost/bank/index.php?p=useredit' . $returnVar . '&firstname=' . $firstname . '&lastname=' . $lastname . '&ak='. $ak . '&email=' . $email);
        }

    } else {
        header('Location: http://localhost/bank/index.php');
        die;
    }
}

die;

function validPersonalCode($code): bool
{
    if (strlen($code) === 11) {
        if ($code[0] >= 1 && $code[0] <= 6) {
            if (checkdate(substr($code, 3, 2), substr($code, 5, 2), substr($code, 1, 2))) {
                $s = $code[0] * 1 + $code[1] * 2 + $code[2] * 3 + $code[3] * 4 + $code[4] * 5 + $code[5] * 6 + $code[6] * 7 + $code[7] * 8 + $code[8] * 9 + $code[9] * 1;
                if ($s % 11 === 10) {
                    $s = $code[0] * 3 + $code[1] * 4 + $code[2] * 5 + $code[3] * 6 + $code[4] * 7 + $code[5] * 8 + $code[6] * 9 + $code[7] * 1 + $code[8] * 2 + $code[9] * 3;
                    if ($s % 11 === 10 && $s % 11 == $code[10]) {
                        return true;
                    } elseif ($s % 11 == $code[10]) {
                        return true;
                    }
                } elseif ($s % 11 == $code[10]) {
                    return true;
                }
            }
        }
    }
    return false;
}

?>