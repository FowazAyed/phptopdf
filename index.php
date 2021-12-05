<?php
$dns = 'mysql:host=localhost;dbname=htmltopdf';
$user = 'root';
$pass = '';
$options = array( PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8');
try {
    $con = new PDO($dns, $user, $pass, $options);
    $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch(PDOException $e) {
    echo 'Failed connect to Database: ' . $e;
}

if($_SERVER["REQUEST_METHOD"] === "POST"){
    if(isset($_POST['firstname']) && isset($_POST['lastname']) && isset($_POST['email'])){
        $firstname = isset($_POST['firstname']) ? filter_var($_POST['firstname'], FILTER_SANITIZE_STRING) : '';
        $lastname = isset($_POST['lastname']) ? filter_var($_POST['lastname'], FILTER_SANITIZE_STRING) : '';
        $email = isset($_POST['email']) ? filter_var($_POST['email'], FILTER_SANITIZE_EMAIL) : '';
        if($firstname === '' || $lastname === '' || $email === '') {
            $formErrors[] = 'All fields are mandatory, you must enter values';
        } else {
            $successMsg = "The data has been added successfully";
            $statement = $con->prepare("INSERT INTO data(`firstname`, `lastname`, `email`) VALUES(:firstname, :lastname, :email)");
            $statement->execute(array(
            'firstname'     => $firstname,
            'lastname'     => $lastname,
            'email'    => $email
            ));
        }   
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>HTML2PDS.js Example</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container">
        <?php
            if(!empty($formErrors)) { ?>
                <div class="alert alert-danger alert-dismissible m-2">
                    <div>
                        <strong>Error!</strong>
                        <ul> 
                            <?php
                            foreach($formErrors as $error):
                                echo '<li>' . $error . '</li>';
                            endforeach ?>
                        </ul>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div> <?php 
            } else if(!empty($successMsg)) {?>
                <div class="alert alert-success  alert-dismissible m-2">
                    <p><strong>Success!</strong> <?php echo $successMsg ?></p>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div> <?php 
            } ?>
        <button class="btn btn-primary m-2" onclick="print()">print</button>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#model">Add Data</button>
        <div class="modal" id="model">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Add User</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="form" action="<?php echo $_SERVER['PHP_SELF']?>" method="post">
                        <div class="m-2">
                            <label for="firstname" class="form-label">First name: </label>
                            <input type="text" class="form-control" id="firstname" placeholder="Enter a first name" name="firstname">
                        </div>
                        <div class="m-2">
                            <label for="lastname" class="form-label">Last name: </label>
                            <input type="text" class="form-control" id="lastname" placeholder="Enter a last name" name="lastname">
                        </div>
                        <div class="m-2">
                            <label for="email" class="form-label">Email: </label>
                            <input type="text" class="form-control" id="email" placeholder="Enter a Email" name="email">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <input form="form" type="submit" class="btn btn-primary m-2" value="Send">
                </div>
            </div>
        </div>
    </div>
    <div class="container" id="root">
        <h1>Users Data</h1>
        <p>Experimental data for printing</p>
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Email</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                    $statement = $con->prepare("SELECT * FROM `data`");
                    $statement->execute();
                    $data = $statement->fetchAll();
                    if(!empty($data)) {
                        foreach($data as $row) {
                            echo "<tr>";
                                echo "<td>" . $row['id'] . "</td>";
                                echo "<td>" . $row['firstname'] . "</td>";
                                echo "<td>" . $row['lastname'] . "</td>";
                                echo "<td>" . $row['email'] . "</td>";
                            echo "</tr>";
                        }
                    }
                ?>
            </tbody>
        </table>
        <img src="./img.jpg" class="img-thumbnail d-block m-auto" alt="this image for test">
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"
        integrity="sha512-GsLlZN/3F2ErC5ifS5QtgpiJtWd43JWSuIgh7mbzZ8zBps+dvLusV+eNQATqgA/HdeKFVgA5v3S/cIrLF7QnIg=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script>
        function print() {
            var content = document.getElementById('root');
            html2pdf().from(content).toPdf().get('pdf').then(function (pdf) {
                window.open(pdf.output('bloburl'), '_blank');
            });
        }
    </script>
</body>

</html>