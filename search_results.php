<?php

$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "dbsproject"; 


$from_station_id = isset($_GET['from_station_id']) ? (int)$_GET['from_station_id'] : 0;
$to_station_id   = isset($_GET['to_station_id']) ? (int)$_GET['to_station_id'] : 0;
$journey_date    = isset($_GET['journey_date']) ? $_GET['journey_date'] : null;

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


$station_names = [];
$station_query = $conn->query(
    "SELECT station_id, station_name FROM stations WHERE station_id IN ($from_station_id, $to_station_id)"
);
while ($row = $station_query->fetch_assoc()) {
    $station_names[$row['station_id']] = $row['station_name'];
}


$sql = "
SELECT 
    r_from.train_id,
    t.train_name,
    r_from.arrival_time AS from_arrival_time,
    r_from.departure_time AS from_departure_time,
    r_to.arrival_time AS to_arrival_time,
    r_to.departure_time AS to_departure_time
FROM routes r_from
JOIN routes r_to 
  ON r_from.train_id = r_to.train_id
JOIN trains t 
  ON t.train_id = r_from.train_id
WHERE r_from.station_id = $from_station_id
  AND r_to.station_id = $to_station_id
  AND r_from.stop_number < r_to.stop_number
ORDER BY t.train_name
";

$result = $conn->query($sql);
$trains = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>Search Results - Pakistan Railways</title>
<link href="main.css" rel="stylesheet">
</head>
<body>

<header>
  <div class="logo">
    <a href="dbs.php"><img src="logo.png" alt="Logo"></a>
  </div>
  <nav>
    <a href="dbs.php">Home</a>
    <a href="timetable.php">Timetable</a>
    <a href="etickets.php">E-Tickets</a>
    <a href="notices.php">Notices</a>
  </nav>
</header>

<div class ="hero">
    <div class = "hero-content2">
<section class="results">
  <h1>Search Results</h1>
  <p>From <strong><?php echo htmlspecialchars($station_names[$from_station_id] ?? ''); ?></strong> 
     to <strong><?php echo htmlspecialchars($station_names[$to_station_id] ?? ''); ?></strong> 
     on <strong><?php echo htmlspecialchars($journey_date); ?></strong></p>

  <?php if (count($trains) > 0): ?>
    <table class="results-table">
      <thead>
        <tr>
          <th>Train Name</th>
          <th>Departure (From Station)</th>
          <th>Arrival (To Station)</th>
          <th>Book Now</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($trains as $train): ?>
          <tr>
            <td><?php echo htmlspecialchars($train['train_name']); ?></td>
            <td>
              <?php echo ($train['from_departure_time']) 
                ? htmlspecialchars($train['from_departure_time']) 
                : htmlspecialchars($train['from_arrival_time']); ?>
            </td>
            <td><?php echo htmlspecialchars($train['to_arrival_time']); ?></td>
            <td>
              <a href="etickets.php?train_id=<?php echo (int)$train['train_id']; ?>&journey_date=<?php echo urlencode($journey_date); ?>&from_station_id=<?php echo (int)$from_station_id; ?>&to_station_id=<?php echo (int)$to_station_id; ?>" class="button">Book Ticket</a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php else: ?>
    <p class="no-results">No direct trains available for this route.</p>
  <?php endif; ?>
</section>
</div>
  </div>
<footer>
  &copy; 2025 Pakistan Railways. All rights reserved.
</footer>

</body>
</html>
