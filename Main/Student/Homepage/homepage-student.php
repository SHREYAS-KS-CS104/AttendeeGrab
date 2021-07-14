<?php
ini_set("display_errors", "off");
session_start();
$usn = $_SESSION['username'];
$content = $subjects = "";
require_once("db.php");
$query = "SELECT c.shortname AS subject
        FROM student s INNER JOIN attendance a
        ON s.student_id = a.student_id
        INNER JOIN course c 
        ON a.course_id = c.course_id
        WHERE s.usn = '$usn'
        ORDER BY subject";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    foreach($stmt as $row){
    $subjects .= "<option value='{$row["subject"]}'>{$row["subject"]}</option>";
}
if(isset($_POST["submit"])) {
    $subject = $_POST["subject"];
    $otp = $_POST["otp"];
    $query = "SELECT valid_attendance('$subject',$otp,'$usn')";
    $connect = mysqli_connect(MYSQL_HOST,MYSQL_USER,MYSQL_PASSWORD,MYSQL_DB);
    $result = mysqli_query($connect,$query);
    $code = mysqli_fetch_row($result);
    if ($code[0] == 1){
    $content  =
    "<h5><br> Invalid OTP </h5>";
    }
    if (!$result){
    $content  =
    "<h5><br>No OTP entered</h5>";
    }
    if ($code[0] == 2){
    $content  =
    "<h5><br>Your attendance is marked</h5>";
    }
    if ($code[0] == 3){
    $content  =
    "<h5><br>You can only mark your attendance once.</h5>";
    }
    if (!is_numeric($otp) && !empty($otp)){
    $content  =
    "<h5><br>Your OTP can only contain numbers</h5>";
    }
    $conn = null;
}
$public_end = strpos($_SERVER['SCRIPT_NAME'], '/Main') + 5;
$doc_root = substr($_SERVER['SCRIPT_NAME'],0,$public_end);
define("WWWW2_ROOT",$doc_root);
?>
<!DOCTYPE html>
<html>
<body>
<head>
    <title>
        Student HOME PAGE
    </title>
    <link rel="stylesheet" href="student.css" type="text/css">
</head>

    <div class="bg1">
        <nav class="navbar">       
            <div class="links">
                <a href="<?php echo WWWW2_ROOT . '/Student/Homepage/homepage-student.php' ?>">Lecture</a>
                <a id="attendence" href="<?php echo WWWW2_ROOT . '/Student/View_attendance/Student_View.php' ?>">View Attendance</a>
                <a href="<?php echo WWWW2_ROOT . '/Sign In/Student_login.php' ?>">Logout</a>
            </div>
        </nav>
        <div id="logo-cont">
            <img src="./undraw_education_f8ru.svg" >
        </div>
        <div class="container">
            <form method="post" class = "container2" >
              <div>
               <div>
                   <label>Subject
                   <select name="subject" ><?php echo $subjects; ?></select>
                   </label>
               </div>
                <div>
                 <label><br>
                  <input class ="label12" type="text" name="otp" placeholder="Enter OTP here">
                  </label>
                </div>
                <div>
               <input type="submit" name="submit">
              </div>
            </form>
            <div>
                <?php echo $content; ?>
                <?php mysqli_free_result($result); ?>
            </div>
        </div>
      </div>
    </div>
</body>
</html>