<?php
require 'config/database.php';

if (isset($_POST['submit'])) {
    $username_email = filter_var($_POST['username_email'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $password = filter_var($_POST['password'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        http_response_code(401);
        exit('Invalid or missing CSRF token');
    }

    // Verify the reCAPTCHA response

    // 1. Get the reCAPTCHA response token from the POST request
    $recaptcha_response = $_POST['g-recaptcha-response'];

    // 2. Set the URL for the reCAPTCHA API endpoint
    $url = 'https://www.google.com/recaptcha/api/siteverify';

    // 3. Set the parameters for the API call
    $data = array(
        'secret' => '6Ldy_xklAAAAALvbIra8n3e1ryxCth7zzBsoiR_b',
        'response' => $recaptcha_response
    );

    // 4. Set the options for the API call
    $options = array(
        'http' => array(
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => http_build_query($data),
        ),
    );

    // 5. Create a stream context with the options
    $context  = stream_context_create($options);

    // 6. Send the API call and get the response
    $result = file_get_contents($url, false, $context);

    // 7. Parse the JSON response
    $json = json_decode($result);

    // 8. Check if the reCAPTCHA verification was successful
    if (!$json->success) {
        // 9. If verification failed, store an error message in the session
        $_SESSION['signin'] = 'Please complete the reCAPTCHA to prove you are not a robot.';
        $_SESSION['signin-data'] = $_POST;

        // 10. Redirect the user to the sign-in page
        header('Location: ' . ROOT_URL . 'signin.php');
        exit;
    }




    if (!$username_email) {
        $_SESSION['signin'] = 'Username or Email required';
    } elseif (!$password) {
        $_SESSION['signin'] = 'Password Required';
    } else {
        //fetch user from database
        $fetch_user_query = "SELECT * FROM users WHERE username='$username_email' OR email = '$username_email'";
        $fetch_user_result = mysqli_query($connection, $fetch_user_query);

        if (mysqli_num_rows($fetch_user_result) == 1) {
            //convert the record into assoc array
            $user_record = mysqli_fetch_assoc($fetch_user_result);
            $db_password = $user_record['password'];
            // compare passwords
            if (password_verify($password, $db_password)) {
                // set session for access control
                $_SESSION['user-id'] = $user_record['id'];
                // set session if user is admin
                if ($user_record['is_admin'] == 1) {
                    $_SESSION['user_is_admin'] = true;
                }
                // log user in
                header('location:' . ROOT_URL . 'admin/');
            } else {
                $_SESSION['signin'] = 'Please check your password';
            }
        } else {
            $_SESSION['signin'] = 'User not found';
        }
    }
    // if any problem, direct back to sign page with signin details
    if (isset($_SESSION['signin'])) {
        $_SESSION['signin-data'] = $_POST;
        header('location:' . ROOT_URL . 'signin.php');
        die();
    }
} else {
    header('location:' . ROOT_URL . 'signin.php');
    die();
}
