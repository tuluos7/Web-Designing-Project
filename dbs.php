<?php
$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "dbsproject"; 

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$stations = [];
$result = $conn->query("SELECT station_id, station_name FROM stations ORDER BY station_name");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $stations[] = $row;
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Modern Pakistan Railways</title>
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

  <section class="hero">
    <div class="hero-content">
      <h1>Plan Your Journey</h1>
      
      <form class="search-form" action="search_results.php" method="get">
        <select name="from_station_id" required>
          <option value="">From</option>
          <?php foreach ($stations as $station): ?>
            <option value="<?php echo $station['station_id']; ?>">
              <?php echo htmlspecialchars($station['station_name']); ?>
            </option>
          <?php endforeach; ?>
        </select>

        <select name="to_station_id" required>
          <option value="">To</option>
          <?php foreach ($stations as $station): ?>
            <option value="<?php echo $station['station_id']; ?>">
              <?php echo htmlspecialchars($station['station_name']); ?>
            </option>
          <?php endforeach; ?>
        </select>

        <input type="date" name="journey_date" required>
        <button type="submit">Search</button>
      </form>
    </div>
  </section>

  <section class="quick-links">
    <div class="quick-link">
      <a href="timetable.php"><h3>Train Timetable</h3></a>
      <p>Check all train schedules</p>
    </div>
    <div class="quick-link">
      <a href="etickets.php"><h3>E-Tickets</h3></a>
      <p>Book your Tickets</p>
    </div>
    <div class="quick-link">
      <a href="notices.php"><h3>Public Notices</h3></a>
      <p>Recent updates and alerts</p>
    </div>
  </section>

  <footer>
    &copy; 2025 Pakistan Railways. All rights reserved.
  </footer>

</body>
</html>
