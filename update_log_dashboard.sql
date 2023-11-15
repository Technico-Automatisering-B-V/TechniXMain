SET sql_log_bin = 0;
TRUNCATE technix_log.log_dashboard;

DROP VIEW IF EXISTS technix_log.`tmp_loadtime_v`;

CREATE VIEW technix_log.`tmp_loadtime_v` AS (select ddl.circulationgroup_id, DATE(starttime) date,HOUR(starttime) hour,
                            ROUND(COUNT(*)) count FROM log_distributors_load l
                            INNER JOIN distributors dd ON dd.id = l.distributor_id
                            INNER JOIN distributorlocations ddl ON ddl.id = dd.distributorlocation_id 
                            WHERE starttime >= DATE(NOW()) - INTERVAL 1 YEAR
                            GROUP BY ddl.id,DATE(starttime), HOUR(starttime)
                            HAVING MINUTE(MIN(starttime)) < 10 AND MINUTE(MAX(starttime))> 50);

DROP TABLE IF EXISTS technix_log.`tmp_loadtime_reject_v`;

CREATE TABLE technix_log.`tmp_loadtime_reject_v` AS (SELECT *,ROUND(COUNT(*)) count FROM (select ddl.id distributorlocation_id, ddl.circulationgroup_id, DATE(starttime) date,starttime as fulltime, HOUR(starttime) hour
			 FROM log_distributors_load l
						INNER JOIN distributors dd ON dd.id = l.distributor_id
						INNER JOIN distributorlocations ddl ON ddl.id = dd.distributorlocation_id 
						WHERE starttime >= DATE(NOW()) - INTERVAL 1 YEAR
			 UNION
			SELECT dl.id distributorlocation_id, dl.circulationgroup_id,DATE(date) date, date as fulltime, HOUR(date) hour
			FROM log_rejected_garments lr
			INNER JOIN distributorlocations dl ON dl.id = lr.distributorlocation_id
			WHERE date >= DATE(NOW()) - INTERVAL 1 YEAR) t1
			GROUP BY t1.distributorlocation_id,date, hour
            HAVING MINUTE(MIN(fulltime)) < 10 AND MINUTE(MAX(fulltime))> 50
                        );

DROP VIEW IF EXISTS technix_log.`tmp_misseized_v`;

CREATE VIEW technix_log.`tmp_misseized_v` AS (SELECT DATE(date) date, COUNT(DISTINCT(garmentuser_id)) cc, distributorlocation_id 
                        FROM log_distributorclients
                        WHERE userbound = 0 AND numgarments = 0 AND superuser_id = 0 AND date >= DATE(NOW()) - INTERVAL 1 YEAR
			GROUP BY distributorlocation_id, DATE(date));

DROP VIEW IF EXISTS technix_log.`tmp_login_v`;
CREATE VIEW technix_log.`tmp_login_v` AS (SELECT dl.circulationgroup_id, DATE(ld.date) date
                    FROM log_distributorclients ld
                    INNER JOIN distributorlocations dl ON dl.id = ld.distributorlocation_id
                    WHERE ld.date >= DATE(NOW()) - INTERVAL 1 YEAR AND (ISNULL(ld.superuser_id) OR ld.superuser_id = 0)
                    GROUP BY dl.circulationgroup_id, DATE(ld.date), ld.garmentuser_id);


INSERT INTO technix_log.log_dashboard
SELECT dl.circulationgroup_id,DATE(lg.starttime) date,DAYNAME(lg.starttime) day,
                              FLOOR(AVG(TIMESTAMPDIFF(SECOND,lg.starttime, lg.endtime))), 'distributiontime_week'
        FROM
        log_garmentusers_garments lg
        INNER JOIN distributors d ON d.id = lg.distributor_id
        INNER JOIN distributorlocations dl ON dl.id = d.distributorlocation_id
        WHERE lg.starttime >= DATE(NOW()) - INTERVAL 6 DAY
        GROUP BY dl.circulationgroup_id, YEAR(lg.starttime), MONTH(lg.starttime), DATE(lg.starttime)
        ORDER BY lg.starttime;

