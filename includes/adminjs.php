<script>
$(document).ready(function() {
        loadTickets();
        <?php foreach ($available_counters as $counter) { ?>
        loadQ<?php echo $counter['userid']; ?>();
        <?php } ?>
        setInterval(function() { 
          loadTickets();
          <?php foreach ($available_counters as $counter) { ?>
          loadQ<?php echo $counter['userid']; ?>();
          <?php } ?>
        }, 5000);
        <?php foreach ($available_counters as $counter) { ?>
        function loadQ<?php echo $counter['userid']; ?>() {
            $.ajax({
                url: '../load/counter' + <?php echo $counter['userid']; ?> + '.php',
                method: 'GET',
                success: function(data) {
                    $('#queue-table' + <?php echo $counter['userid']; ?>).html(data);
                }
            });
        }
        <?php } ?>

        function loadTickets() {
            $.ajax({
                url: '../load/loadqueue.php',
                method: 'GET',
                success: function(data) {
                    $('#ticket-table-body').html(data);
                }
            });
        }

        $(document).on('contextmenu', function(e) {
            e.preventDefault();
        });

        $(document).on('contextmenu', '#queuetable table tbody tr', function(e) {
            $('.removedrop').remove();
            var rowData = $(this).children('td').map(function() {
                return $(this).text();
            }).get();
            console.log(rowData);
            var id = rowData[0]; 
            var served = rowData[1];
            var branchid = rowData[2];
            var menu = $('<div class="dropdown-menu small removedrop" id="actiondropdown" style="display:block; position:absolute; z-index:1000;">'
                        + (served == '<?php echo $_SESSION['user_id']; ?>' ? '<a class="dropdown-item small" href="#" id="receive"><i class="fa fa-check-circle text-success" aria-hidden="true"></i> <span>Accomplished: Received</span></a>' : '<a class="dropdown-item small disabled" href="#" id="receive"><i class="fa fa-lock text-secondary dont" aria-hidden="true"></i> <span class="ml-2">Restricted</span></a>')
                        + (served == '<?php echo $_SESSION['user_id']; ?>' ? '<a class="dropdown-item small" href="#" id="decline"><i class="fa fa-times-circle text-danger" aria-hidden="true"></i> Accomplished: Declined</a>' : '')
                        + (served == '<?php echo $_SESSION['user_id']; ?>' ? '<a class="dropdown-item small" href="preview.php?id=' + id + '" id="preview"><i class="fa fa-eye text-info" aria-hidden="true"></i> Preview</a>' : '')
                        + (served == '<?php echo $_SESSION['user_id']; ?>' ? '<a class="dropdown-item small" href="#" id="return"><i class="fa fa-undo text-primary" aria-hidden="true"></i> Return to Queue</a>' : '')
                        + '</div>').appendTo('body');
            menu.css({top: e.pageY + 'px', left: e.pageX + 'px'});

            $('#receive').on('click', function() {
              swal({
                title: "Accomplished: Received",
                text: "Input Note",
                icon: "info",
                content: {
                  element: "textarea",
                  attributes: {
                    placeholder: "Enter Note",
                    rows: 5,
                    id: "note-textarea"
                  },
                },
                buttons: {
                  cancel: "Cancel",
                  confirm: "Confirm"
                }
              }).then((value) => {
                if (value) {
                  var note = $("#note-textarea").val();
                  $.ajax({
                    url: '../actions.php',
                    method: 'POST',
                    data: {id: id, note: note, action: 'receive'},
                    success: function() {
                      swal({
                        title: "Received",
                        text: "Payment Received",
                        icon: "success",
                        buttons: false,
                        timer: 1500
                      }).then(function() {
                        location.reload();
                      });
                    }
                  });
                }
              });
            });

            $('#decline').on('click', function() {
              swal({
                title: "Accomplished: Declined",
                text: "Input Note",
                icon: "info",
                content: {
                  element: "textarea",
                  attributes: {
                    placeholder: "Enter Note",
                    rows: 5,
                    id: "note-textarea1"
                  },
                },
                buttons: {
                  cancel: "Cancel",
                  confirm: "Confirm"
                }
              }).then((value) => {
                if (value) {
                  var note = $("#note-textarea1").val();
                  $.ajax({
                    url: '../actions.php',
                    method: 'POST',
                    data: {id: id, note: note, action: 'decline'},
                    success: function() {
                      swal({
                        title: "Declined",
                        text: "Payment Declined",
                        icon: "success",
                        buttons: false,
                        timer: 1500
                      }).then(function() {
                        location.reload();
                      });
                    }
                  });
                }
              });
            });

            $('#return').on('click', function() {
                $.ajax({
                    url: '../actions.php',
                    method: 'POST',
                    data: {id: id, action: 'return'},
                    success: function(response) {
                        swal({
                            title: 'Success',
                            text: 'Returned to Queue!',
                            icon: 'success',
                            timer: 1500,
                            buttons: false
                        });
                        loadTickets(); 
                        loadQ1();
                        loadQ2();
                        loadQ3();
                        loadQ4();
                    }
                });
            });
                $(document).on('click', function() {
                menu.remove();
            });
        });

        $(document).on('contextmenu', '#ticket-table tbody tr', function(e) {
            e.preventDefault();
            $('.removedrop').remove();

            var rowData = $(this).children('td').map(function() {
                return $(this).text();
            }).get();
            console.log(rowData);
            var id = rowData[0]; 
            var queueno = rowData[3];
            var menu = $('<div class="dropdown-menu small removedrop" id="queuedropdown" style="display:block; position:absolute; z-index:1000;">'
                        + '<span class="dropdown-item small">No.: <b class="small">' + queueno + '</b></span>'
                        + '<div class="dropdown-divider"></div>'
                        + '<a class="dropdown-item small serve" href="#"><i class="fa fa-check-circle text-success" aria-hidden="true"></i> Serve</a>'
                        + '<a class="dropdown-item small" href="preview.php?id=' + id + '" id="preview"><i class="fa fa-eye text-info" aria-hidden="true"></i> Preview</a>'
                        + '</div>').appendTo('body');
            menu.css({top: e.pageY + 'px', left: e.pageX + 'px'});

            $('.serve').on('click', function() {
                $.ajax({
                    url: '../actions.php',
                    method: 'POST',
                    data: {id: id, action: 'serve'},
                    success: function(response) {
                        swal({
                            title: 'Success',
                            text: 'Serving Client!',
                            icon: 'success',
                            timer: 1500,
                            buttons: false
                        });
                        loadTickets(); 
                        loadQ1();
                        loadQ2();
                        loadQ3();
                        loadQ4();
                    }
                });
                menu.remove();
            });

            $(document).on('click', function() {
                menu.remove();
            });
        });
        $(document).on('change', '#ticket-table tbody tr', function(e) {    
            e.preventDefault();
            $('#alert').removeClass('d-none');
            setTimeout(function() {
                $('#alert').addClass('d-none');
            }, 1500);
        })
    });
</script>