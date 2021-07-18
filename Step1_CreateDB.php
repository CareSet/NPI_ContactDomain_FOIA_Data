<?php
	require_once('util/run_sql_loop.function.php'); //allows us to run an array of sql commands easily.


	$csv_file_location = "./data/CareSet.NPI_domain_FOIA.Apr2021.csv";

	if(!file_exists($csv_file_location)){
		echo "Error: I am looking for \n$csv_file_location\n And I did not find it... please read the documentation\n";
		exit();
	}


	$target_db = 'foia_npi_domain';
	$target_table = 'foia_npi_domain_apr2021';


	$sql = [];

	$sql['create the database if it does not yet exist']= "
CREATE DATABASE IF NOT EXISTS $target_db
";

	$sql['drop the database table if it exists'] = "
DROP TABLE IF EXISTS $target_db.$target_table
";

	$sql['create table'] = "
CREATE TABLE $target_db.$target_table (
        `npi` BIGINT,
        `domainname` VARCHAR(46),
        INDEX(npi)
) ENGINE='DEFAULT'
";
	
	$sql['load the data'] = "
LOAD DATA LOCAL INFILE '$csv_file_location'
INTO TABLE $target_db.$target_table
FIELDS ENCLOSED BY '\"' 
TERMINATED BY ',' 
ESCAPED BY '\"' 
LINES TERMINATED BY '\\n' 
IGNORE 1 LINES
";

	$is_echo_loop = true;

	run_sql_loop($sql,$is_echo_loop);

	
