<?php
$content = $subjects = $semesters = $classes = "";
session_start();
$teacher_mail = $_SESSION['username'];
require_once("db.php");
$teacher_name = "SELECT name FROM professor
          where email = '$teacher_mail'";
          $connect = mysqli_connect(MYSQL_HOST,MYSQL_USER,MYSQL_PASSWORD,MYSQL_DB);
          $result = mysqli_query($connect,$teacher_name);
          $row = mysqli_fetch_row($result);
$teacher_name = $row[0];
$query = "SELECT DISTINCT semester
          FROM professor p INNER JOIN prof_course pc
          ON p.prof_id = pc.prof_id
          INNER JOIN classroom c
          ON pc.class_id = c.class_id
          WHERE p.name = '$teacher_name'
          ORDER BY semester";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    foreach($stmt as $row){
    $semesters .= "<option value='{$row["semester"]}'>{$row["semester"]}</option>";
}
$query = "SELECT p.name AS teacher,c.shortname as subject
          FROM professor p INNER JOIN prof_course pc
          ON p.prof_id = pc.prof_id
          INNER JOIN course c
          ON pc.course_id = c.course_id
          WHERE p.name = '$teacher_name'
          ORDER BY subject";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    foreach($stmt as $row){
    $subjects .= "<option value='{$row["subject"]}'>{$row["subject"]}</option>";
}
$query = "SELECT DISTINCT sec_name
          FROM professor p INNER JOIN prof_course pc
          ON p.prof_id = pc.prof_id
          INNER JOIN classroom c
          ON pc.class_id = c.class_id
          WHERE p.name = '$teacher_name'";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    foreach($stmt as $row){
    $classes .= "<option value='{$row["sec_name"]}'>{$row["sec_name"]}</option>";
}

if(isset($_POST["submit"])) {
$subject = $_POST["subject"];
$semester = $_POST["semester"];
$sec_name = $_POST["sec_name"];
$query = "SELECT s.usn AS USN,s.name AS Name,total_held AS 'NO. OF CLASSES', total_attend AS 'NO. OF CLASSES ATTENDED',
(total_held-total_attend) AS 'NO. OF CLASSES ABSENT',cast(ROUND(((total_attend/total_held)*100),2) as float) AS
'ATTENDANCE PERCENTAGE'
FROM
prof_course pc INNER JOIN course c
ON pc.course_id = c.course_id INNER JOIN attendance a
ON pc.course_id = a.course_id
INNER JOIN student s ON a.student_id = s.student_id
INNER JOIN classroom cr
ON s.class_id = cr.class_id
WHERE shortname = '$subject' AND semester = $semester AND sec_name = '$sec_name'";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    foreach ($stmt as $row) {
      $content .= "<tr>
        <td>{$row["USN"]}</td>
        <td>{$row["Name"]}</td>
        <td>{$row["NO. OF CLASSES"]}</td>
        <td>{$row["NO. OF CLASSES ATTENDED"]}</td>
        <td>{$row["ATTENDANCE PERCENTAGE"]}</td>";
    }
    $content  =
"<h5>Section : $semester-$sec_name &nbsp; &nbsp; &nbsp; Course: $subject</h5>
     <table>
     <tr>
     <th>USN</th>
     <th>Name</th>
     <th>NO. OF CLASSES</th>
     <th>NO. OF CLASSES ATTENDED</th>
     <th>ATTENDANCE PERCENTAGE</th>
     </tr>
     {$content}
     </table>";
  $conn = null;
}
$public_end = strpos($_SERVER['SCRIPT_NAME'], '/Main') + 5;
$doc_root = substr($_SERVER['SCRIPT_NAME'],0,$public_end);
define("WWWW_ROOT",$doc_root);
?>
<!DOCTYPE html>
<html>

<head>
    <title>
        TEACHER HOME PAGE
    </title>
    <link rel="stylesheet" href="teacher.css" type="text/css">
</head>

<body>
    <div class="bg1">
        <nav class="navbar">
            <div class="links">
                <a href="<?php echo WWWW_ROOT . '/Teacher/Homepage/Teacher_home.php' ?> ">Lecture</a>
                <a id="attendence" href="<?php echo WWWW_ROOT . '/Teacher/View_attendance/Teacher-View.php' ?>">View Attendance</a>
                <a href="<?php echo WWWW_ROOT . '/Sign In/Teacher_login.php' ?>">Logout</a>
            </div>
        </nav>
        <div id="logo-cont">
            <img src="./undraw_education_f8ru.svg" >
        </div>
        <div class="container">
            <form method="post" class = "container2" >
              <div>
               <label>Semester
               <select name="semester" ><?php echo $semesters; ?></select>
               </label>

               <label>Subject
               <select name="subject" ><?php echo $subjects; ?></select>
               </label>
               <label>Class
               <select name="sec_name" ><?php echo $classes; ?></select>
               </label>
              </div>
              <div>
               <input type="submit" name="submit">
              </div>
            </form>
             <div>
               <?php echo $content; ?>
             </div>
        </div>
    </div>
  </body>
</html>