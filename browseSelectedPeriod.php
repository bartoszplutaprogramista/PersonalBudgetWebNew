<?php

	session_start();

    require_once "database.php";

	if ((!isset($_SESSION['loggedIn'])) && ($_SESSION['loggedIn']==false)){
		header('Location: index.php');
		exit();
	}

    $paymentMethod = $_POST['paymentMethod'];

    $queryId = $db->prepare('SELECT id FROM users WHERE username = :userNameSession');	
    $queryId->bindValue(':userNameSession', $_SESSION['userName'], PDO::PARAM_STR);
    $queryId->execute();

    $userId = $queryId->fetch();

	$currentDate=date("Y-m-d");

	$dataHelpYearMonth = date("Y-m");
    $dataHelpCurrentMonth = $dataHelpYearMonth."%";

	$queryId = $db->prepare('SELECT id FROM users WHERE username = :userNameSession');	
    $queryId->bindValue(':userNameSession', $_SESSION['userName'], PDO::PARAM_STR);
    $queryId->execute();

    $userId = $queryId->fetch();

    if($paymentMethod=='currentMonth'){
        $incomesCurrentMonth = $db->prepare('SELECT * FROM incomes WHERE user_id = :user_id AND date_of_income LIKE :dataHelpCurrentMonth ORDER BY date_of_income ASC');	
        $incomesCurrentMonth->bindValue(':user_id', $userId['id'], PDO::PARAM_INT);
		$incomesCurrentMonth->bindValue(':dataHelpCurrentMonth', $dataHelpCurrentMonth, PDO::PARAM_STR);
        $incomesCurrentMonth->execute();
        $incomesOfLoggedUser = $incomesCurrentMonth->fetchAll();

    }
 //   elseif($paymentMethod=='lastMonth') echo 'lastMonth';
//   elseif($paymentMethod=='currentYear') echo 'currentYear';
 //   else echo 'selectedPeriod';
?>
<!DOCTYPE HTML>
<html lang="pl">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	
	<title>Budżet osobisty</title>
	
	<meta name="description" content="Aplikacja Budżet osobisty pomoże Ci w zarządzaniu swoimi wydatkami">
	<meta name="keywords" content="budżet osobisty, wydatki, przychody, zarządzaj swoimi wydatkami">
	<link rel="stylesheet" href="css/bootstrap.min.css" type="text/css">
	<link rel="stylesheet" href="css/style1.css" type="text/css">
	<link rel="stylesheet" href="css/fontello.css" type="text/css">
	<link href='https://fonts.googleapis.com/css?family=Open+Sans:400,700&subset=latin,latin-ext' rel='stylesheet' type='text/css'>
	<link href="https://fonts.googleapis.com/css2?family=Rubik+Distressed&display=swap" rel="stylesheet">
	
</head>

<body>

	
	<div class="container-fluid">
		<header>
			<div class="row justify-content-center m-0 mb-2 p-0">
				<div class="col-xl-12 col-xxl-9">
					<div class="p-0 mt-5 dispaly-as-flex">
							<div class="text-center"><i class="icon-dollar-1"></i></div>
							<div><h1>BUDŻET OSOBISTY</h1></div>
					</div>
					<p class="bo">Aplikacja do zarządzania budżetem osobistym</p>
				</div>
			</div>
			<div class="row class-center col-xl-12 col-xxl-9 p-0"><p class="display-right">Witaj 
			<?php 
				echo $_SESSION['userName'];
			?></p></div>
			<div class="row justify-content-center m-0 p-0">
				<nav class="navbar navbar-expand-xl col-xl-12 col-xxl-9 navbar-light top-bar p-0">
					<div class="container-fluid padding-991"> 
						<a class="navbar-brand text-light mx-0" href="#"></a>
						<button class="navbar-toggler navbar-dark" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
						<span class="navbar-toggler-icon "></span>
						</button>  
						<div class="collapse navbar-collapse" id="navbarSupportedContent">
							<ul class="navbar-nav mx-auto me-auto mb-2 mb-lg-0">
								<li class="nav-item li-change-color">
									<a class="nav-link active text-light py-3" aria-current="page" href="personalBudget.php"><i class="icon-home"></i>Strona główna</a>
								</li>
								<li class="nav-item li-change-color">
									<a class="nav-link active text-light py-3" aria-current="page" href="addIncome.php"><i class="icon-dollar"></i>Dodaj przychód</a>
								</li>
								<li class="nav-item li-change-color">
									<a class="nav-link active text-light py-3" aria-current="page" href="addExpense.php"><i class="icon-basket"></i>Dodaj wydatek</a>
								</li>
								<li class="nav-item li-change-color">
									<a class="nav-link text-light py-3" href="browseTheBalance.php"><i class="icon-chart-bar"></i>Przeglądaj bilans</a>
								</li>
								<li class="nav-item dropdown li-change-color">
									<a class="nav-link dropdown-toggle text-light py-3" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
									<i class="icon-wrench"></i>Ustawienia
									</a>
									<ul class="dropdown-menu dropdown-menu-dark mt-neg" aria-labelledby="navbarDropdown">
										<li class="dropdown-my-color"><a class="dropdown-item text-light py-2" href="#">Zmień adres e-mail</a></li>
										<li class="dropdown-my-color"><a class="dropdown-item text-light py-2" href="#">Zmień hasło</a></li>
									</ul>
								</li>
								<li class="nav-item li-change-color">
									<a class="nav-link active text-light py-3" aria-current="page" href="logOut.php"><i class="icon-logout-1"></i>Wyloguj się</a>
								</li>
							</ul>
						</div>
					</div> 
				</nav>
			</div>
		</header>
		<main>
			<article>
				<div class="row justify-content-center m-0 p-0">
					<div class="col-xl-12 col-xxl-9 p-0">
						<div class="content mt-2 p-4">	
							<h3>PRZEGLĄDAJ BILANS</h3>
				<!--			<p class="p-content p-2 text-center">Zestawienie przychodów w okresie od do </p> -->
                <table class="table-center">
					<thead>
						<tr><th colspan="5">Zestawienie przychodów w okresie od <?= $dataHelpYearMonth."-01" ?> do <?= $currentDate ?></th></tr>
						<tr>
                            <th>Lp</th>
                            <th>Kwota (zł)</th>
                            <th>Data</th>
                            <th>Kategoria</th>
                            <th>Komentarz</th>
                        </tr>
					</thead>
                    <tbody>
						<?php

             //           echo $incomesOfLoggedUser['amount'];
						$i=1;
						foreach ($incomesOfLoggedUser as $incomesUser) {
							echo "<tr>
                                    <td class=\"center-td\">$i</td>
                                    <td>{$incomesUser['amount']}</td>
                                    <td>{$incomesUser['date_of_income']}</td>
									<td>{$incomesUser['income_category_assigned_to_user_id']}</td>
                                    <td>{$incomesUser['income_comment']}</td>
                                </tr>";
								$i++;
						    }
						?>
					</tbody>
					
				</table>                            
						</div>
					</div>
				</div>
			</article>
		</main>
		<footer>
			<div class="row justify-content-center m-0 p-0">	
				<div class="col-xl-12 col-xxl-9">
					<div class="p-2">
						<p>Wszelkie prawa zastrzeżone &copy; 2023 Dziękuję za wizytę!</p>
					</div>
				</div>
			</div>
		</footer>
	</div>
	<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
	<script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
	<script src="src/script.js"></script>
	<script src="js/bootstrap.min.js"></script>
</body>
</html>