INSERT INTO technix_log.log_dashboard
SELECT dl.circulationgroup_id, DATE(lg.starttime) date, DAYNAME(lg.starttime) day,
                               FLOOR(AVG(TIMESTAMPDIFF(SECOND,lg.starttime, lg.endtime))), 'distributiontime_month'
        FROM
        log_garmentusers_garments lg
        INNER JOIN distributors d ON d.id = lg.distributor_id
        INNER JOIN distributorlocations dl ON dl.id = d.distributorlocation_id
        WHERE lg.starttime >= DATE(NOW()) - INTERVAL 4 WEEK
        GROUP BY dl.circulationgroup_id, YEAR(lg.starttime), MONTH(lg.starttime), DATE(lg.starttime);

INSERT INTO technix_log.log_dashboard
SELECT dl.circulationgroup_id, DATE(lg.starttime) date ,MONTHNAME(lg.starttime) day,
                              FLOOR(AVG(TIMESTAMPDIFF(SECOND,lg.starttime, lg.endtime))), 'distributiontime_year'
        FROM
        log_garmentusers_garments lg
        INNER JOIN distributors d ON d.id = lg.distributor_id
        INNER JOIN distributorlocations dl ON dl.id = d.distributorlocation_id
        WHERE lg.starttime >= DATE(NOW()) - INTERVAL 1 YEAR
        GROUP BY dl.circulationgroup_id, YEAR(lg.starttime), MONTH(lg.starttime);

INSERT INTO technix_log.log_dashboard
SELECT c.id, IF(ISNULL(ld.circulationgroup_id), DATE(NOW()), ld.date),
                            IF(ISNULL(ld.circulationgroup_id), DAYNAME(NOW()), DAYNAME(ld.date)),
                            IF(ISNULL(ld.circulationgroup_id), 0, FLOOR(AVG(ld.count))), 'loadtime_week'
                    FROM circulationgroups c
                    LEFT JOIN 
                        (SELECT t1.date,t1.circulationgroup_id,ROUND(SUM(t1.count)) count FROM
                            (select ddl.circulationgroup_id, DATE(starttime) date,HOUR(starttime) hour,
                            ROUND(COUNT(*)) count FROM log_distributors_load l
                            INNER JOIN distributors dd ON dd.id = l.distributor_id
                            INNER JOIN distributorlocations ddl ON ddl.id = dd.distributorlocation_id 
                            WHERE starttime >= DATE(NOW()) - INTERVAL 6 DAY
                            GROUP BY ddl.id,DATE(starttime), HOUR(starttime)
                            HAVING MINUTE(MIN(starttime)) < 10 AND MINUTE(MAX(starttime))> 50
                        ) t1
                        GROUP BY t1.circulationgroup_id, t1.date,t1.hour
                        HAVING COUNT(*) = (SELECT COUNT(*) FROM distributorlocations WHERE circulationgroup_id = t1.circulationgroup_id)) ld ON ld.circulationgroup_id = c.id
                    GROUP BY ld.circulationgroup_id, ld.date
                    ORDER BY ld.date;
					
INSERT INTO technix_log.log_dashboard
SELECT c.id,
                            IF(ISNULL(ld.circulationgroup_id), DATE(NOW()), ld.date),
                            IF(ISNULL(ld.circulationgroup_id), DAYNAME(NOW()), DAYNAME(ld.date)),
                            IF(ISNULL(ld.circulationgroup_id), 0, FLOOR(AVG(ld.count))), 'loadtime_reject_week'
                    FROM circulationgroups c
                    LEFT JOIN 
						(SELECT t1.date,t1.circulationgroup_id,ROUND(SUM(t1.count)) count FROM
							(SELECT *,ROUND(COUNT(*)) count FROM (select ddl.id distributorlocation_id, ddl.circulationgroup_id, DATE(starttime) date,starttime as fulltime, HOUR(starttime) hour
						 FROM log_distributors_load l
									INNER JOIN distributors dd ON dd.id = l.distributor_id
									INNER JOIN distributorlocations ddl ON ddl.id = dd.distributorlocation_id 
									WHERE starttime >= DATE(NOW()) - INTERVAL 6 DAY
						 UNION
						SELECT dl.id distributorlocation_id, dl.circulationgroup_id,DATE(date) date, date as fulltime, HOUR(date) hour
						FROM log_rejected_garments lr
						INNER JOIN distributorlocations dl ON dl.id = lr.distributorlocation_id
						WHERE date >= DATE(NOW()) - INTERVAL 6 DAY) t1
						GROUP BY t1.distributorlocation_id,date, hour
									HAVING MINUTE(MIN(fulltime)) < 10 AND MINUTE(MAX(fulltime))> 50) t1
                        GROUP BY t1.circulationgroup_id, t1.date,t1.hour
                        HAVING COUNT(*) = (SELECT COUNT(*) FROM distributorlocations WHERE circulationgroup_id = t1.circulationgroup_id)) ld ON ld.circulationgroup_id = c.id
                    GROUP BY ld.circulationgroup_id, ld.date
                    ORDER BY ld.date;
					


