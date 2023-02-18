<?php

	session_start();

    require_once "database.php";

	if ((!isset($_SESSION['loggedIn'])) && ($_SESSION['loggedIn']==false)){
		header('Location: personal-budget');
		exit();
	}

    $paymentMethod = $_POST['paymentMethod'];

	$currentDate=date("Y-m-d");

	$dataHelpYearMonth = date("Y-m");
	$dateCurrentYear = date("Y");
    $dataHelpCurrentMonth = $dataHelpYearMonth."%";

    if($paymentMethod=='currentMonth'){

		$dateFromTo = $dataHelpYearMonth."-01 do ".$currentDate; 

		$queryNameIncome = $db->prepare('SELECT * FROM incomes_category_assigned_to_users INNER JOIN incomes ON incomes_category_assigned_to_users.id = incomes.income_category_assigned_to_user_id WHERE incomes.user_id = :userId AND date_of_income LIKE :dataHelpCurrentMonth ORDER BY date_of_income ASC');
		$queryNameIncome->bindValue(':userId', $_SESSION['userId'], PDO::PARAM_INT);
		$queryNameIncome->bindValue(':dataHelpCurrentMonth', $dataHelpCurrentMonth, PDO::PARAM_STR);
		$queryNameIncome->execute();

		$queryName = $queryNameIncome->fetchAll();

		$queryNameExpense = $db->prepare('SELECT 
		ex.amount AS amn,
		ex.date_of_expense AS dateExp,
		pay.name AS pay,
		exCat.name AS excategory,
		ex.expense_comment AS comment
		FROM expenses_category_assigned_to_users AS exCat 
		INNER JOIN expenses AS ex ON exCat.id = ex.expense_category_assigned_to_user_id 
		INNER JOIN payment_methods_assigned_to_users AS pay ON ex.payment_method_assigned_to_user_id = pay.id
		WHERE ex.user_id = :userId AND date_of_expense LIKE :dataHelpCurrentMonth 
		ORDER BY date_of_expense ASC');
		$queryNameExpense->bindValue(':userId', $_SESSION['userId'], PDO::PARAM_INT);
		$queryNameExpense->bindValue(':dataHelpCurrentMonth', $dataHelpCurrentMonth, PDO::PARAM_STR);
		$queryNameExpense->execute();

		$queryExpense = $queryNameExpense->fetchAll();


	} elseif ($paymentMethod=='lastMonth'){

		$timeHowManyDays = date('t', strtotime("-1 MONTH"));
		$timeMonth = date('m', strtotime("-1 MONTH"));
		$timeYear = date('Y', strtotime("-1 MONTH"));

		$dateFromTo = $timeYear."-".$timeMonth."-01 do ".$timeYear."-".$timeMonth."-".$timeHowManyDays;
		
		$fullDateLastMonth = $timeYear."-".$timeMonth."%";

		$queryNameIncome = $db->prepare('SELECT * FROM incomes_category_assigned_to_users INNER JOIN incomes ON incomes_category_assigned_to_users.id = incomes.income_category_assigned_to_user_id WHERE incomes.user_id = :userId AND date_of_income LIKE :dataHelpLastMonth ORDER BY date_of_income ASC');
		$queryNameIncome->bindValue(':userId', $_SESSION['userId'], PDO::PARAM_INT);
		$queryNameIncome->bindValue(':dataHelpLastMonth', $fullDateLastMonth, PDO::PARAM_STR);
		$queryNameIncome->execute();

		$queryName = $queryNameIncome->fetchAll();
		
		$queryNameExpense = $db->prepare('SELECT 
		ex.amount AS amn,
		ex.date_of_expense AS dateExp,
		pay.name AS pay,
		exCat.name AS excategory,
		ex.expense_comment AS comment
		FROM expenses_category_assigned_to_users AS exCat 
		INNER JOIN expenses AS ex ON exCat.id = ex.expense_category_assigned_to_user_id 
		INNER JOIN payment_methods_assigned_to_users AS pay ON ex.payment_method_assigned_to_user_id = pay.id
		WHERE ex.user_id = :userId AND date_of_expense LIKE :dataHelpLastMonth 
		ORDER BY date_of_expense ASC');
		$queryNameExpense->bindValue(':userId', $_SESSION['userId'], PDO::PARAM_INT);
		$queryNameExpense->bindValue(':dataHelpLastMonth', $fullDateLastMonth, PDO::PARAM_STR);
		$queryNameExpense->execute();

		$queryExpense = $queryNameExpense->fetchAll();
	} elseif ($paymentMethod=='currentYear'){

		$dateFromTo = 	$dateCurrentYear."-01-01 do ".$currentDate;
		$fullDateCurrentYear = $dateCurrentYear."%";

		$queryNameIncome = $db->prepare('SELECT * FROM incomes_category_assigned_to_users INNER JOIN incomes ON incomes_category_assigned_to_users.id = incomes.income_category_assigned_to_user_id WHERE incomes.user_id = :userId AND date_of_income LIKE :dataHelpCurrentYear ORDER BY date_of_income ASC');
		$queryNameIncome->bindValue(':userId', $_SESSION['userId'], PDO::PARAM_INT);
		$queryNameIncome->bindValue(':dataHelpCurrentYear', $fullDateCurrentYear, PDO::PARAM_STR);
		$queryNameIncome->execute();

		$queryName = $queryNameIncome->fetchAll();

		$queryNameExpense = $db->prepare('SELECT 
		ex.amount AS amn,
		ex.date_of_expense AS dateExp,
		pay.name AS pay,
		exCat.name AS excategory,
		ex.expense_comment AS comment
		FROM expenses_category_assigned_to_users AS exCat 
		INNER JOIN expenses AS ex ON exCat.id = ex.expense_category_assigned_to_user_id 
		INNER JOIN payment_methods_assigned_to_users AS pay ON ex.payment_method_assigned_to_user_id = pay.id
		WHERE ex.user_id = :userId AND date_of_expense LIKE :dataHelpCurrentYear 
		ORDER BY date_of_expense ASC');
		$queryNameExpense->bindValue(':userId', $_SESSION['userId'], PDO::PARAM_INT);
		$queryNameExpense->bindValue(':dataHelpCurrentYear', $fullDateCurrentYear, PDO::PARAM_STR);
		$queryNameExpense->execute();

		$queryExpense = $queryNameExpense->fetchAll();
	} else {
		header('Location: browse-selected-period-from-to');
	}

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
									<a class="nav-link active text-light py-3" aria-current="page" href="home"><i class="icon-home"></i>Strona główna</a>
								</li>
								<li class="nav-item li-change-color">
									<a class="nav-link active text-light py-3" aria-current="page" href="add-income"><i class="icon-dollar"></i>Dodaj przychód</a>
								</li>
								<li class="nav-item li-change-color">
									<a class="nav-link active text-light py-3" aria-current="page" href="add-expense"><i class="icon-basket"></i>Dodaj wydatek</a>
								</li>
								<li class="nav-item li-change-color">
									<a class="nav-link text-light py-3" href="browse-the-balance"><i class="icon-chart-bar"></i>Przeglądaj bilans</a>
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
							<div class="table-responsive">
								<table class="table-center">
									<thead>
										<tr><th colspan="5" class="center-td">Zestawienie przychodów w okresie od 
											<?php 
												echo $dateFromTo;
											?>
										
										</th></tr>
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
											if($queryNameIncome->rowCount()>0){
											$i=1;
											foreach ($queryName as $incomesUser) {
												echo "<tr>
														<td class=\"center-td\">$i</td>
														<td>{$incomesUser['amount']}</td>
														<td>{$incomesUser['date_of_income']}</td>
														<td>{$incomesUser['name']}</td>
														<td>{$incomesUser['income_comment']}</td>
													</tr>";
													$i++;
												}
											} else {
												echo "<tr><td colspan=\"6\" class=\"center-td\">
												Brak przychodów</td></tr>";											
											}
										?>
									</tbody>
								</table>
							</div> 
							<div class="table-responsive mt-4">
								<table class="table-center">
									<thead>
										<tr><th colspan="6" class="center-td">Zestawienie wydatków w okresie od 
											<?php 
												echo $dateFromTo;
											?>
										
										</th></tr>
										<tr>
											<th>Lp</th>
											<th>Kwota (zł)</th>
											<th>Data</th>
											<th>Sposób płatności</th>
											<th>Kategoria</th>
											<th>Komentarz</th>
										</tr>
									</thead>
									<tbody>
										<?php
											if($queryNameExpense->rowCount()>0){
												$i=1;
												foreach ($queryExpense as $expensesUser) {
													echo "<tr>
															<td class=\"center-td\">$i</td>
															<td>{$expensesUser['amn']}</td>
															<td>{$expensesUser['dateExp']}</td>
															<td>{$expensesUser['pay']}</td>
															<td>{$expensesUser['excategory']}</td>
															<td>{$expensesUser['comment']}</td>
														</tr>";
														$i++;
												}
											}else {
												echo "<tr><td colspan=\"6\" class=\"center-td\">
												Brak wydatków</td></tr>";								
											}
										
										?>
									</tbody>
								</table>
							</div>                            
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
	<script src="js/script.js"></script>
	<script src="js/bootstrap.min.js"></script>
</body>
</html>