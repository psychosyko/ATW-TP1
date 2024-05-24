<?php

    session_start();

    include '../../session_admin.php';

    include '../../conn.php';

    $message = null;

    if ($_SERVER["REQUEST_METHOD"] === "POST") {

        $event_name = $_POST['eventName'];
        $organizer = $_POST['organizer'];
        $description = $_POST['description'];
        $date = $_POST['date'];
        $time = $_POST['time'];
        $location = $_POST['location'];
        $genres = $_POST['genres'];
        $price = $_POST['price'];

        $uploaddir = '../../uploads/events/';
        $uploadfile = $uploaddir . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], $uploadfile);


        if (isset($_GET['id'])) {
            $conn->execute_query('UPDATE concerts SET name = ?, id_type = ?, location = ?, date = ?, time = ?, price = ?, organizer = ?, description = ?, image_location = ? WHERE id = ?', [
                $event_name,
                $genres,
                $location,
                $date,
                $time,
                $price,
                $organizer,
                $description,
                '/uploads/events/'. basename($_FILES['image']['name']),
                $_GET['id']
            ]);

            $message = "Evento editado com sucesso!";
        } else {

            $conn->execute_query('INSERT INTO concerts (name, id_type, location, date, time, price, organizer, description, image_location) VALUES (?,?,?,?,?,?,?,?,?)', [
                $event_name,
                $genres,
                $location,
                $date,
                $time,
                $price,
                $organizer,
                $description,
                '/uploads/events/'. basename($_FILES['image']['name'])
            ]);

            $message = "Evento criado com sucesso!";
        }
    }

    $types = $conn->execute_query('SELECT * FROM types');

    $create = true;

    if (isset($_GET['id'])) {
        $create = false;

        $concert = $conn->execute_query('SELECT * FROM concerts WHERE id = ?', [$_GET['id']])->fetch_assoc();
    }
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Event Form</title>
    <link rel="stylesheet" href="createEvent.module.css">
</head>

<body>
    <div class="form-container">
        <button class="back-button" onclick="redirectToHomepage()">
            <img src="back_arrow.png" alt="Back" class="back_arrow" />
        </button>
        <header class="form-header">
        <h1><?php if ($create == true) {echo 'Create Event'; } else { echo 'Edit Event '; } ?></h1>
        <?php
            if ($message != null) {
                echo  $message;
            }
        ?>
        </header>
        <form class="event-form" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="eventName">Event Name</label>
                <input type="text" id="eventName" name="eventName" required <?php if (!$create) { echo 'value="'.$concert['name'].'"'; } else { echo "";} ?>>
            </div>
            <div class="form-group">
                <label for="organizer">Organizer</label>
                <input type="text" id="organizer" name="organizer" required <?php if (!$create) { echo 'value="'.$concert['organizer'].'"'; } else { echo "";} ?> >
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" rows="4" required><?php if (!$create) { echo $concert['description']; } else { echo "";} ?></textarea>
            </div>
            <div class="form-group">
                <label for="date">Date</label>
                <input type="date" id="date" name="date" required <?php if (!$create) { echo 'value="'.$concert['date'].'"'; } else { echo "";} ?>>
            </div>
            <div class="form-group">
                <label for="time">Time</label>
                <input type="time" id="time" name="time" required <?php if (!$create) { echo 'value="'.$concert['time'].'"'; } else { echo "";} ?>>
            </div>
            <div class="form-group">
                <label for="location">Location</label>
                <input type="text" id="location" name="location" required <?php if (!$create) { echo 'value="'.$concert['location'].'"'; } else { echo "";} ?>>
            </div>
            <div class="form-group">
                <label for="genres">Genres</label>
                <select name="genres" id="genres" require>
                    <?php
                        while ($type = $types->fetch_assoc()) {

                            $selected = "";
                            if (!$create) {
                                if ($concert['id_type'] == $type['id']) {
                                    $selected = "selected";
                                }
                            }

                            echo '
                                <option '.$selected.' value="'.$type['id'].'">'.$type['name'].'</option>
                            ';
                        }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label for="price">Ticket Price ($)</label>
                <input type="number" id="price" name="price" required <?php if (!$create) { echo 'value="'.$concert['price'].'"'; } else { echo "";} ?>>
            </div>
            <div class="upload-btn-wrapper">
                    <label>Event Banner</label>
                    <label for="image" class="btn">Load an image</label>
                    <input type="file" id="image" name="image" accept="image/*">
                </div>
            <div class="form-group">
                <button type="submit" class="submit-button"><?php if ($create == true) {echo 'Create'; } else { echo 'Edit'; } ?> Event</button>
            </div>
        </form>
    </div>
    <script>
        function redirectToHomepage() {
            window.location.href = '../../home/';
        }
    </script>
</body>

</html>

