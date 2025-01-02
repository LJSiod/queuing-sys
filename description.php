CREATE TICKET CREATE TICKET CREATE TICKET CREATE TICKET CREATE TICKET CREATE TICKET CREATE TICKET CREATE TICKET CREATE TICKET CREATE TICKET 

<?php
session_start();
include 'db.php';

date_default_timezone_set('Asia/Manila');
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $branch_id = $_SESSION['user_id']; // Assuming the branch ID is stored in session

    $query = "INSERT INTO tickets (branch_id, title, description, status, created_at, updated_at) 
              VALUES ('$branch_id', '$category', '$description', 'Pending', NOW(), NOW())";

    if (mysqli_query($conn, $query)) {
        header('Location: branch_dashboard.php');
    } else {
        echo "Error: " . $query . "<br>" . mysqli_error($conn);
    }
}
?>

EDIT TICKET EDIT TICKET EDIT TICKET EDIT TICKET EDIT TICKET EDIT TICKET EDIT TICKET EDIT TICKET EDIT TICKET EDIT TICKET EDIT TICKET 

    <?php
session_start();
include 'db.php';
date_default_timezone_set('Asia/Manila');

// Check if the user is logged in and is a branch user
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'branch') {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $ticket_id = $_POST['ticket_id'];
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $description = $_POST['description'];

    $query = "UPDATE tickets SET title = ?, description = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssi", $category, $description, $ticket_id);

    if ($stmt->execute()) {
        header("Location: branch_dashboard.php");
        exit();
    } else {
        echo "Error updating ticket: " . $conn->error;
    }
}
?>

FETCH BRANCH TICKET FETCH BRANCH TICKET FETCH BRANCH TICKET FETCH BRANCH TICKET FETCH BRANCH TICKET FETCH BRANCH TICKET 

<?php
include 'db.php';
session_start();
date_default_timezone_set('Asia/Manila');

$branch_id = $_SESSION['user_id'];

$query = "SELECT t.*, GROUP_CONCAT(CONCAT(c.comment, ' (', u.name, ') at ', c.created_at) SEPARATOR '<br>') as comments
          FROM tickets t
          LEFT JOIN comments c ON t.id = c.ticket_id
          LEFT JOIN users u ON c.user_id = u.id
          WHERE t.branch_id = $branch_id AND DATE(t.created_at) = CURDATE()
          GROUP BY t.id";
$result = mysqli_query($conn, $query);

while ($row = mysqli_fetch_assoc($result)): ?>
<tr class="<?php echo strtolower(str_replace(' ', '-', $row['status'])); ?>">
    <td><?php echo $row['id']; ?></td>
    <td><?php echo $row['title']; ?></td>
    <td><?php echo $row['description']; ?></td>
    <td><?php echo $row['status']; ?></td>
    <td><?php echo $row['created_at']; ?></td>
    <td><?php echo $row['updated_at']; ?></td>
    <td>
        <?php if ($row['status'] == 'Pending'): ?>
        <button class="btn btn-sm btn-info" data-toggle="modal" data-target="#updateTicketModal<?php echo $row['id']; ?>" data-ticket-id="<?php echo $row['id']; ?>" data-title="<?php echo $row['title']; ?>" data-description="<?php echo $row['description']; ?>">Edit</button>
        <button class="btn btn-sm btn-danger" data-toggle="modal" data-target="#cancelTicketModal<?php echo $row['id']; ?>" data-ticket-id="<?php echo $row['id']; ?>">Cancel</button>
        <?php endif; ?>
    </td>
    <td>
        <?php
        $ticket_id = $row['id'];
        $comment_query = "SELECT c.*, u.name FROM comments c JOIN users u ON c.user_id = u.id WHERE c.ticket_id = $ticket_id";
        $comment_result = mysqli_query($conn, $comment_query);
        while ($comment_row = mysqli_fetch_assoc($comment_result)):
        ?>
        <p><strong><?php echo $comment_row['name']; ?>:</strong> <?php echo $comment_row['comment']; ?> <small>(<?php echo $comment_row['created_at']; ?>)</small></p>
        <?php endwhile; ?>
    </td>
</tr>
<?php endwhile; ?>


FETCH ADMIN TICKET FETCH ADMIN TICKET FETCH ADMIN TICKET FETCH ADMIN TICKET FETCH ADMIN TICKET FETCH ADMIN TICKET 


<?php
session_start();
include 'db.php';
date_default_timezone_set('Asia/Manila');

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

