SELECT 

	COUNT(DISTINCT(domainlist.domainname)) AS domains_in_foia_not_in_endpoint_count,
    	SUM(`npi_count`) AS providers_with_new_information_released
FROM `domainlist` 
LEFT JOIN nppes_endpoint_apr2021 AS endpoint ON 
	endpoint.endpoint_short_domain =
    domainlist.domainname
WHERE is_personal = 0 AND endpoint_short_domain IS NULL
