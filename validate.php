<?php
$email = $_POST['email'] ?? '';
$firstname = $_POST['firstname'] ?? '';
$lastname = $_POST['lastname'] ?? '';
$password = $_POST['password'] ?? '';
$confirmpassword = $_POST['confirmpassword'] ?? '';
// $loginemail = $_POST['Email'] ?? '';
// $loginpass = $_POST['Password'] ?? '';

//Checking if any of the registration fields are empty
if(empty($email) || empty($firstname) || empty($lastname) || empty($password) || empty($confirmpassword)){
    echo '<script> alert("Fields should not be empty");</script>';
    exit();
}

//checking if password and confirm password match
if($password !== $confirmpassword){
    echo '<script>alert("Password Mismatch");</script>';
    exit();
}

$conn = new mysqli('localhost:8080', 'root', '', 'user');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} else {
    // Check if email already exists
    $checkStmt = $conn->prepare("SELECT * FROM registration WHERE email = ?");
    $checkStmt->bind_param("s", $email);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();

    if ($checkResult->num_rows > 0) {
        echo '<script>alert("You have already registered");</script>';
        // echo '<script>window.location.href="signin.html";</script>';
        // // Validating sign-in
        // if(empty($loginemail) || empty($loginpass)){
        //     echo '<script>alert("Fields must be filled");</script>';
        //     exit();
        // }
        // // Authenticating user
        // if(($loginemail !== $email) || ($loginpass !== $password)){
        //     echo '<script>alert("Invalid Email ID or Password");</script>';
        //     exit();
        // }
    } else {
        $stmt = $conn->prepare("INSERT INTO registration(firstname, lastname, email, password, confirmpassword) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $firstname, $lastname, $email, $password, $confirmpassword);
        $stmt->execute();

        // Check if the registration was successful
        if ($stmt->affected_rows > 0) {
            // Registration successful, send email to the user
            require 'PHPMailer/PHPMailerAutoload.php';
            $mail = new PHPMailer;

            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com'; // Change this to your SMTP host
            $mail->SMTPAuth = true;
            $mail->Username = 'hariprasadjakkana@gmail.com'; // Your Gmail address
            $mail->Password = 'nbjpknzvrxrnyglt'; // Your Gmail password   //  nbjp knzv rxrn yglt
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            $mail->setFrom('hariprasadjakkana@gmail.com', 'Sri'); // Your Name and your email
            $mail->addAddress($email, $firstname); // User's email and name
            $mail->isHTML(true);

            $mail->Subject = 'Thank You for Registration';
            $mail->Body    = 'Hello ' . $firstname . ',<br><br>Thank you for registering with Stream. We appreciate your interest.<br><br>Regards,<br>Your Host/Admin';

            if (!$mail->send()) {
                echo 'Message could not be sent.';
            } else {
                echo '<script>alert("Account Created Successfully.");</script>';
                echo '<script>window.location.href="index.html";</script>';
            }
        } else {
            echo "Failed to create account.";
        }
        $stmt->close();
    }

    $checkStmt->close();
    $conn->close();
}
?>