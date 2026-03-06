<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "dbsproject"; // <-- put your database name here

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$station_result = $conn->query("SELECT station_id, station_name FROM stations ORDER BY station_name");
$stations = $station_result->fetch_all(MYSQLI_ASSOC);

$search_results = [];
if (isset($_GET['from_station_id']) && isset($_GET['to_station_id'])) {
    $from_id = (int)$_GET['from_station_id'];
    $to_id = (int)$_GET['to_station_id'];

    $sql = "
      SELECT t.train_name, 
             r1.departure_time AS depart_time, 
             r2.arrival_time AS arrival_time
      FROM routes r1
      JOIN routes r2 ON r1.train_id = r2.train_id
      JOIN trains t ON t.train_id = r1.train_id
      WHERE r1.station_id = $from_id
      AND r2.station_id = $to_id
      AND r1.stop_number < r2.stop_number
      ORDER BY r1.departure_time ASC
    ";
    $res = $conn->query($sql);
    $search_results = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Timetable - Pakistan Railways</title>
  <link href="main.css" rel="stylesheet">
</head>
<body>
  <header>
    <div class="logo">
      <a href="dbs.php"><img src="logo.png" alt="Pakistan Railways"></a>
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
      <h1>Train Timetable</h1>
      <h2>View the latest schedules for all trains.</h2>
      <form class="search-form" action="timetable.php" method="GET">
        <select name="from_station_id" required>
          <option value="" disabled selected>From</option>
          <?php foreach ($stations as $station): ?>
            <option value="<?php echo $station['station_id']; ?>" 
              <?php if (!empty($_GET['from_station_id']) && $_GET['from_station_id']==$station['station_id']) echo "selected"; ?>>
              <?php echo htmlspecialchars($station['station_name']); ?>
            </option>
          <?php endforeach; ?>
        </select>

        <select name="to_station_id" required>
          <option value="" disabled selected>To</option>
          <?php foreach ($stations as $station): ?>
            <option value="<?php echo $station['station_id']; ?>" 
              <?php if (!empty($_GET['to_station_id']) && $_GET['to_station_id']==$station['station_id']) echo "selected"; ?>>
              <?php echo htmlspecialchars($station['station_name']); ?>
            </option>
          <?php endforeach; ?>
        </select>
        <button type="submit">Search</button>
      </form>
      <div class="hero-content2">
      <section class="results">
    <?php if ($_GET): ?>
      <h2>Search Results</h2>
      <?php if (count($search_results) > 0): ?>
        <table>
          <thead>
            <tr>
              <th>Train</th>
              <th>Departure Time</th>
              <th>Arrival Time</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($search_results as $row): ?>
              <tr>
                <td><?php echo htmlspecialchars($row['train_name']); ?></td>
                <td><?php echo htmlspecialchars($row['depart_time']); ?></td>
                <td><?php echo htmlspecialchars($row['arrival_time']); ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php else: ?>
        <p>No trains found for this route.</p>
      <?php endif; ?>
    <?php endif; ?>
    </div>
  </section>
    </div>
  </section>



  <div class="back-button">
    <a href="dbs.php">Back to Home</a>
  </div>

  <footer>
    &copy; 2025 Pakistan Railways. All rights reserved.
  </footer>
</body>
</html>
