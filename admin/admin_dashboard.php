<?php
session_start();

include '../connections.php';

if (!isset($_SESSION['user']) || !isset($_SESSION['user']['role']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Sari-Sari Store ni David</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<?php include 'adminheader.php'; ?>
<body>
    <?php include 'adminsidebar.php'; ?>


        <main class="flex-1 p-6">
            <h1 class="text-2xl font-bold">Welcome, <?php echo isset($_SESSION['user']['name']) ? htmlspecialchars($_SESSION['user']['name']) : 'Admin'; ?>!</h1>
                <tbody>
                    <?php
                    $adminQuery = "SELECT * FROM users WHERE role IN ('super_admin', 'editor')";
                    $adminResult = mysqli_query($conn, $adminQuery);
                    while ($row = mysqli_fetch_assoc($adminResult)) {
                        echo "<tr>
                                <td class='border p-2'>{$row['id']}</td>
                                <td class='border p-2'>{$row['name']}</td>
                                <td class='border p-2'>{$row['role']}</td>
                                <td class='border p-2'>
                                    <button class='bg-yellow-500 text-white px-2 py-1 rounded'>Edit</button>
                                    <button class='bg-red-500 text-white px-2 py-1 rounded'>Delete</button>
                                </td>
                              </tr>";
                    }
                    ?>
                </tbody>
            </table>
        </main>
    </div>
</body>
</html>
