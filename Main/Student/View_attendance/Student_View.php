<?php
 session_start();
 $content = "";
 $usn = $_SESSION['username'];
 require_once("db.php");
 $query =
 "SELECT c.name as Subject, total_held AS 'NO. OF CLASSES', total_attend AS 'NO. OF CLASSES ATTENDED',(total_held-total_attend) AS 'NO. OF CLASSES ABSENT',
    cast(ROUND(((total_attend/total_held)*100),2) as float) AS
    'ATTENDANCE PERCENTAGE'
    FROM course c NATURAL JOIN attendance a
    INNER JOIN student s
    ON a.student_id = s.student_id
    WHERE usn = '$usn'";
 $stmt = $conn->prepare($query);
 $stmt -> execute();
 foreach($stmt as $row){
  $content .=
  "<tr>
   <td>{$row["Subject"]}</td>
   <td>{$row["NO. OF CLASSES"]}</td>
   <td>{$row["NO. OF CLASSES ATTENDED"]}</td>
   <td>{$row["NO. OF CLASSES ABSENT"]}</td>
   <td>{$row["ATTENDANCE PERCENTAGE"]}</td>
  </tr>";
  }
 $content =
  "<table>
    <tr>
     <th>Subject</th>
     <th>No. Of classes</th>
     <th>No. Of classes attended</th>
     <th>No. Of classes absent</th>
     <th>Attendance percentage</th>
    </tr>
    {$content}
  </table>";
  $conn = null;
  $public_end = strpos($_SERVER['SCRIPT_NAME'], '/Main') + 5;
  $doc_root = substr($_SERVER['SCRIPT_NAME'],0,$public_end);
  define("WWWW2_ROOT",$doc_root);
  ?>
<!DOCTYPE html>
<html>

<head>
    <title>
        Student home page
    </title>
    <link rel="stylesheet" href="main.css" type="text/css">
    <script src="js/login.js"></script>
</head>

<body>
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
        <div class="container-area">
            <div class="container">
            <h3><?php echo $usn ?></h3>
            <div> <?php echo $content ?> </div>
                </div>
                </form>
            </div>
        </div>
    </div>

</body>

</html>