INSERT INTO technix_log.log_dashboard
SELECT ld.circulationgroup_id,
                            ld.date,
                            DAYNAME(ld.date) day,
                            FLOOR(AVG(ld.count)), 'loadtime_month'
                    FROM (
                        SELECT t1.date,t1.circulationgroup_id,ROUND(SUM(t1.count)) count FROM
                            technix_log.tmp_loadtime_v t1
                        GROUP BY t1.circulationgroup_id, t1.date,t1.hour
                        HAVING COUNT(*) = (SELECT COUNT(*) FROM distributorlocations WHERE circulationgroup_id = t1.circulationgroup_id)) ld
                    WHERE ld.date >= DATE(NOW()) - INTERVAL 4 WEEK 
                    GROUP BY ld.circulationgroup_id, ld.date
                    ORDER BY ld.date;
					
INSERT INTO technix_log.log_dashboard
SELECT ld.circulationgroup_id,
                            ld.date,
                            DAYNAME(ld.date) day,
                            FLOOR(AVG(ld.count)) average, 'loadtime_reject_month'
                    FROM (
                        SELECT t1.date,t1.circulationgroup_id,ROUND(SUM(t1.count)) count FROM
                            technix_log.tmp_loadtime_reject_v t1
                        GROUP BY t1.circulationgroup_id, t1.date,t1.hour
                        HAVING COUNT(*) = (SELECT COUNT(*) FROM distributorlocations WHERE circulationgroup_id = t1.circulationgroup_id)) ld
                    WHERE ld.date >= DATE(NOW()) - INTERVAL 4 WEEK 
                    GROUP BY ld.circulationgroup_id, ld.date
                    ORDER BY ld.date;
					
INSERT INTO technix_log.log_dashboard
SELECT ld.circulationgroup_id,
                            ld.date,
                            MONTHNAME(ld.date) day,
                            FLOOR(AVG(ld.count)), 'loadtime_year'
                    FROM (
                        SELECT t1.date,t1.circulationgroup_id,ROUND(SUM(t1.count)) count FROM
                            technix_log.tmp_loadtime_v t1
                        GROUP BY t1.circulationgroup_id, t1.date,t1.hour
                        HAVING COUNT(*) = (SELECT COUNT(*) FROM distributorlocations WHERE circulationgroup_id = t1.circulationgroup_id)) ld
                    WHERE ld.date >= DATE(NOW()) - INTERVAL 1 YEAR
                    GROUP BY ld.circulationgroup_id, MONTH(ld.date)
                    ORDER BY ld.date;
					
INSERT INTO technix_log.log_dashboard
SELECT ld.circulationgroup_id,
                            ld.date,
                            MONTHNAME(ld.date) day,
                            FLOOR(AVG(ld.count)), 'loadtime_reject_year'
                    FROM (
                        SELECT t1.date,t1.circulationgroup_id,ROUND(SUM(t1.count)) count FROM
                            technix_log.tmp_loadtime_reject_v t1
                        GROUP BY t1.circulationgroup_id, t1.date,t1.hour
                        HAVING COUNT(*) = (SELECT COUNT(*) FROM distributorlocations WHERE circulationgroup_id = t1.circulationgroup_id)) ld
                    WHERE ld.date >= DATE(NOW()) - INTERVAL 1 YEAR
                    GROUP BY ld.circulationgroup_id, MONTH(ld.date)
                    ORDER BY ld.date;

