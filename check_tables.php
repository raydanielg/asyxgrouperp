<?php
foreach(DB::select("SELECT name FROM sqlite_master WHERE type='table' ORDER BY name") as $t) {
    echo $t->name . PHP_EOL;
}
