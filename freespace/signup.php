<?php
require 'config/database.php';
require './mail.php';

// check if user is already logged in
if (isset($_SESSION['user'])) {
    header("Location: " . ROOT_URL);
    exit;
}

// check if the user has just logged in with Google
if (isset($_GET['code'])) {

    // get the access token from Google
    $params = [
        'client_id' => USER_KEY,
        'client_secret' => GOOGLE_SECRET,
        'redirect_uri' => URI,
        'grant_type' => 'authorization_code',
        'code' => $_GET['code']
    ];

    $ch = curl_init('https://oauth2.googleapis.com/token');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POSTFIELDS => http_build_query($params)
    ]);
    $response = curl_exec($ch);
    curl_close($ch);

    $tokenData = json_decode($response);

    // get user info from Google using the access token
    $url = 'https://www.googleapis.com/oauth2/v2/userinfo?access_token=' . $tokenData->access_token;
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true
    ]);
    $response = curl_exec($ch);
    curl_close($ch);

    $userInfo = json_decode($response);


    // check if username or email already exist in db :
    $user_check_query = "SELECT * FROM users WHERE email = '$userInfo->email'";
    $user_check_result = mysqli_query($connection, $user_check_query);
    if (mysqli_num_rows($user_check_result) > 0) {
        $_SESSION['signup'] = 'Email already exist';
    } else {
        $firstname = $userInfo->given_name;
        $lastname = $userInfo->family_name;
        $username = $userInfo->given_name . rand(100, 999);
        $email = $userInfo->email;
        $password = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 8);
        $hashed_pass = password_hash($password, PASSWORD_DEFAULT);
        $avatar = $userInfo->picture;

        // get image data
        $imageData = file_get_contents($avatar);

        // generate unique file name
        $avatarFileName = time() . '_' . basename($avatar) . '.jpg';

        // set destination path
        $avatarDestinationPath = 'images/' . $avatarFileName;

        // write image data to destination file
        file_put_contents($avatarDestinationPath, $imageData);


        // insert new user into users table
        $insert_google_user_query = "INSERT INTO users (firstname, lastname, username, email, password, avatar, is_admin) VALUES ('$firstname','$lastname','$username','$email','$hashed_pass','$avatarFileName',0)";
        $insert_google_user_result = mysqli_query($connection, $insert_google_user_query);

        if (!mysqli_errno($connection)) {

            //Recipients
            $mail->setFrom(GMAIL, 'Mehdi');
            $mail->addAddress($email);
            // Set the email subject
            $mail->Subject = 'Your Password for Our Website';
            // Set the email body
            $mail->Body = 'Hi ' . $firstname . ',<br><br>Your password for our website is: <b>' . $password . '</b><br><br>Thank you for registering with us.';               //Name is optional
            $mail->send();

            // redirect to login page with success message
            $_SESSION['signup-success'] = "Thank you for registering. We have sent an email with your password.";
            header('location:'.ROOT_URL.'signin.php');
            die();
         } 
        
    }
}

// get back data if there was an error
$signupData = $_SESSION['signup-data'] ?? [];

// unpack signup data
$firstname = $signupData['firstname'] ?? null;
$lastname = $signupData['lastname'] ?? null;
$username = $signupData['username'] ?? null;
$email = $signupData['email'] ?? null;
$createpassword = $signupData['createpassword'] ?? null;
$confirmpassword = $signupData['confirmpassword'] ?? null;

// delete signup data session
unset($_SESSION['signup-data']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home page</title>
    <!-- Style -->
    <link rel="stylesheet" href="<?= ROOT_URL ?>css/style.css">
    <!-- Iconscout CDN -->
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.0/css/line.css">
    <!-- Google Font : Montserrat -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

</head>

<body>

    <section class="form__section">
        <div class="container form__section-container">
            <h2>Sign Up</h2>
            <?php if (isset($_SESSION['signup'])) : ?>
                <div class="alert__message error">
                    <p>
                        <?=
                        $_SESSION['signup'];
                        unset($_SESSION['signup']);
                        ?>
                    </p>

                </div>

            <?php endif ?>
            <div class="social__login-container">
                <h3 style="color:blanchedalmond;font-family:Cambria, Cochin, Georgia, Times, 'Times New Roman', serif">Sign in with :</h3>
                <div class="socials-btns">
                    <div class="google-btn">
                        <div class="google-icon-wrapper">
                            <img class="google-icon" src="https://upload.wikimedia.org/wikipedia/commons/5/53/Google_%22G%22_Logo.svg" />
                        </div>
                        <p class="btn-text"><a href="https://accounts.google.com/o/oauth2/auth?response_type=code&client_id=<?= USER_KEY ?>&redirect_uri=<?= URI ?>&scope=openid%20email%20profile">Sign up with google</a></p>
                    </div>
                </div>
                <h3 style="color:blanchedalmond;font-family:Cambria, Cochin, Georgia, Times, 'Times New Roman', serif">OR</h3>
            </div>
            <hr class="hr-19">

            <form style="margin-bottom: 20px;" action="<?= ROOT_URL ?>signup-logic.php" method="post" enctype="multipart/form-data">
                <input type="text" name="firstname" value="<?= $firstname ?>" placeholder="First name">
                <input type="text" name="lastname" value="<?= $lastname ?>" placeholder="Last name">
                <input type="text" name="username" value="<?= $username ?>" placeholder="Username">
                <input type="email" name="email" value="<?= $email ?>" placeholder="Email">
                <input type="password" name="createpassword" value="<?= $createpassword ?>" placeholder="Create Password">
                <input type="password" name="confirmpassword" value="<?= $confirmpassword ?>" placeholder="Confirm Password">
                <div class="form__control">
                    <label for="avatar"></label>
                    <input type="file" name="avatar" id="avatar">
                </div>
                <button type="submit" name="submit" class="btn">Sign Up</button>
                <small>Already have an account? <a href="<?= ROOT_URL ?>signin.php">Sign In</a></small>
            </form>
        </div>
    </section>


    <script src="main.js"></script>
</body>

</html>