INSERT INTO technix_log.log_dashboard
SELECT d.circulationgroup_id,t1.date, 
                            DAYNAME(t1.date) day, ROUND(SUM(t1.misseized)/SUM(t1.distributed)*100,2), 'misseized_week'  FROM (
            SELECT l1.distributorlocation_id, DATE(l1.date) date,COUNT(*) distributed, IF(ISNULL(l2.cc),'0',l2.cc) misseized
            FROM log_distributorclients l1
            LEFT JOIN (SELECT DATE(date) date, COUNT(DISTINCT(garmentuser_id)) cc, distributorlocation_id 
                        FROM log_distributorclients
                        WHERE userbound = 0 AND numgarments = 0 AND superuser_id = 0 AND date >= DATE(NOW()) - INTERVAL 6 DAY
			GROUP BY distributorlocation_id, DATE(date)) l2 
            ON DATE(l2.date) = DATE(l1.date) AND l2.distributorlocation_id = l1.distributorlocation_id
            WHERE l1.date >= DATE(NOW()) - INTERVAL 6 DAY AND l1.numgarments > 0  AND l1.userbound = 0  AND l1.superuser_id = 0
            GROUP BY l1.distributorlocation_id,DATE(l1.date)) t1
            INNER JOIN distributorlocations d ON d.id = t1.distributorlocation_id
            GROUP BY d.circulationgroup_id, t1.date
            ORDER BY t1.date;

INSERT INTO technix_log.log_dashboard
SELECT d.circulationgroup_id,t1.date, 
                            DAYNAME(t1.date) day, ROUND(SUM(t1.misseized)/SUM(t1.distributed)*100,2), 'misseized_month'  FROM (
            SELECT l1.distributorlocation_id, DATE(l1.date) date,COUNT(*) distributed, IF(ISNULL(l2.cc),'0',l2.cc) misseized
            FROM log_distributorclients l1
            LEFT JOIN technix_log.tmp_misseized_v l2 
            ON DATE(l2.date) = DATE(l1.date) AND l2.distributorlocation_id = l1.distributorlocation_id
            WHERE l1.date >= DATE(NOW()) - INTERVAL 4 WEEK AND l1.numgarments > 0  AND l1.userbound = 0  AND l1.superuser_id = 0
            GROUP BY l1.distributorlocation_id,DATE(l1.date)) t1
            INNER JOIN distributorlocations d ON d.id = t1.distributorlocation_id
            GROUP BY d.circulationgroup_id, t1.date
            ORDER BY t1.date;

INSERT INTO technix_log.log_dashboard
SELECT d.circulationgroup_id,t1.date, 
                            MONTHNAME(t1.date) day, ROUND(SUM(t1.misseized)/SUM(t1.distributed)*100,2), 'misseized_year'  FROM (
            SELECT l1.distributorlocation_id, DATE(l1.date) date,COUNT(*) distributed, IF(ISNULL(l2.cc),'0',l2.cc) misseized
            FROM log_distributorclients l1
            LEFT JOIN technix_log.tmp_misseized_v l2 
            ON DATE(l2.date) = DATE(l1.date) AND l2.distributorlocation_id = l1.distributorlocation_id
            WHERE l1.date >= DATE(NOW()) - INTERVAL 1 YEAR AND l1.numgarments > 0  AND l1.userbound = 0  AND l1.superuser_id = 0
            GROUP BY l1.distributorlocation_id,DATE(l1.date)) t1
            INNER JOIN distributorlocations d ON d.id = t1.distributorlocation_id
            GROUP BY d.circulationgroup_id, MONTH(t1.date)
            ORDER BY t1.date;

