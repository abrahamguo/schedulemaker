<?php
	const LOGIN_PAGE = true;
	const PAGE_TYPE = "index";
	require "Head.php";
	if ($_GET["action"] == "Login") Session::login($_GET["UserName"], $_GET["Password"]);
	if ($_GET["logout"]) {
		Session::logout();
		Util::redir("index.php");
	}
	if ($_GET["redirect"] == "nologin")
		echo "<div class='alert alert-warning' role='alert'>Please login to access this page.</div>";
?>

	<div class="row justify-content-center align-items-center" style="height: calc(100vh - 125px)">
		<div class="col-lg-4 col-md-6 col-sm-8 bg-light p-5">

			<p class='text-center'><img class='img-fluid' src='Images/logo_file.png'></p>

			<form>
				<input type='hidden'
					name='action'
					value='Login'
				>

				<div class='form-group'>
					<label for='exampleInputEmail1'>User Name</label>
					<input name='UserName' type='text' class='form-control' id='exampleInputEmail1' aria-describedby='emailHelp' placeholder='Enter Username'>
				</div>
				<div class='form-group'>
					<label for='exampleInputPassword1'>Password</label>
					<input name='Password' type='password' class='form-control' id='exampleInputPassword1' placeholder='Password'>
				</div>
				<button type='submit' class='btn btn-primary'>Log In</button>
			</form>
		</div>
	</div>
<?php require "Foot.php"; ?>