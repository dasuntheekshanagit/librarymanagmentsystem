<?php
// Database connection credentials
$host = 'localhost';
$dbUsername = 'root';
$dbPassword = '';
$dbName = 'library_management_system';

// Establish a database connection
$conn = new mysqli($host, $dbUsername, $dbPassword, $dbName);

// Check if the connection was successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the registration form was submitted
if (isset($_POST['submit'])) {
    // Retrieve form data and sanitize/validate if necessary
    $username = $_POST['username'];
    $password = $_POST['password'];
    $firstName = $_POST['first_name'];
    $lastName = $_POST['last_name'];
    $address = $_POST['address'];
    $phoneNumber = $_POST['phone_number'];
    $email = $_POST['email'];

    // Hash the password before storing in the database
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    // Prepare the SQL statement to insert username and password into the members table
    $sql = "INSERT INTO members (username, password) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $username, $hashedPassword);

    // Execute the SQL statement to insert username and password into the members table
    if ($stmt->execute()) {
        // Registration successful for the members table, now insert the borrower profile data
        $memberID = $stmt->insert_id;
        $stmt->close();

        // Prepare the SQL statement to insert borrower profile data into the borrowers table
        $sql = "INSERT INTO borrower (BorrowerID, FirstName, LastName, Address, PhoneNumber, Email) 
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isssss", $memberID, $firstName, $lastName, $address, $phoneNumber, $email);

        // Execute the SQL statement to insert borrower profile data into the borrowers table
        if ($stmt->execute()) {
            // Registration successful for both tables
            $successMessage = "Registration successful. BorrowerID: " . $stmt->insert_id;
        } else {
            // Registration failed for borrowers table
            $errorMessage = "Error: Unable to register. Please try again.";
        }
    } else {
        // Registration failed for members table
        $errorMessage = "Error: Unable to register. Please try again.";
    }

    $stmt->close();
}

$conn->close();
?>