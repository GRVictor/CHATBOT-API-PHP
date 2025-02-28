<?php
    class Connect{
        protected $dbh;

        protected function connection(){
            try{
                $connect = $this->dbh = new PDO("mysql:local=localhost;dbname=dbnwz5uj73miwg","uzef7j7tfejm5","125^763+f~A2");
                // error_log("connection - Database connection established"); // Comment out for debugging
                return $connect;
            }catch(Exception $e){
                // error_log("connection - Error BD: " . $e->getMessage()); // Comment out for debugging
                print "Error BD:" . $e->getMessage() . "<br>";
                die();
            }
        }

        public function set_names(){
            // error_log("set_names - Setting character set to utf8"); // Comment out for debugging
            return $this->dbh->query("SET NAMES 'utf8'");
        }
    }
?>