<?php
$dbname = "db_jemuran_auto";
$backup_file = "../../database/backup_" . date("Y-m-d_H-i-s") . ".sql";
$command = "mysqldump -u root $dbname > $backup_file";
system($command);
echo json_encode(["status"=>"success","message"=>"Backup berhasil","file"=>$backup_file]);
?>
