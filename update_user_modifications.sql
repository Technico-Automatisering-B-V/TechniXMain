SET sql_log_bin = 0;
				
INSERT INTO history_garmentusers
SELECT gu.id garmentuser_id, gu.`name` garmentusers_name, gu.surname garmentusers_surname, gu.personnelcode garmentusers_personnelcode,
gu.`code` garmentusers_code, gu.date_service_on garmentusers_date_service_on, gu.date_service_off garmentusers_date_service_off,
f.`value` function, c.`name` clientdepartment ,p.name profession, gg.garments_in_use, IF(ISNULL(g.id),'no','yes') clothing, DATE(NOW()) date
FROM garmentusers gu
LEFT JOIN functions f ON f.id = gu.function_id
LEFT JOIN clientdepartments c ON c.id = gu.clientdepartment_id
LEFT JOIN professions p ON p.id = gu.profession_id
LEFT JOIN (SELECT garmentuser_id, COUNT(*) garments_in_use FROM garmentusers_garments GROUP BY garmentuser_id) gg ON gg.garmentuser_id = gu.id
LEFT JOIN garments g ON g.garmentuser_id = gu.id
WHERE ISNULL(gu.deleted_on)
GROUP BY gu.id;

SET sql_log_bin = 1;