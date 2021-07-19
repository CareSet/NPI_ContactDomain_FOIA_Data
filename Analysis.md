Analysis of the NPPES FOIA Data
============

## Setup
* mysqli.allow_local_infile = On must be set in your php.ini file
* The csv file should be downloaded in the ./data directory. All scripts moving forward will assume that it is there.. 
* Presuming that you are running this analysis using a late version of Ubuntu+Mariadb
* Credentials for the database need to be included in a yaml file that lives in ./util/ directory..
* Run Step1_CreateDB.php with
```
php Step1_CreateDB.php
```
This should return "done. Processing run took ? minutes."
* 



