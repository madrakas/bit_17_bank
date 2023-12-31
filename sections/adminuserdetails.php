<?php  if (isset($_SESSION['login']) && ($_SESSION['login'] === '1' )){
        $admins = unserialize(file_get_contents(__DIR__ . '/../data/admins.ser'));
        if (in_array($_SESSION['uid'], $admins)){
            $isAdmin = true;
            if(isset($_GET['usr'])){
                $users = unserialize(file_get_contents(__DIR__ . '/../data/users.ser'));
                $userExists = false;
                foreach($users as $user) {

                    if (strval($user['id']) === strval($_GET['usr'])){
                        $userExists = true;
                        $uid = $user['id'];
                        $ufname = $user['firstname'];
                        $ulname = $user['lastname'];
                        $uak = $user['ak'];
                        $uemail = $user['email'];
                    }
                }
                if (!$userExists){
                    header('Location: http://localhost/bank/index.php?p=adminlistusers');
                    die;
                }
            }else{
                header('Location: http://localhost/bank/index.php?p=adminlistusers');
                die;
            }
        } else {
            header('Location: http://localhost/bank/index.php?p=accountsview');
            die;
        }
    } else {
        header('Location: http://localhost/bank/index.php');
        die;
    }
?>

<h1>User Details</h1>
<?php
    echo '<div class="acc_row">';
    echo '<div>ID: ' .  $uid . '</div>';
    echo '</div>';
    echo '<div class="details-head">Personal data</div>';
    echo '<div class="detail-row">';
    echo '<div><p><strong>First Name: </strong>'. $ufname .'</p>';
    echo '<p><strong>Last Name: </strong>'. $ulname .'</p>';
    echo '<p><strong>Email: </strong>'. $uemail .'</p>';
    echo '<p><strong>Personal identificartion code: </strong>'. $uak .'</p>';
    echo '<p><a href="http://localhost/bank/index.php?p=useredit&usr='. $uid .'">Edit data</a> <a href="http://localhost/bank/index.php?p=userchangepw&usr='. $uid .'">Change password</a> <a href="http://localhost/bank/index.php?p=usercloseacc&usr='. $uid .'">Delete user</a></p></div>';
    echo '</div>';
    echo '<div class="details-head">Money accounts</div>';

    $accounts = unserialize(file_get_contents(__DIR__ . '/../data/accounts.ser'));
    $accounts = array_filter($accounts, fn($account) => $account['uid'] === $uid);
    $i = 0;
    foreach ($accounts as $account) {
        echo '<div class="detail-row">';
        echo '<div>' . ++$i . '.</div>';
        echo '<div>' .  $account['iban'] . ' | ' . $account['amount'] . ' ' . $account['currency'] . ' | <a href="http://localhost/bank/index.php?p=adminaccaddfunds&accid='. $account['id'] .'">Add funds</a> | <a href="http://localhost/bank/index.php?p=adminaccremfunds&accid='. $account['id'] .'">Withdraw funds</a> | <a href="http://localhost/bank/index.php?p=admintransactions&usr='. $uid .'">View transactions</a> | <a href="http://localhost/bank/index.php?p=accountsdel&acc='. $account['id'] .'">Delete money account</a></div>';
        echo '</div>';
    }

?>
<h2>More actions</h2>
<p><a href="http://localhost/bank/index.php?p=accountsadd&usr=<?= $uid ?>">Add money account</a> | <a href=http://localhost/bank/index.php?p=adminlogins&usr=<?= $uid?>">Show logins log</a></p>