$current_date = date('Y-m-d');
$query = "SELECT t.*, u.name as branch_name FROM tickets t JOIN users u ON t.branch_id = u.id WHERE DATE(t.created_at) = '$current_date'";
$result = mysqli_query($conn, $query);

while ($row = mysqli_fetch_assoc($result)): ?>
<tr class="<?php echo strtolower(str_replace(' ', '-', $row['status'])); ?>">
    <td><?php echo $row['id']; ?></td>
    <td><?php echo $row['title']; ?></td>
    <td><?php echo $row['description']; ?></td>
    <td><?php echo $row['status']; ?></td>
    <td><?php echo $row['branch_name']; ?></td>
    <td><?php echo $row['created_at']; ?></td>
    <td>
        <button class="btn btn-sm btn-info" data-toggle="modal" data-target="#updateTicketModal<?php echo $row['id']; ?>">Update</button>
        <button class="btn btn-sm btn-primary" data-toggle="modal" data-target="#commentModal<?php echo $row['id']; ?>">Comment</button>
    </td>
    <td>
        <?php
        $ticket_id = $row['id'];
        $comment_query = "SELECT c.*, u.name FROM comments c JOIN users u ON c.user_id = u.id WHERE c.ticket_id = $ticket_id";
        $comment_result = mysqli_query($conn, $comment_query);
        while ($comment_row = mysqli_fetch_assoc($comment_result)):
        ?>
        <p><strong><?php echo $comment_row['name']; ?>:</strong> <?php echo $comment_row['comment']; ?> <small>(<?php echo $comment_row['created_at']; ?>)</small></p>
        <?php endwhile; ?>
    </td>
</tr>

<!-- Update Ticket Modal -->
<div class="modal fade" id="updateTicketModal<?php echo $row['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="updateTicketModalLabel<?php echo $row['id']; ?>" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="updateTicketModalLabel<?php echo $row['id']; ?>">Update Ticket Status</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="update_ticket.php" method="post">
                    <input type="hidden" name="ticket_id" value="<?php echo $row['id']; ?>">
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select class="form-control" id="status" name="status">
                            <option value="Pending" <?php if ($row['status'] == 'Pending') echo 'selected'; ?>>Pending</option>
                            <option value="In Progress" <?php if ($row['status'] == 'In Progress') echo 'selected'; ?>>In Progress</option>
                            <option value="Done" <?php if ($row['status'] == 'Done') echo 'selected'; ?>>Done</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Update</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Add Comment Modal -->
<div class="modal fade" id="commentModal<?php echo $row['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="commentModalLabel<?php echo $row['id']; ?>" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="commentModalLabel<?php echo $row['id']; ?>">Add Comment</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="add_comment.php" method="post">
                    <input type="hidden" name="ticket_id" value="<?php echo $row['id']; ?>">
                    <div class="form-group">
                        <label for="comment">Comment</label>
                        <textarea class="form-control" id="comment" name="comment" rows="3" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php endwhile; ?>


ADMIN DASH ADMIN DASH ADMIN DASH ADMIN DASH ADMIN DASH ADMIN DASH ADMIN DASH ADMIN DASH ADMIN DASH ADMIN DASH ADMIN DASH ADMIN DASH 

<?php
session_start();
include 'db.php';
include 'admin_header.php';
date_default_timezone_set('Asia/Manila');
// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

$current_user_name = $_SESSION['name'];
$current_date = date('Y-m-d');
$query = "SELECT t.*, u.name as branch_name FROM tickets t JOIN users u ON t.branch_id = u.id WHERE DATE(t.created_at) = '$current_date'";
$result = mysqli_query($conn, $query);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="styles.css" rel="stylesheet">
    <style>
        .pending {
            background-color: orange;
        }
        .in-progress {
            background-color: lightblue;
            color: white;
        }
        .done {
            background-color: yellowgreen;
            color: white;
        }
    </style>
    <title>Admin Dashboard</title>
