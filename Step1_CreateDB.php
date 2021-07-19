<?php
	require_once('util/run_sql_loop.function.php'); //allows us to run an array of sql commands easily.


	$needed_data_files = [

		'endpoint_csv_file' => './reference_data/endpoint_pfile_20050523-20210411.csv',
		'csv_file_location' => "./data/CareSet.NPI_domain_FOIA.Apr2021.csv",
		'domainlist_sql_file' => "./reference_data/domainlist.sql",
	];

	foreach($needed_data_files as $this_file_name => $file_location){

		if(!file_exists($file_location)){
			echo "Error: Looked for $file_location and did not find it\n";
			exit();
		}else{
			$$this_file_name = $file_location;
		}

	}



	$target_db = 'foia_npi_domain';
	$target_table = 'foia_npi_domain_apr2021';


	$sql = [];

	$sql['create the database if it does not yet exist']= "
CREATE DATABASE IF NOT EXISTS $target_db
";

	//LOAD the FOIA data first..

	$sql['drop the database foia table if it exists'] = "
DROP TABLE IF EXISTS $target_db.$target_table
";

	$sql['create foia table'] = "
CREATE TABLE $target_db.$target_table (
        `npi` BIGINT,
        `domainname` VARCHAR(46),
        INDEX(npi)
) ENGINE='DEFAULT'
";
	
	$sql['load the data into the foia table'] = "
LOAD DATA LOCAL INFILE '$csv_file_location'
INTO TABLE $target_db.$target_table
FIELDS TERMINATED BY ',' 
LINES TERMINATED BY '\\n' 
IGNORE 1 LINES
";

	//Load the endpoint data

	$sql['drop current endpoint table'] = "
DROP TABLE IF EXISTS $target_db.nppes_endpoint_apr2021
";

	$sql['create endpoint table'] = "
CREATE TABLE $target_db.nppes_endpoint_apr2021 (
        `npi` BIGINT,
        `endpoint_type` VARCHAR(7),
        `endpoint_type_description` VARCHAR(24),
        `endpoint` VARCHAR(100),
        `affiliation` VARCHAR(1),
        `endpoint_description` VARCHAR(196),
        `affiliation_legal_business_name` VARCHAR(66),
        `use_code` VARCHAR(6),
        `use_description` VARCHAR(33),
        `other_use_description` VARCHAR(74),
        `content_type` VARCHAR(5),
        `content_description` VARCHAR(5),
        `other_content_description` VARCHAR(145),
        `affiliation_address_line_one` VARCHAR(55),
        `affiliation_address_line_two` VARCHAR(55),
        `affiliation_address_city` VARCHAR(25),
        `affiliation_address_state` VARCHAR(7),
        `affiliation_address_country` VARCHAR(2),
        `affiliation_address_postal_code` VARCHAR(9),
        INDEX(npi),
        INDEX(affiliation_legal_business_name),
        INDEX(use_code),
        INDEX(affiliation_address_postal_code)
) ENGINE='DEFAULT'

";

	$sql['load the endpoint data' ] = "
LOAD DATA LOCAL INFILE '$endpoint_csv_file' 
INTO TABLE $target_db.nppes_endpoint_apr2021 
FIELDS ENCLOSED BY '\"' 
TERMINATED BY ',' 
ESCAPED BY '\"' 
LINES TERMINATED BY '\\n' 
IGNORE 1 LINES
";


	//load the domainlist... this has manually configured status of whether a domain is a personal email domain
	//the public version of the dataset has already been filtered by this file...

	$sql['drop current domainlist table'] = "
DROP TABLE IF EXISTS $target_db.domainlist
";

	$sql['create domainlist table'] = "
CREATE TABLE $target_db.domainlist (
  `domainname` varchar(50) CHARACTER SET utf8 NOT NULL,
  `is_personal` int(1) NOT NULL,
  `npi_count` bigint(21) NOT NULL
) ENGINE='DEFAULT'
";

	$sql['index domainlist'] = "
ALTER TABLE $target_db.domainlist
  ADD PRIMARY KEY (`domainname`);
";

	//we need to make sure that the data load goes into the right place..
	$sql['select the target db'] = "
use $target_db
";

	$sql_file = file_get_contents($domainlist_sql_file);

	$sql_insert_statements = explode(';',$sql_file); //thank gods that semicolon is not a common data point in domain names...

	foreach($sql_insert_statements as $sql_id => $this_insert_statement){
		$sql["Insert data $sql_id"] = $this_insert_statement;
	}


	//load the data from the file... 
	$is_echo_loop = true;

	run_sql_loop($sql,$is_echo_loop);

	
