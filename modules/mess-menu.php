<?php
session_start();
include('../includes/dbconn.php');
include('../includes/header.php');
include('../includes/student-sidebar.php');
?>

<div class="main-content" style="margin-left:250px; padding:30px;">
    <h2 class="mb-4">🍽️ Weekly Mess Menu</h2>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Day</th>
                <th>Breakfast</th>
                <th>Lunch</th>
                <th>Dinner</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $query = "SELECT * FROM mess_weekly_menu";
            $result = mysqli_query($conn, $query);
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>
                        <td>{$row['day']}</td>
                        <td>{$row['breakfast']}</td>
                        <td>{$row['lunch']}</td>
                        <td>{$row['dinner']}</td>
                      </tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<?php include('../includes/footer.php'); ?>
