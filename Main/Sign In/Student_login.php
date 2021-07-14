<?php
ob_start();
session_start();
require_once('db.php');
$public_end = strpos($_SERVER['SCRIPT_NAME'], '/Main') + 5;
$doc_root = substr($_SERVER['SCRIPT_NAME'],0,$public_end);
define("WWW_ROOT",$doc_root);
$errors = [];
$username = '';
$password = '';
$connect = mysqli_connect(MYSQL_HOST,MYSQL_USER,MYSQL_PASSWORD,MYSQL_DB);

function is_post_request() {
  return $_SERVER['REQUEST_METHOD'] == 'POST';
}
function redirect_to($location) {
  header("Location: " . $location);
  exit;
}
function url_for($script_path) {
  if($script_path[0] != '/') {
    $script_path = "/" . $script_path;
  }
  return WWW_ROOT . $script_path;
}
function h($string="") {
  return htmlspecialchars($string);
}
function display_errors($errors=array()) {
  $output = '';
  if(!empty($errors)) {
    $output .= "<div class=\"errors\">";
    $output .= "Please fix the following errors:";
    $output .= "<ul>";
    foreach($errors as $error) {
      $output .= "<li>" . h($error) . "</li>";
    }
    $output .= "</ul>";
    $output .= "</div>";
  }
  return $output;
}
function confirm_result_set($result_set) {
  if (!$result_set) {
  	exit("Database query failed.");
  }
}
function find_student_by_username($username) {
  global $connect;

  $sql = "SELECT * FROM stu_login ";
  $sql .= "WHERE stu_user_id = (SELECT student_id FROM student
  WHERE USN ='" . mysqli_real_escape_string($connect,$username) . "') ";
  $sql .= "LIMIT 1";
  $result = mysqli_query($connect, $sql);
  confirm_result_set($result);
  $student = mysqli_fetch_assoc($result);
  mysqli_free_result($result);
  return $student;
}
if(is_post_request()) {

  $username = $_POST['username'] ?? '';
  $password = $_POST['password'] ?? '';
  $student = find_student_by_username($username);
  if($student){
  	if($password == $student['password']){
      $_SESSION['username'] = $username;
      redirect_to(url_for('/Student/Homepage/homepage-student'));
    } else {
    	print '<script type="text/javascript">window.location.replace( "./Student_login.php");alert("Wrong password"); </script>';
    }
  }
  else{
  	print '<script> type="text/javascript">alert("The College ID is invalid"); </script>';
  }
}
display_errors($errors);
?>

<?php $page_title = 'Log in'; ?>

<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Student Sign in</title>

	<link href='https://fonts.googleapis.com/css?family=Source+Sans+Pro:400,700' rel='stylesheet' type='text/css'>

	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.11.2/css/all.min.css">
	<link rel="stylesheet" href="css/main.css">
	<img src="undraw_Collaboration_re_vyau.svg" class="image" alt="" />
</head>
<body>

	<div class="container">
		<form class="registration" id="registration" action="Student_login.php" method="post">
			<h1><a href="<?php echo WWW_ROOT . '/Sign In/Teacher_login.php' ?>" style="text-decoration:none;">STUDENT FORM</a></h1>

			<label for="username">
				Username
				<input type="text" name = "username" id="username" minlength="3" required>
				<ul class="inputRequirements">
					<li>Student of BMSCE</li>
					<li>Valid College id(USN)</li>
				</ul>
			</label>

			<label for="password">
				Password
				<input type="password" name = "password"id="password" maxlength="100" minlength="4" required>
			</label>

			<input type="submit" name="submit">
		</form>
	</div>
    <script>

function CustomValidation(input) {
	this.invalidities = [];
	this.validityChecks = [];

	//add reference to the input node
	this.inputNode = input;

	//trigger method to attach the listener
	this.registerListener();
}

CustomValidation.prototype = {
	addInvalidity: function(message) {
		this.invalidities.push(message);
	},
	getInvalidities: function() {
		return this.invalidities.join('. \n');
	},
	checkValidity: function(input) {
		for ( var i = 0; i < this.validityChecks.length; i++ ) {

			var isInvalid = this.validityChecks[i].isInvalid(input);
			if (isInvalid) {
				this.addInvalidity(this.validityChecks[i].invalidityMessage);
			}

			var requirementElement = this.validityChecks[i].element;

			if (requirementElement) {
				if (isInvalid) {
					requirementElement.classList.add('invalid');
					requirementElement.classList.remove('valid');
				} else {
					requirementElement.classList.remove('invalid');
					requirementElement.classList.add('valid');
				}

			}
		}
	},
	checkInput: function() {

		this.inputNode.CustomValidation.invalidities = [];
		this.checkValidity(this.inputNode);

		if ( this.inputNode.CustomValidation.invalidities.length === 0 && this.inputNode.value !== '' ) {
			this.inputNode.setCustomValidity('');
		} else {
			var message = this.inputNode.CustomValidation.getInvalidities();
			this.inputNode.setCustomValidity(message);
		}
	},
	registerListener: function() {

		var CustomValidation = this;

		this.inputNode.addEventListener('keyup', function() {
			CustomValidation.checkInput();
		});


	}

};

var usernameValidityChecks = [
	{
		isInvalid: function(input) {
			return !input.value.match(/^1BM/g);
		},
		invalidityMessage: 'Only student of BMSCE can log in',
		element: document.querySelector('label[for="username"] .inputRequirements li:nth-child(1)')
	},
	{
		isInvalid: function(input) {
			return !input.value.match(/^1BM\d\d[a-zA-Z][a-zA-Z]\d\d\d$/g);
		},
		invalidityMessage: 'Please enter a valid USN',
		element: document.querySelector('label[for="username"] .inputRequirements li:nth-child(2)')
	},
];


var usernameInput = document.getElementById('username');
var passwordInput = document.getElementById('password');
var passwordRepeatInput = document.getElementById('password_repeat');

usernameInput.CustomValidation = new CustomValidation(usernameInput);
usernameInput.CustomValidation.validityChecks = usernameValidityChecks;

passwordInput.CustomValidation = new CustomValidation(passwordInput);
passwordInput.CustomValidation.validityChecks = passwordValidityChecks;

passwordRepeatInput.CustomValidation = new CustomValidation(passwordRepeatInput);
passwordRepeatInput.CustomValidation.validityChecks = passwordRepeatValidityChecks;

var inputs = document.querySelectorAll('input:not([type="submit"])');
var submit = document.querySelector('input[type="submit"');
var form = document.getElementById('registration');

function validate() {
	for (var i = 0; i < inputs.length; i++) {
		inputs[i].CustomValidation.checkInput();
	}
}

submit.addEventListener('click', validate);
form.addEventListener('submit', validate);


    </script>
</body>
</html>