</head>
<body>
    <div class="container-fluid mt-3">
        <h2><strong>Admin Dashboard</strong></h2>
        <table class="table table-bordered mt-3" style="width: 100%;">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Category</th>
                    <th>Description</th>
                    <th>Status</th>
                    <th>Branch</th>
                    <th>Created At</th>
                    <th>Actions</th>
                    <th>Comments</th>
                </tr>
            </thead>
            <tbody id="ticket-table-body">
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <tr class="<?php echo strtolower(str_replace(' ', '-', $row['status'])); ?>">
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['title']; ?></td>
                    <td><?php echo $row['description']; ?></td>
                    <td><?php echo $row['status']; ?></td>
                    <td><?php echo $row['branch_name']; ?></td>
                    <td><?php echo $row['created_at']; ?></td>
                    <td>
                        <button class="btn btn-sm btn-info" data-toggle="modal" data-target="#updateTicketModal<?php echo $row['id']; ?>">Update</button>
                        <button class="btn btn-sm btn-primary" data-toggle="modal" data-target="#commentModal<?php echo $row['id']; ?>">Comment</button>
                        
                    </td>
                    <td>
                        <?php
                        $ticket_id = $row['id'];
                        $comment_query = "SELECT c.*, u.name FROM comments c JOIN users u ON c.user_id = u.id WHERE c.ticket_id = $ticket_id";
                        $comment_result = mysqli_query($conn, $comment_query);
                        while ($comment_row = mysqli_fetch_assoc($comment_result)):
                        ?>
                        <p><strong><?php echo $comment_row['name']; ?>:</strong> <?php echo $comment_row['comment']; ?> <small>(<?php echo $comment_row['created_at']; ?>)</small></p>
                        <?php endwhile; ?>
                    </td>
                </tr>

                <!-- Update Ticket Modal -->
                <div class="modal fade" id="updateTicketModal<?php echo $row['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="updateTicketModalLabel<?php echo $row['id']; ?>" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="updateTicketModalLabel<?php echo $row['id']; ?>">Update Ticket Status</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form action="update_ticket.php" method="post">
                                    <input type="hidden" name="ticket_id" value="<?php echo $row['id']; ?>">
                                    <div class="form-group">
                                        <label for="status">Status</label>
                                        <select class="form-control" id="status" name="status">
                                            <option value="Pending" <?php if ($row['status'] == 'Pending') echo 'selected'; ?>>Pending</option>
                                            <option value="In Progress" <?php if ($row['status'] == 'In Progress') echo 'selected'; ?>>In Progress</option>
                                            <option value="Done" <?php if ($row['status'] == 'Done') echo 'selected'; ?>>Done</option>
                                        </select>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Update</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Add Comment Modal -->
                <div class="modal fade" id="commentModal<?php echo $row['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="commentModalLabel<?php echo $row['id']; ?>" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="commentModalLabel<?php echo $row['id']; ?>">Add Comment</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form action="add_comment.php" method="post">
                                    <input type="hidden" name="ticket_id" value="<?php echo $row['id']; ?>">
                                    <div class="form-group">
                                        <label for="comment">Comment</label>
                                        <textarea class="form-control" id="comment" name="comment" rows="3" required></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    </div>
<?php
// Include the footer
include 'footer.php';
?>


    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        function loadTickets() {
            $.ajax({
                url: 'fetch_admin_tickets.php',
                method: 'GET',
                success: function(data) {
                    $('#ticket-table-body').html(data);
                }
            });
        }

        $(document).ready(function() {
            loadTickets(); // Initial load
            setInterval(loadTickets, 5000); // Reload every 10 seconds
        });
    </script>
</body>
</html>

BRANCH DASH BRANCH DASH BRANCH DASH BRANCH DASH BRANCH DASH BRANCH DASH BRANCH DASH BRANCH DASH BRANCH DASH BRANCH DASH BRANCH DASH 

<?php
session_start();
include 'db.php';
include 'header.php';
date_default_timezone_set('Asia/Manila');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'branch') {
    header("Location: login.php");
    exit();
}

$branch_id = $_SESSION['user_id'];
$current_user_name = $_SESSION['name'];

