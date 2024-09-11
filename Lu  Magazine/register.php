<?php
// Include config file
require_once "config.php";
 
// Define variables and initialize with empty values
$username = $password = $confirm_password = "";
$username_err = $password_err = $confirm_password_err = "";

// Define regex patterns
$email_pattern = "/^[a-zA-Z0-9._%+-]+@([a-zA-Z0-9.-]+\.[a-zA-Z]{2,})$/";
$password_pattern = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/";

// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){

    // Validate username (email)
    if(empty(trim($_POST["username"]))){
        $username_err = "Please enter an email address.";
    } elseif (!preg_match($email_pattern, trim($_POST["username"]))) {
        $username_err = "Please enter a valid email address.";
    } else{
        // Prepare a select statement
        $sql = "SELECT id FROM users WHERE username = ?";
        
        if($stmt = mysqli_prepare($con, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_username);
            
            // Set parameters
            $param_username = trim($_POST["username"]);
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                /* store result */
                mysqli_stmt_store_result($stmt);
                
                if(mysqli_stmt_num_rows($stmt) == 1){
                    $username_err = "This email address is already taken.";
                } else{
                    $username = trim($_POST["username"]);
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }
        }
         
        // Close statement
        mysqli_stmt_close($stmt);
    }
    
    // Validate password
    if(empty(trim($_POST["password"]))){
        $password_err = "Please enter a password.";     
    } elseif(!preg_match($password_pattern, trim($_POST["password"]))){
        $password_err = "Password must be at least 8 characters long and include uppercase, lowercase letters, numbers, and special characters.";
    } else{
        $password = trim($_POST["password"]);
    }
    
    // Validate confirm password
    if(empty(trim($_POST["confirm_password"]))){
        $confirm_password_err = "Please confirm password.";     
    } else{
        $confirm_password = trim($_POST["confirm_password"]);
        if(empty($password_err) && ($password != $confirm_password)){
            $confirm_password_err = "Password did not match.";
        }
    }
    
    // Check input errors before inserting in database
    if(empty($username_err) && empty($password_err) && empty($confirm_password_err)){
        
        // Prepare an insert statement
        $sql = "INSERT INTO users (username, password) VALUES (?, ?)";
         
        if($stmt = mysqli_prepare($con, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "ss", $param_username, $param_password);
            
            // Set parameters
            $param_username = $username;
            $param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Redirect to login page
                header("location: login.php");
            } else{
                echo "Something went wrong. Please try again later.";
            }
        }
         
        // Close statement
        mysqli_stmt_close($stmt);
    }
    
    // Close connection
    mysqli_close($con);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - LU Magazine</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
    <style>
        body { font: 14px sans-serif; }
        .container { min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .card { max-width: 500px; width: 100%; }
        .form-group { margin-bottom: 1rem; }
        .form-text { color: #dc3545; }
    </style>
</head>
<body>
   
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <!-- Add your navbar content here -->
    </nav>

    <div class="container">
        <div class="card shadow">
            <div class="card-body">
                <h3 class="text-center">Sign Up</h3>
                <hr>
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <div class="mb-3 <?php echo (!empty($username_err)) ? 'text-danger' : ''; ?>">
                        <label for="username" class="form-label">Email</label>
                        <input type="text" id="username" name="username" class="form-control" value="<?php echo $username; ?>">
                        <div class="form-text"><?php echo $username_err; ?></div>
                    </div>
                    <div class="mb-3 <?php echo (!empty($password_err)) ? 'text-danger' : ''; ?>">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" id="password" name="password" class="form-control">
                        <div class="form-text"><?php echo $password_err; ?></div>
                    </div>
                    <div class="mb-3 <?php echo (!empty($confirm_password_err)) ? 'text-danger' : ''; ?>">
                        <label for="confirm_password" class="form-label">Confirm Password</label>
                        <input type="password" id="confirm_password" name="confirm_password" class="form-control">
                        <div class="form-text"><?php echo $confirm_password_err; ?></div>
                    </div>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-success">Submit</button>
                    </div>
                    <p class="mt-3 d-flex justify-content-evenly">Already have an account? <a href="../login.php">Login here</a>.</p>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