INSERT INTO technix_log.log_dashboard
SELECT circulationgroup_id,
                    date,
                    DAYNAME(date) day,
                    COUNT(*), 'login_week'
            FROM ( SELECT dl.circulationgroup_id, DATE(ld.date) date
                    FROM log_distributorclients ld
                    INNER JOIN distributorlocations dl ON dl.id = ld.distributorlocation_id
                    WHERE ld.date >= DATE(NOW()) - INTERVAL 6 DAY AND (ISNULL(ld.superuser_id) OR ld.superuser_id = 0)
                    GROUP BY dl.circulationgroup_id, DATE(ld.date), ld.garmentuser_id) t1
            GROUP BY t1.circulationgroup_id, t1.date
                    ORDER BY t1.date;

INSERT INTO technix_log.log_dashboard
SELECT circulationgroup_id,
                    date,
                    DAYNAME(date) day,
                    COUNT(*), 'login_month'
            FROM technix_log.`tmp_login_v` t1
            WHERE t1.date >= DATE(NOW()) - INTERVAL 4 WEEK
            GROUP BY t1.circulationgroup_id, t1.date
                    ORDER BY t1.date;

INSERT INTO technix_log.log_dashboard
SELECT circulationgroup_id,
                    date,
                    MONTHNAME(date) day,
                    COUNT(*), 'login_year'
            FROM technix_log.`tmp_login_v` t1
            GROUP BY t1.circulationgroup_id, MONTH(t1.date)
                    ORDER BY t1.date;

INSERT INTO technix_log.log_dashboard
SELECT dl.circulationgroup_id, DATE(lg.endtime) date, DAYNAME(lg.endtime) day, COUNT(*), 'distributioncount_week'
                    FROM log_garmentusers_garments lg
                    INNER JOIN distributors d ON d.id = lg.distributor_id
                    INNER JOIN distributorlocations dl ON dl.id = d.distributorlocation_id
                    WHERE lg.endtime >= DATE(NOW()) - INTERVAL 6 DAY AND (ISNULL(lg.superuser_id) OR lg.superuser_id = 0)
                    GROUP BY dl.circulationgroup_id, DATE(lg.endtime);

INSERT INTO technix_log.log_dashboard
SELECT dl.circulationgroup_id, DATE(lg.endtime) date, DAYNAME(lg.endtime) day, COUNT(*), 'distributioncount_month'
                    FROM log_garmentusers_garments lg
                    INNER JOIN distributors d ON d.id = lg.distributor_id
                    INNER JOIN distributorlocations dl ON dl.id = d.distributorlocation_id
                    WHERE lg.endtime >= DATE(NOW()) - INTERVAL 4 WEEK AND (ISNULL(lg.superuser_id) OR lg.superuser_id = 0)
                    GROUP BY dl.circulationgroup_id, DATE(lg.endtime)
                    ORDER BY lg.endtime;

INSERT INTO technix_log.log_dashboard
SELECT dl.circulationgroup_id, DATE(lg.endtime) date, MONTHNAME(lg.endtime) day, COUNT(*), 'distributioncount_year'
                    FROM log_garmentusers_garments lg
                    INNER JOIN distributors d ON d.id = lg.distributor_id
                    INNER JOIN distributorlocations dl ON dl.id = d.distributorlocation_id
                    WHERE lg.endtime >= DATE(NOW()) - INTERVAL 1 YEAR AND (ISNULL(lg.superuser_id) OR lg.superuser_id = 0)
                    GROUP BY dl.circulationgroup_id, MONTH(lg.endtime)
                    ORDER BY lg.endtime;

INSERT INTO technix_log.log_dashboard
SELECT t1.circulationgroup_id,t1.date,t1.day, ROUND(AVG(cc),2), 'distributionuser_month'
                    FROM (SELECT dl.circulationgroup_id, YEAR(lg.endtime) year, DATE(lg.endtime) date, WEEK(lg.endtime, 1) day, COUNT(*) cc
                    FROM log_garmentusers_garments lg
                    INNER JOIN distributors d ON d.id = lg.distributor_id
                    INNER JOIN distributorlocations dl ON dl.id = d.distributorlocation_id
                    WHERE lg.endtime BETWEEN subdate(curdate() - INTERVAL 3 WEEK,dayofweek(curdate() - INTERVAL 3 WEEK)+5)
					AND subdate(curdate(),dayofweek(curdate())-2)
					AND (ISNULL(lg.superuser_id) OR lg.superuser_id = 0) 
                    GROUP BY dl.circulationgroup_id, lg.garmentuser_id,WEEK(lg.endtime, 1)) t1
					GROUP BY t1.circulationgroup_id,t1.year,t1.day;
					