$query = "SELECT t.*, GROUP_CONCAT(CONCAT(c.comment, ' (', u.name, ') at ', c.created_at) SEPARATOR '<br>') as comments
FROM tickets t
LEFT JOIN comments c ON t.id = c.ticket_id
LEFT JOIN users u ON c.user_id = u.id
WHERE t.branch_id = $branch_id AND DATE(t.created_at) = CURDATE()
GROUP BY t.id";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="styles.css" rel="stylesheet">
    <title>Branch Dashboard</title>
    <style>
        .pending {
            background-color: orange;
        }
        .in-progress {
            background-color: lightblue;
            color: white;
        }
        .done {
            background-color: yellowgreen;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container-fluid mt-3">
        <div class="d-flex justify-content-between">
            <h2><strong>Branch Dashboard</strong></h2>
        </div>
        <button class="btn btn-primary" data-toggle="modal" data-target="#createTicketModal">Create New Ticket</button>
        <table class="table table-bordered mt-3" style="width: 100%;">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Category</th>
                    <th>Description</th>
                    <th>Status</th>
                    <th>Time Created</th>
                    <th>Time Updated</th>
                    <th>Actions</th>
                    <th>Comments</th>
                </tr>
            </thead>
            <tbody id="ticket-table-body">
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr class="<?php echo strtolower(str_replace(' ', '-', $row['status'])); ?>">
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo $row['title']; ?></td>
                        <td><?php echo $row['description']; ?></td>
                        <td><?php echo $row['status']; ?></td>
                        <td><?php echo $row['created_at']; ?></td>
                        <td><?php echo $row['updated_at']; ?></td>
                        <td>
                            <?php if ($row['status'] == 'Pending'): ?>
                                <button class="btn btn-sm btn-info" data-toggle="modal" data-target="#updateTicketModal<?php echo $row['id']; ?>">Edit</button>
                                <button class="btn btn-sm btn-danger" data-toggle="modal" data-target="#cancelTicketModal<?php echo $row['id']; ?>">Cancel</button>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php
                            $ticket_id = $row['id'];
                            $comment_query = "SELECT c.*, u.name FROM comments c JOIN users u ON c.user_id = u.id WHERE c.ticket_id = $ticket_id";
                            $comment_result = mysqli_query($conn, $comment_query);
                            while ($comment_row = mysqli_fetch_assoc($comment_result)):
                                ?>
                                <p><strong><?php echo $comment_row['name']; ?>:</strong> <?php echo $comment_row['comment']; ?> <small>(<?php echo $comment_row['created_at']; ?>)</small></p>
                            <?php endwhile; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <!-- Create Ticket Modal -->
    <div class="modal fade" id="createTicketModal" tabindex="-1" role="dialog" aria-labelledby="createTicketModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createTicketModalLabel">Create New Ticket</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="create_ticket.php" method="post">
                        <div class="form-group">
                            <label for="category">Category</label>
                            <select class="form-control" id="category" name="category">
                                <option value="Technical Concern">Technical Concern</option>
                                <option value="System Concern">System Concern</option>
                                <option value="Request">Request</option>

                            </select>
                        </div>
                        <div class="form-group">
                            <label for="description">Description:</label>
                            <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Create</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Update Ticket Modal Template -->
    <div class="modal fade" id="updateTicketModalTemplate" tabindex="-1" role="dialog" aria-labelledby="updateTicketModalLabelTemplate" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="updateTicketModalLabelTemplate">Update Ticket</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="updateTicketFormTemplate" action="edit_ticket.php" method="post">
                        <input type="hidden" name="ticket_id" id="updateTicketIdTemplate">
                        <div class="form-group">
                            <label for="category">Category</label>
                            <select class="form-control" id="category" name="category">
                                <option value="Technical Concern">Technical Concern</option>
                                <option value="System Concern">System Concern</option>
                                <option value="Request">Request</option>

                            </select>
                        </div>
                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea class="form-control" id="updateDescriptionTemplate" name="description" rows="3" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Cancel Ticket Modal Template -->
    <div class="modal fade" id="cancelTicketModalTemplate" tabindex="-1" role="dialog" aria-labelledby="cancelTicketModalLabelTemplate" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="cancelTicketModalLabelTemplate">Cancel Ticket</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="cancelTicketFormTemplate" action="cancel_ticket.php" method="post">
                        <input type="hidden" name="ticket_id" id="cancelTicketIdTemplate">
                        <p>Are you sure you want to cancel this ticket?</p>
                        <button type="submit" class="btn btn-danger">Cancel Ticket</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?php
// Include the footer
    include 'footer.php';
    ?>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        function loadTickets() {
            $.ajax({
                url: 'fetch_branch_tickets.php',
                method: 'GET',
                success: function(data) {
                    $('#ticket-table-body').html(data);
                    attachModalHandlers();
                }
            });
        }

        function attachModalHandlers() {
            // Attach modal handlers for each ticket row
            $('button[data-target^="#updateTicketModal"]').on('click', function() {
                var ticketId = $(this).data('ticket-id');
                var title = $(this).data('title');
                var description = $(this).data('description');
                $('#updateTicketIdTemplate').val(ticketId);
                $('#updateTitleTemplate').val(title);
                $('#updateDescriptionTemplate').val(description);
                $('#updateTicketModalTemplate').modal('show');
            });

            $('button[data-target^="#cancelTicketModal"]').on('click', function() {
                var ticketId = $(this).data('ticket-id');
                $('#cancelTicketIdTemplate').val(ticketId);
                $('#cancelTicketModalTemplate').modal('show');
            });
        }

        $(document).ready(function() {
            loadTickets(); // Initial load
            setInterval(loadTickets, 5000); // Reload every 10 seconds
        });
    </script>
</body>
</html>

