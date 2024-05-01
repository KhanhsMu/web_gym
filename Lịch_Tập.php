<?php

function build_calendar($month, $year)
{

    $mysqli = new mysqli('localhost', 'root', '', 'web_gym');

    $daysOfWeek = array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');
    $firstDayOfMonth = mktime(0, 0, 0, $month, 1, $year);
    $numberDays = date('t', $firstDayOfMonth);
    $dateComponents = getdate($firstDayOfMonth);
    $monthName = $dateComponents['month'];
    $dayOfWeek = $dateComponents['wday'];
    if ($dayOfWeek == 0) {
        $dayOfWeek = 6;
    } else {
        $dayOfWeek = $dayOfWeek - 1;
    }
    $dateToday = date('Y-m-d');

    $prev_month = date('m', mktime(0, 0, 0, $month - 1, 1, $year));
    $prev_year = date('Y', mktime(0, 0, 0, $month - 1, 1, $year));
    $next_month = date('m', mktime(0, 0, 0, $month + 1, 1, $year));
    $next_year = date('Y', mktime(0, 0, 0, $month + 1, 1, $year));

    $calendar = "<center><h2>$monthName $year</h2>";
    $calendar .= "<a class='btn btn-xs btn-primary ' href='?month=" . $prev_month . "&year=" . $prev_year . " '> Tháng trước </a>";
    $calendar .= "<a class='btn btn-primary btn-xs' href='?month=" . date('m') . "&year=" . date('Y') . " '> Tháng hiện tại </a>";
    $calendar .= "<a class='btn btn-primary btn-xs' href='?month=" . $next_month . "&year=" . $next_year . " '> Tháng sau </a></center>";
    $calendar .= "<br><table class='table table-bordered'>";
    $calendar .= "<tr>";

    foreach ($daysOfWeek as $day) {
        $calendar .= "<th class='header'>$day</th>";
    }
    $calendar .= "</tr><tr>";
    $currentDay = 1;
    if ($dayOfWeek > 0) {
        for ($k = 0; $k < $dayOfWeek; $k++) {
            $calendar .= "<td class='empty'></td>";
        }
    }

    $month = str_pad($month, 2, "0", STR_PAD_LEFT);
    while ($currentDay <= $numberDays) {
        if ($dayOfWeek == 7) {
            $dayOfWeek = 0;
            $calendar .= "</tr><tr>";
        }

        $currentDayRel = str_pad($currentDay, 2, "0", STR_PAD_LEFT);
        $date = "$year-$month-$currentDayRel";

        $dayName = strtolower(date('l', strtotime($date)));
        $eventNum = 0;
        $today = $date == date('Y-m-d') ? 'today' : '';
        if ($dayName == 'sunday') {
            $calendar .= "<td><h4>$currentDay</h4> <button class='btn btn-warning'>Nghỉ</button> </td>";
        } elseif ($date <= date('Y-m-d')) {
            $calendar .= "<td><h4>$currentDay</h4> <button class='btn btn-danger'>N/A</button> </td>";
        } else {
            $total_bookings = checkSlot($mysqli, $date);
            if ($total_bookings == 5) {
                $calendar .= "<td class='$today'><h4>$currentDay</h4> <a href='#' class='btn btn-danger'>Đã đầy</a> </td>";
            } else {
                $available =5-$total_bookings;
                $calendar .= "<td><h4>$currentDay</h4> <a href='book.php?date=" . $date . "' class='btn btn-success'>
                Chọn</a> <small><h1>$available suất rảnh</h1></small> </td>";
            }
        }
        $currentDay++;
        $dayOfWeek++;
    }

    if ($dayOfWeek < 7) {
        $remainingDays = 7 - $dayOfWeek;
        for ($i = 0; $i < $remainingDays; $i++) {
            $calendar .= "<td class='empty'></td>";
        }
    }

    $calendar .= "</tr>";
    $calendar .= "</table>";
    return $calendar;
}
function checkSlot($mysqli, $date)
{
    $stmt = $mysqli->prepare('SELECT * FROM bookingss WHERE date = ?');
    $stmt->bind_param('s', $date);
    $total_bookings = 0;
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $total_bookings++;
            }
            $stmt->close();
        }
    }
    return $total_bookings;
}
?>

<!DOCTYPE html>
<html lang="zxx">
<head>
	<title>WellFit | GYM </title>
	<meta charset="UTF-8">
	<meta name="description" content="Ahana Yoga HTML Template">
	<meta name="keywords" content="yoga, html">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<!-- Stylesheets -->
	<link rel="stylesheet" href="css/bootstrap.min.css"/>
	<link rel="stylesheet" href="css/font-awesome.min.css"/>
	<link rel="stylesheet" href="css/owl.carousel.min.css"/>
	<link rel="stylesheet" href="css/nice-select.css"/>
	<link rel="stylesheet" href="css/slicknav.min.css"/>

	<!-- Main Stylesheets -->
	<link rel="stylesheet" href="css/style.css"/>
