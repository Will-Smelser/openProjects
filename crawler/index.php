<?php
/**
 * Author: Will Smelser
 * Date: 1/10/14
 * Time: 11:09 PM
 * Project: openProjects
 */
error_reporting(E_ALL);
include 'required/class/Crawler.php';

$crawler = new Crawler('http://simple-seo-api.local','http://openprojects.local/crawler/required/class/PageLoadThread.php');

$result = $crawler->start();

var_dump($result);
