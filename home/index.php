<?php
session_start();

include '../session.php';
include '../conn.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $filter = [];
    $values = [];

    if (isset($_POST['search'])) {
        array_push($filter, "c.name like ?");
        array_push($values, $_POST['search'] . '%');
    }

    if (isset($_POST['date'])) {
        array_push($filter, "date like ?");
        array_push($values, $_POST['date']);
    }

    if (isset($_POST['genre'])) {
        array_push($filter, "id_type = ?");
        array_push($values, $_POST['genre']);
    }

    if (isset($_POST['location'])) {
        array_push($filter, "location like ?");
        array_push($values, $_POST['location']);
    }

    if (count($filter) == 0) {
        $query = 'SELECT *, c.id as id, c.name as concert, t.name as type FROM concerts c LEFT JOIN types t ON t.id = c.id_type';
    } else {
        $query = 'SELECT *, c.id as id, c.name as concert, t.name as type FROM concerts c LEFT JOIN types t ON t.id = c.id_type WHERE ' . join(' AND ', $filter);
    }

    $concerts = $conn->execute_query($query, $values);
} else {
    $concerts = $conn->execute_query('SELECT *, c.id as id, c.name as concert, t.name as type FROM concerts c LEFT JOIN types t ON t.id = c.id_type');
}

    $types = $conn->execute_query('SELECT * FROM types');
    $locations = $conn->execute_query('SELECT location FROM concerts');
    $dates = $conn->execute_query('SELECT date FROM concerts');
    $user = $conn->execute_query('SELECT * FROM users WHERE id = ?', [$_SESSION['id']])->fetch_assoc();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home Page</title>
    <link rel="stylesheet" href="home.module.css">
</head>

<body>
    <nav>
        <ul class="sidebar">
            <li onclick="hideSidebar()"><a href="../home/"><svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#fffff"><path d="m256-200-56-56 224-224-224-224 56-56 224 224 224-224 56 56-224 224 224 224-56 56-224-224-224 224Z"/></svg></a></li>

            <?php
            if ($_SESSION['role'] > 0) {
                echo '
                    <li id="createEventSidebar"><a href="../admin/createEvent/">Create Event</a></li>
                    <li id="userListSidebar"><a href="../admin/userList/">User List</a></li>
                    ';
            }
            ?>

            <li><a href="../profile/">Profile</a></li>
            <li><a href="../reservation/">Reservations</a></li>
            <li><a href="../logout/">Logout</a></li>
        </ul>
        <ul>
            <li><img src= "./groovibe.png" href="../home/" class="groovibe"></img></li>
            <?php
            if ($_SESSION['role'] > 0) {
                echo '
                    <li class="hideOnMobile" id="createEvent"><a href="../admin/createEvent/">Create Event</a></li>
                    <li class="hideOnMobile" id="userList"><a href="../admin/userList/">User List</a></li>
                    ';
            }
            ?>
            <li class="hideOnMobile profile-link">
                <a href="../profile/">
                <img src="<?php echo $user['image_location']; ?>" alt="User Profile" class="profile-pic">
                    Profile
                </a>
            </li>
            <li class="hideOnMobile"><a href="../reservation/">Reservations</a></li>
            <li class="hideOnMobile"><a href="../logout/">Logout</a></li>
            <li class="menu-button" onclick="showSidebar()"><a href="#">
                    <img src="<?php echo $user['image_location']; ?>" alt="User Profile" class="profile-pic-mobile">
                </a></li>
        </ul>
    </nav>
    <main class="main-content">
        <section class="search-section">
            <div class="title-box">
                <h2>Find concerts near you</h2>
            </div>
            <form action="" method="POST" id="filterForm">
                <div class="search-box">
                    <button class="search-icon">
                        <img src="magnifier_icon.png" alt="Search">
                    </button>
                    <?php
                    if (isset($_POST['search'])) {
                        echo '
                                <input type="text" name="search" placeholder="Search" value="' . $_POST['search'] . '">
                            ';
                    } else {
                        echo '
                                <input type="text" name="search" placeholder="Search">
                            ';
                    }
                    ?>
                </div>
                <div class="filters">
                    <select name="date" id="date">
                        <option hidden disabled selected value>Date</option>
                        <?php
                        while ($date = $dates->fetch_assoc()) {

                            $selected = "";

                            if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['date'])) {
                                if ($_POST['date'] == $date['date']) {
                                    $selected = "selected";
                                }
                            }

                            echo '
                                    <option ' . $selected . '  value="' . $date['date'] . '">' . $date['date'] . '</option>
                                ';
                        }
                        ?>
                    </select>
                    <select name="genre" id="genre">
                        <option hidden disabled selected value>Genre</option>
                        <?php
                        while ($type = $types->fetch_assoc()) {

                            $selected = "";

                            if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['genre'])) {
                                if ($_POST['genre'] == $type['id']) {
                                    $selected = "selected";
                                }
                            }

                            echo '
                                    <option ' . $selected . ' value="' . $type['id'] . '">' . $type['name'] . '</option>
                                ';
                        }
                        ?>
                    </select>
                    <select name="location" id="location">
                        <option hidden disabled selected value>Location</option>
                        <?php
                        while ($location = $locations->fetch_assoc()) {

                            $selected = "";

                            if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['location'])) {
                                if ($_POST['location'] == $location['location']) {
                                    $selected = "selected";
                                }
                            }

                            echo '
                                    <option ' . $selected . ' value="' . $location['location'] . '">' . $location['location'] . '</option>
                                ';
                        }
                        ?>
                    </select>
                </div>
                <button type="button" onclick="clearFilters()">Clear filters</button>

                <button type="submit">Apply filters</button>
            </form>
        </section>
        <section class="concert-list">
            <?php
            while ($concert = $concerts->fetch_assoc()) {
                echo '
                    <a href="/events/index.php?id=' . $concert['id'] . '" class="concert">
                        <div class="concert-image-wrapper">
                            <img src="'.$concert['image_location'].'" alt="Banner" class="concert-image">
                            <button class="genre">' . $concert['type'] . '</button>
                        </div>
                        <div class="concert-info">
                            <h3>' . $concert['concert'] . '</h3>
                            <p>' . $concert['location'] . '</p>
                            <p>' . $concert['date'] . ' | ' . $concert['time'] . '</p>
                            <span>$' . $concert['price'] . '</span>
                        </div>
                    </a>
                    ';
            }
            ?>
        </section>
    </main>
    <script>

        function showSidebar() {
            const sidebar = document.querySelector('.sidebar');
            sidebar.style.display = 'flex';
        }

        function hideSidebar() {
            const sidebar = document.querySelector('.sidebar');
            sidebar.style.display = 'none';
        }

        function clearFilters() {
            document.getElementById('filterForm').reset();
            window.location.href = window.location.pathname;
        }

    </script>
</body>

</html>
