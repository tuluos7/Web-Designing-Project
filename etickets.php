<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "dbsproject";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$stations = $conn->query("SELECT station_id, station_name FROM stations ORDER BY station_name");
$trains = $conn->query("SELECT train_id, train_name FROM trains ORDER BY train_name");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $conn->real_escape_string($_POST["name"]);
    $cnic = $conn->real_escape_string($_POST["cnic"]);
    $age = (int)$_POST["age"];
    $gender = $conn->real_escape_string($_POST["gender"]);
    $phone = $conn->real_escape_string($_POST["phone"]);
    $journey_date = $conn->real_escape_string($_POST["journey_date"]);
    $from_station_id = (int)$_POST["from_station_id"];
    $to_station_id = (int)$_POST["to_station_id"];
    $train_id = (int)$_POST["train_id"];
    $seat_number = $conn->real_escape_string($_POST["seat_number"]);

    $sql_passenger = "INSERT INTO passengers (name, age, gender) VALUES ('$name', $age, '$gender')";
    if ($conn->query($sql_passenger)) {
        $passenger_id = $conn->insert_id;

        $seat_query = $conn->query(
            "SELECT seat_id FROM seats WHERE train_id = $train_id AND seat_number = '$seat_number' LIMIT 1"
        );

        if ($seat_query->num_rows > 0) {
            $seat_id = $seat_query->fetch_assoc()['seat_id'];

            $sql_booking = "
                INSERT INTO bookings (passenger_id, train_id, journey_date, from_station_id, to_station_id, seat_number) 
                VALUES ($passenger_id, $train_id, '$journey_date', $from_station_id, $to_station_id, '$seat_number')
            ";

            if ($conn->query($sql_booking)) {
                echo "<script>alert('Ticket booked successfully!'); window.location.href='etickets.php';</script>";
            } else {
                echo "Error inserting booking: " . $conn->error;
            }
        } else {
            echo "<script>alert('Invalid seat number for this train');</script>";
        }
    } else {
        echo "Error inserting passenger: " . $conn->error;
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>E-Tickets - Pakistan Railways</title>
<link href="main2.css" rel="stylesheet">
<style>
  h1 { text-align: center; }
  form { max-width: 600px; margin: auto; }
  input, select, button { width: 100%; padding: 10px; margin: 10px 0; box-sizing: border-box; }
</style>
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
<main>
  <section class="hero">
    <div class="hero-content">
      <h1>Book Your E-Tickets</h1>
      <form action="" method="POST">
        <input type="text" name="name" placeholder="Your Name" required>
        <input type="text" name="cnic" placeholder="Your CNIC (e.g. 12345-1234567-1)" pattern="\d{5}-\d{7}-\d{1}" required>
        <input type="number" name="age" placeholder="Your Age" required>
        <select name="gender" required>
          <option value="" disabled selected>Gender</option>
          <option value="M">Male</option>
          <option value="F">Female</option>
          <option value="O">Other</option>
        </select>
        <input type="tel" name="phone" placeholder="Your Phone Number" pattern="\d{11}" required>

        <select name="train_id" required>
          <option value="" disabled selected>Select Train</option>
          <?php foreach ($trains as $train): ?>
            <option value="<?php echo $train['train_id']; ?>"><?php echo htmlspecialchars($train['train_name']); ?></option>
          <?php endforeach; ?>
        </select>

        <select name="from_station_id" required>
          <option value="" disabled selected>From Station</option>
          <?php foreach ($stations as $station): ?>
            <option value="<?php echo $station['station_id']; ?>"><?php echo htmlspecialchars($station['station_name']); ?></option>
          <?php endforeach; ?>
        </select>

        <select name="to_station_id" required>
          <option value="" disabled selected>To Station</option>
          <?php foreach ($stations as $station): ?>
            <option value="<?php echo $station['station_id']; ?>"><?php echo htmlspecialchars($station['station_name']); ?></option>
          <?php endforeach; ?>
        </select>

        <input type="date" name="journey_date" required>
        <input type="text" name="seat_number" placeholder="Seat Number (e.g. 1)" required>
        <button type="submit" style="background-color:#014421; border-radius: 6px; color: white;">Book Ticket</button>
      </form>
    </div>
  </section>
</main>
<div class="back-button">
  <a href="dbs.php">Back to Home</a>
</div>
<footer>
  &copy; 2025 Pakistan Railways. All rights reserved.
</footer>
</body>
</html>
