<?php 

echo "BEFORE";
exec(" java -jar sift.jar Penguins.jpg 3 3.6 10 8 64 1024");
echo "AFTER";

?>