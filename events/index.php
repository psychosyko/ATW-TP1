<?php

    session_start();

    include '../session.php';

    include '../conn.php';


    if ($_SERVER["REQUEST_METHOD"] === "POST") {

        if (isset($_GET['id'])) {
            $id = $_GET['id'];

            $conn->execute_query('INSERT INTO orders (id_user, id_concert) VALUES (?,?)', [$_SESSION['id'], $id]);
            header('Location: /reservation/');
        }

    }

    $concert = $conn->execute_query('SELECT *, c.name as concert, t.name as type FROM concerts c LEFT JOIN types t ON t.id = c.id_type WHERE c.id = ?', [$_GET['id']])->fetch_assoc();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Page</title>
    <link rel="stylesheet" href="events.module.css">
</head>

<body>
    <div class="event-container">
        <button class="back-button" onclick="history.back()">
            <img src="back_arrow.png" alt="Back" class="back_arrow" />
        </button>
        <header class="event-header">
            <img src="<?php echo $concert['image_location']; ?>" alt="Concert Image">
            <button class="genre-button"><?php echo $concert['type'] ?></button>
        </header>
        <form class="event-details" method="POST">
            <h1><?php echo $concert['concert'] ?></h1>
            <p class="organizer"><?php echo $concert['organizer'] ?></p>
            <p class="description"><?php echo $concert['description'] ?></p>
            <div class="info-box"> 
                <p class="event-info"><span class="icon">&#128197;</span> <?php echo $concert['date'] ?> | <?php echo $concert['time'] ?></p>
                <p class="event-info"><span class="icon">&#128205;</span> <?php echo $concert['location'] ?></p>
                <button class="book-now">Book now ($<?php echo $concert['price'] ?>)</button>
            </div>
        </form>
    </div>
    <?php

        if ($_SESSION['role'] > 0) {
            echo '
                <!-- Admin Buttons -->
                <span id="message" class="error-text"></span>
                <a id="downloadAnchorElem" style="display:none"></a>
                <div class="admin-buttons" id="adminButtons">
                    <button id="download-users" attr-id="'.$_GET['id'].'" class="admin-button">Download Users</button>
                    <button id="edit-btn" attr-id="'.$_GET['id'].'" class="admin-button">Edit Event</button>
                    <button id="delete-btn" attr-id="'.$_GET['id'].'" class="admin-button">Delete Event</button>
                </div>
            ';
        }

    ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.0/xlsx.full.min.js"></script>
    <script>

        //download to txt
        // document.getElementById('download-users').addEventListener('click', async (e) => {
        //     const button = e.srcElement;
        //     const id = button.getAttribute('attr-id');

        //     let result = await fetch("/admin/usersEvent/index.php?id=" + id);

        //     const data = await result.json();

        //     var dataStr = "data:text/json;charset=utf-8," + encodeURIComponent(JSON.stringify(data));
        //     var dlAnchorElem = document.getElementById('downloadAnchorElem');
        //     dlAnchorElem.setAttribute("href",dataStr);
        //     dlAnchorElem.setAttribute("download", "users.txt");
        //     dlAnchorElem.click();
        // });

        //download to excel
        document.getElementById('download-users').addEventListener('click', async (e) => {
            const button = e.srcElement;
            const id = button.getAttribute('attr-id');

            let result = await fetch("/admin/usersEvent/index.php?id=" + id);

            const data = await result.json();

            const worksheet = XLSX.utils.json_to_sheet(data);

            const workbook = XLSX.utils.book_new();

            XLSX.utils.book_append_sheet(workbook, worksheet, 'Users');

            const workbookBinary = XLSX.write(workbook, { bookType: 'xlsx', type: 'binary' });

            const buffer = new ArrayBuffer(workbookBinary.length);
            const view = new Uint8Array(buffer);
            for (let i = 0; i < workbookBinary.length; ++i) {
                view[i] = workbookBinary.charCodeAt(i) & 0xFF;
            }

            const blob = new Blob([buffer], { type: 'application/octet-stream' });

            const url = URL.createObjectURL(blob);
            var dlAnchorElem = document.getElementById('downloadAnchorElem');
            dlAnchorElem.setAttribute("href", url);
            dlAnchorElem.setAttribute("download", "users.xlsx");
            dlAnchorElem.click();

            URL.revokeObjectURL(url);
        });


        document.getElementById('edit-btn').addEventListener("click", (e) => {
            const button = e.srcElement;
            const id = button.getAttribute('attr-id')

            window.location.href = "/admin/createEvent/index.php?id=" + id
        })

        document.getElementById('delete-btn').addEventListener("click", async (e) => {

            const button = e.srcElement;
            const id = button.getAttribute('attr-id');

            try {

                let result = await fetch("/admin/deleteEvent/index.php?id=" + id, {
                    method: 'DELETE'
                });

                if (result.status === 200) {
                    window.location.href = "/home/";
                } else {
                    let value = await result.text();
                    document.getElementById('message').innerHTML = value
                }
            } catch (e){

            }
        })
    </script>
</body>

</html>