INSERT INTO technix_log.log_dashboard
select t1.circulationgroup_id,t1.date,t1.day, ROUND(AVG(cc), 2), 'distributionuser_year'
                FROM (SELECT dl.circulationgroup_id, YEAR(lg.endtime) year, DATE(lg.endtime) date, MONTHNAME(lg.endtime) day, COUNT(*) cc
                    FROM log_garmentusers_garments lg
                    INNER JOIN distributors d ON d.id = lg.distributor_id
                    INNER JOIN distributorlocations dl ON dl.id = d.distributorlocation_id
                    WHERE lg.endtime BETWEEN subdate(curdate() - INTERVAL 1 YEAR,dayofweek(curdate() - INTERVAL 3 WEEK)+5)
													AND subdate(curdate(),dayofweek(curdate())-2) AND WEEK(lg.endtime, 1) NOT IN (1,53)
					AND (ISNULL(lg.superuser_id) OR lg.superuser_id = 0) 
                    GROUP BY dl.circulationgroup_id, lg.garmentuser_id,WEEK(lg.endtime, 1)) t1
                GROUP BY circulationgroup_id, day;
				
INSERT INTO technix_log.log_dashboard
SELECT t1.circulationgroup_id,t1.date,t1.day, ROUND(AVG(cc)), 'distributionstation_week'
                FROM (SELECT dl.circulationgroup_id, DATE(lg.endtime) date, DAYNAME(lg.endtime) day, COUNT(*) cc
                    FROM log_garmentusers_garments lg
                    INNER JOIN distributors d ON d.id = lg.distributor_id
                    INNER JOIN distributorlocations dl ON dl.id = d.distributorlocation_id
                    WHERE lg.endtime >= DATE(NOW()) - INTERVAL 6 DAY AND (ISNULL(lg.superuser_id) OR lg.superuser_id = 0)
                    GROUP BY dl.circulationgroup_id, DATE(lg.endtime), lg.distributor_id) t1
                GROUP BY circulationgroup_id,date;
				
INSERT INTO technix_log.log_dashboard
SELECT t1.circulationgroup_id,t1.date,t1.day, ROUND(AVG(cc)), 'distributionstation_month'
                FROM (SELECT dl.circulationgroup_id, DATE(lg.endtime) date, DAYNAME(lg.endtime) day, COUNT(*) cc
                    FROM log_garmentusers_garments lg
                    INNER JOIN distributors d ON d.id = lg.distributor_id
                    INNER JOIN distributorlocations dl ON dl.id = d.distributorlocation_id
                    WHERE lg.endtime >= DATE(NOW()) - INTERVAL 4 WEEK AND (ISNULL(lg.superuser_id) OR lg.superuser_id = 0)
                    GROUP BY dl.circulationgroup_id, DATE(lg.endtime), lg.distributor_id
                    ORDER BY lg.endtime) t1
                GROUP BY circulationgroup_id, date;
				
INSERT INTO technix_log.log_dashboard
select t1.circulationgroup_id,t1.date,t1.day, ROUND(AVG(cc)), 'distributionstation_year'
                FROM (SELECT dl.circulationgroup_id, DATE(lg.endtime) date, MONTHNAME(lg.endtime) day, COUNT(*) CC
                    FROM log_garmentusers_garments lg
                    INNER JOIN distributors d ON d.id = lg.distributor_id
                    INNER JOIN distributorlocations dl ON dl.id = d.distributorlocation_id
                    WHERE lg.endtime >= DATE(NOW()) - INTERVAL 1 YEAR AND (ISNULL(lg.superuser_id) OR lg.superuser_id = 0)
                    GROUP BY dl.circulationgroup_id, DATE(lg.endtime), lg.distributor_id
                    ORDER BY lg.endtime) t1
                GROUP BY circulationgroup_id, day;
SET sql_log_bin = 1;