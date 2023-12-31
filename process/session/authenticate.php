<?php 
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        // show 405 error response
        header('HTTP/1.1 405 Method Not Allowed');
        die;
    }else{
       
        $users = file_get_contents(__DIR__ .'/../../data/users.ser');
        $users = unserialize($users);
        foreach ($users as $user) {
            if ($user['email'] === $_POST['email']) {
                if ($user['password'] === sha1($_POST['pw'])) {
                    $logins = unserialize(file_get_contents(__DIR__ . '/../../data/logins.ser'));
                    $logins[] = [
                        'time' => date('Y-m-d H:i:s'),
                        'user' => $user['id'],
                        'status' => 'Login ok',
                    ];
                    file_put_contents(__DIR__ . '/../../data/logins.ser', serialize($logins));

                    session_start();
                    $_SESSION['login'] = '1';
                    $_SESSION['uid'] = $user['id'];
                    $_SESSION['msg'] = 'Loged In successfully.';
                    $_SESSION['msgType'] = 'green';
                    header('Location: http://localhost/bank/index.php');
                    die;
                }
            }
        }

        $logins = unserialize(file_get_contents(__DIR__ . '/../../data/logins.ser'));
        $logins[] = [
            'time' => date('Y-m-d H:i:s'),
            'user' => $user['id'],
            'status' => 'Login failed',
        ];
        file_put_contents(__DIR__ . '/../../data/logins.ser', serialize($logins));
        session_start();
        $_SESSION['msg'] = 'Log In unsuccessfull.';
        $_SESSION['msgType'] = 'red';
        header('Location: http://localhost/bank/index.php?l=baaaad');
        die;
    }