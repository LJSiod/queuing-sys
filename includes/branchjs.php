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

        $(document).on('contextmenu', '#queuetable table tbody tr',  function(e) {
            $('.removedrop').remove();
            var rowData = $(this).children('td').map(function() {
                return $(this).text();
            }).get();
            console.log(rowData);
            var id = rowData[0]; 
            var branchid = rowData[2];
            var menu = $('<div class="dropdown-menu small removedrop" style="display:block; position:absolute; z-index:1000;">'
                        + (branchid == '<?php echo $_SESSION['branch_id']; ?>' ? '<a class="dropdown-item small" href="preview.php?id=' + id + '" id="preview"><i class="fa fa-eye text-info" aria-hidden="true"></i> Preview</a>' : '<a class="dropdown-item small disabled" href="#" id="receive"><i class="fa fa-lock text-secondary" aria-hidden="true"></i> <span class="ml-2">Restricted</span></a>')
                        + '</div>').appendTo('body');
            menu.css({top: e.pageY + 'px', left: e.pageX + 'px'});

                $(document).on('click', function() {
                menu.remove();
            });
        });

        $(document).on('contextmenu', '#ticket-table tbody tr',  function(e) {
            $('.removedrop').remove();
            var rowData = $(this).children('td').map(function() {
                return $(this).text();
            }).get();
            console.log(rowData);
            var id = rowData[0]; 
            var branchid = rowData[2];
            var menu = $('<div class="dropdown-menu small removedrop" style="display:block; position:absolute; z-index:1000;">'
                        + (branchid == '<?php echo $_SESSION['branch_id']; ?>' ? '<a class="dropdown-item small" href="preview.php?id=' + id + '" id="preview"><i class="fa fa-eye text-info" aria-hidden="true"></i> Preview</a>' : '<a class="dropdown-item small disabled" href="#" id="receive"><i class="fa fa-lock text-secondary" aria-hidden="true"></i> <span class="ml-2">Restricted</span></a>')
                        // + (branchid == '<?php echo $_SESSION['branch_id']; ?>' ? '<a class="dropdown-item small" href="edit.php?id=' + id + '" id="edit"><i class="fa fa-edit text-primary" aria-hidden="true"></i> Edit</a>' : '')
                        + '</div>').appendTo('body');
            menu.css({top: e.pageY + 'px', left: e.pageX + 'px'});

                $(document).on('click', function() {
                menu.remove();
            });
        });
    });


    </script>