SELECT 
		COUNT(DISTINCT(study.domainname)) AS domain_name_count,
     	COUNT(DISTINCT(foiadata.npi)) AS FOIA_npi_count,
        COUNT(DISTINCT(foiadata.npi)) * 1000 AS estimated_patient_panel,
    	COUNT(DISTINCT(paid_total_npi.npi)) AS paid_directly_npi_count,
    	SUM(paid_total_npi.total_paid) AS total_paid,
		SUM(
          IF(`is_plaintext_email_delivery_available` = 1 OR 
             `is_secure_email_non_Direct_delivery_available` = 1 OR 
             `is_Direct_delivery_available` = 1 OR 
             `is_portal_delivery_available` = 1 OR 
             `is_physical_disk_available` = 1 OR
             `is_undefined_digital_delivery_available` = 1 OR
             `is_FHIR_url_delivery_available` = 1 OR 
             `is_non_FHIR_app_delivery_available` =1, 1, 0)) AS sum_of_is_digital_delivery_available,
          SUM(
              	IF(`is_fax_delivery_available` =1 OR `is_mail_delivery_available` =1,1,0)) AS sum_of_is_mail_or_fax_available,
                		SUM(
          IF(`is_plaintext_email_delivery_available` = 1 OR 
             `is_secure_email_non_Direct_delivery_available` = 1 OR 
             `is_Direct_delivery_available` = 1 OR 
             `is_portal_delivery_available` = 1 OR 
             `is_physical_disk_available` = 1 OR
             `is_undefined_digital_delivery_available` = 1 OR
             `is_FHIR_url_delivery_available` = 1 OR 
             `is_non_FHIR_app_delivery_available` =1, 0, paid_total_npi.total_paid)) AS sum_paid_without_digital_access
FROM `full_manual_study` AS study
LEFT JOIN foia_cms_nppes_domain_npi_apr2021.foia_npi_domain_apr2021 AS foiadata ON 
	study.domainname =
    foiadata.domainname
 LEFT JOIN puf_cms_meaningful_use.paid_total_npi    ON 
 	paid_total_npi.npi =
    foiadata.npi
WHERE `patient_record_request_form_url` IS NOT NULL
