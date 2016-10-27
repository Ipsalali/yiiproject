use ycrm;

SELECT cc.*
FROM client cc
WHERE cc.id IN (
  case WHEN (SELECT p.id FROM payment_state p WHERE p.default = 1) = 4 
	THEN (SELECT c.id 
			FROM client c
            WHERE 
				(SELECT COUNT(a.id) FROM autotruck a 
					INNER JOIN customer_payment cp ON a.id = cp.autotruck_id
                    INNER JOIN payment_state ps  ON ps.id = cp.payment_state_id
                    WHERE cp.client_id = c.id AND ps.end_state = 1) !=
				(SELECT COUNT(a.id) FROM autotruck a
				INNER JOIN app ap ON ap.autotruck_id = a.id
				WHERE ap.client = c.id)
		  )
	WHEN (SELECT p.id FROM payment_state p WHERE p.end_state = 1) = 4
     THEN
		(SELECT c.id 
			FROM client c
            WHERE 
				(SELECT COUNT(a.id) FROM autotruck a 
					INNER JOIN customer_payment cp ON a.id = cp.autotruck_id
                    INNER JOIN payment_state ps  ON ps.id = cp.payment_state_id
                    WHERE cp.client_id = c.id AND ps.end_state = 1) =
				(SELECT COUNT(a.id) FROM autotruck a
				INNER JOIN app ap ON ap.autotruck_id = a.id
				WHERE ap.client = c.id)
		  )
	else 0
	END
)



SELECT * FROM `client` 
	WHERE (`id` IN (
			SELECT `id` FROM `client` 
				WHERE (
					SELECT COUNT(a.id) FROM autotruck a 
                    INNER JOIN customer_payment cp ON a.id = cp.autotruck_id
                    INNER JOIN payment_state ps  ON ps.id = cp.payment_state_id
                    WHERE cp.client_id = c.id AND ps.end_state = 1) != 
				(SELECT COUNT(a.id) FROM autotruck a
                INNER JOIN app ap ON ap.autotruck_id = a.id
                WHERE ap.client = c.id))) AND ((`manager`='9') AND (`client_category_id`='1'))
          
          