</head>
<body>
	
<div id="preloder">
		<div class="loader"></div>
	</div>

	<!-- Header Section -->
	<header class="header-section">
		<div class="header-top">
			<div class="row m-0">
				<div class="col-md-6 d-none d-md-block p-0">
					<div class="header-info">
						<i class="material-icons">map</i>
						<p>32 Lê Doãn Nhạ </p>
					</div>
					<div class="header-info">
						<i class="material-icons">phone</i>
						<p>(965) 436 3274</p>
					</div>
				</div>
				<div class="col-md-6 text-left text-md-right p-0">
					<div class="header-info d-none d-md-inline-flex">
						<i class="material-icons">alarm_on</i>
						<p>Mon - Fri:  6:30am - 07:45pm</p>
					</div>
				</div>
			</div>
		</div>
		<div class="header-bottom">
			<a href="index.html" class="site-logo">
				<img src="img/logo1.png" alt="">
			</a>
			
			<div class="container">
				<ul class="main-menu">
					<li><a href="index.html" class="active">Home</a></li>
					<li><a href="about.html">About</a></li>
					<!-- <li><a href="classes.html">Classes</a>
						<ul class="sub-menu">
							<li><a href="classes.html">Our Claasses</a></li>
							<li><a href="classes-details.html">Claasses Details</a></li>
						</ul>
					</li> -->
					<li><a href="trainer.html">trainers</a>
						<!-- <ul class="sub-menu">
							<li><a href="trainer.html">Our Trainers</a></li>
							<li><a href="trainer-details.html">Trainers Details</a></li>
						</ul> -->
					</li>
					<li><a href="Lịch_Tập.php">register</a> 
					</li>
					<!-- <li><a href="blog.html">Blog</a>
						<ul class="sub-menu">
							<li><a href="blog.html">Our Blog</a></li>
							<li><a href="single-blog.html">Blog Details</a></li>
						</ul>
					</li> -->
					<li><a href="contact.html">Contact</a></li>
				</ul>
			</div>
		</div>
	</header>
	<!-- Header Section end -->

<?php include "connect.php";?>






<div class="container">
    <div class="row">
        <div class="col-md-12">
            <?php
            $dateComponents = getdate();
            if (isset($_GET['month']) && isset($_GET['year'])) {
                $month = $_GET['month'];
                $year = $_GET['year'];
            } else {
                $month = $dateComponents['mon'];
                $year = $dateComponents['year'];
            }
            echo build_calendar($month, $year);
            ?>
        </div>
    </div>
</div>
<button>Đăng kí ngay</button>

</body>

</html>


	
	<!--====== Javascripts & Jquery ======-->
	<script src="js/vendor/jquery-3.2.1.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
	<script src="js/jquery.slicknav.min.js"></script>
	<script src="js/owl.carousel.min.js"></script>
	<script src="js/jquery.nice-select.min.js"></script>
	<script src="js/jquery-ui.min.js"></script>
	<script src="js/jquery.magnific-popup.min.js"></script>
	<script src="js/main.js"></script>

	</body>
</html>

<style>
    /* table {
        width: 60%;
        border-collapse: collapse;
        border: 1px solid #ddd;
    }

    th {
        background-color: #337ab7;
        color: #fff;
        font-weight: bold;
        padding: 10px;
        text-align: center;
    }

    td {
        border: 1px solid #ddd;
        padding: 5px;
        text-align: center;
        background-color: #f2f2f2;
        height: 50px;
    } */
    @media only screen and (max-width:760px),
    (min-device-width:802px) and (max-device-width:1020px) {

        table,
        thead,
        tbody,
        th,
        td,
        tr {
            display: block;
        }

        .empty {
            display: none;
        }

        td:nth-of-type(1)::before {
            content: "Chủ Nhật";
        }

        td:nth-of-type(1)::before {
            content: "Thứ 2";
        }

        td:nth-of-type(1)::before {
            content: "Thứ 3";
        }

        td:nth-of-type(1)::before {
            content: "Thứ 4";
        }

        td:nth-of-type(1)::before {
            content: "Thứ 5";
        }

        td:nth-of-type(1)::before {
            content: "Thứ 6";
        }

        td:nth-of-type(1)::before {
            content: "Thứ 7";
        }
    }

    @media only screen and (min-device-width:320px) and (max-device-width:480px) {
        body {
            padding: 0;
            margin: 0;
        }
    }

    @media only screen and (min-device-width:802px) and (max-device-width:1020px) {
        body {
            width: 495px;
        }
    }

    @media (min-width:641px) {
        table {
            table-layout: fixed;
        }

        td {
            width: 33.33%;
        }
    }

    .row {
        margin-top: 20px;
    }

    .today {
        background-color: yellow;
    }
</style>
