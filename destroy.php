<?php
/**
 * Created by PhpStorm.
 * User: Melle Dijkstra
 * Date: 1-3-2016
 * Time: 20:33
 */

session_start();

session_destroy();

echo 'Session destroyed!';