<?php
    class Register extends Connect{

        public function insert_register($log_number, $log_text){
            $connect = parent::connection();
            parent::set_names();
            $sql = "INSERT INTO tm_log (log_number, log_text, fech_create_at) VALUES (?, ?, now())";
            $sql = $connect->prepare($sql);
            $sql->bindValue(1, $log_number);
            $sql->bindValue(2, $log_text);

            error_log("insert_register - log_number: $log_number, log_text: $log_text");

            try {
                $sql->execute();
                error_log("insert_register - Data inserted successfully");
            } catch (Exception $e) {
                error_log("insert_register - Exception: " . $e->getMessage());
            }
        }

        public function insert_ticket($log_ticket, $log_number){ {
            $connect = parent::connection();
            parent::set_names();
            $sql = "INSERT INTO tm_log (log_ticket, log_number, fech_create_at) VALUES (?, ?, now())";
            $sql = $connect->prepare($sql);
            $sql->bindValue(1, $log_ticket);
            $sql->bindValue(2, $log_number);

            error_log("insert_ticket - log_ticket: $log_ticket");

            try {
                $sql->execute();
                error_log("insert_ticket - Data inserted successfully");
            } catch (Exception $e) {
                error_log("insert_ticket - Exception: " . $e->getMessage());
            }
        }

        }
    }
?>