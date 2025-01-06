<?php
$available_counters = $_SESSION['counterid'];

echo '<div id="queuetable">';

foreach ($available_counters as $counter) {
    echo '<div class="col-md" id="c' . $counter . '">
        <div class="br-section-wrapper counter mt-3" id="counter' . $counter . '">
            <div class="sticky-top bg-white" style="z-index: 100;">
                <div class="d-flex justify-content-between">
                    <h6 class="font-weight-bold">Counter ' . $counter . '</h6>
                    <span class="small">Running Collection: <strong><span id="c' . $counter . 'running"></span></strong></span>
                </div>
                <p class="font-weight-bold"></i> [Name]</p>
                <hr>
                <p class="small">Now Serving: </p>
            </div>
            <table class="table table-hover table-sm mt-3" id="queue-table' . $counter . '"> 
            </table>
        </div>
    </div>';
}

echo '</div>';