<?php

use yii\db\Migration;

class m180730_172137_db_programming extends Migration
{
    public function safeUp()
    {   
        $this->drop_all();
        $this->create_get_client_sverka();
        $this->create_get_manager_sverka();
        $this->create_get_user_role();
        $this->create_get_user_sverka();

        $this->create_function_client_sverka();

        $this->create_view_clietn_list();
        $this->create_get_client_list();



        $this->create_execute_all_users_sverka();
    }

    public function safeDown()
    {
        $this->drop_all();
    }


    public function drop_all(){
        $this->execute("Drop procedure if exists `get_client_sverka`");

        $this->execute("Drop procedure if exists `get_manager_sverka`");
        
        $this->execute("Drop function if exists `get_user_role`");
        
        $this->execute("Drop procedure if exists `get_user_sverka`");
        
        $this->execute("Drop view if exists `client_list`");

        $this->execute("Drop procedure if exists `get_client_list`");

        $this->execute("Drop function if exists `f_get_client_sverka`");


        $this->execute("Drop function if exists `execute_all_users_sverka`");

        
    }



    public function create_get_client_sverka(){

        $sql = <<<SQL

        Create procedure  `get_client_sverka`(in p_user_id int, in p_enddate datetime)
        begin
    
        
        SELECT  ROUND(SUM( sum ),2) as sum, 
                ROUND(SUM( sum_cash ),2) as sum_cash, 
                ROUND(SUM( sum_card ),2) as sum_card
                FROM (
                    SELECT SUM( ex.cost ) AS sum, 0 as sum_cash, 0 as sum_card
                    FROM expenses_manager ex
                    WHERE  `manager_id` = p_user_id AND  ex.`date` <=  p_enddate AND ex.isDeleted=0
                    
                    UNION ALL 
                    SELECT SUM( if(1,0 - pe.sum,pe.sum) ) AS sum, SUM(if(1,0-pe.sum_cash,pe.sum_cash)) as sum_cash, SUM(if(1,0-pe.sum_card,pe.sum_card)) as sum_card
                    FROM payments_expenses pe
                    WHERE  `manager_id` = p_user_id AND  pe.`date` <= p_enddate AND pe.isDeleted=0
                    
                    UNION ALL
                    SELECT  ROUND(SUM(a.`summa_us`),2) as 'sum',
                            SUM(a.`summa_us` * at.`course`) as 'sum_cash', 
                            SUM((c.payment_clearing/100 * a.`summa_us` * at.`course`) + (a.`summa_us` * at.`course`)) as sum_card
                    FROM autotruck at
                    INNER JOIN app a ON a.autotruck_id = at.id
                    INNER JOIN client c ON c.id = a.client
                    INNER JOIN app_status a_s ON a_s.id = at.status
                    INNER JOIN app_trace apt ON apt.autotruck_id = at.id
                    WHERE  c.`user_id` = p_user_id AND a_s.send_check = 1 AND apt.status_id = at.status AND a.isDeleted=0 AND at.isDeleted=0 AND apt.`trace_date` <= p_enddate
                ) AS v;
            end
SQL;
        $this->execute($sql);

    }



    public function create_get_manager_sverka(){

        $sql = <<<SQL
        Create procedure `get_manager_sverka`(in p_user_id int, in p_enddate datetime)
        begin
    
        
        SELECT  ROUND(SUM( sum ),2) as sum, 
                ROUND(SUM( sum_cash ),2) as sum_cash, 
                ROUND(SUM( sum_card ),2) as sum_card
                FROM (
                    SELECT SUM( ex.cost ) AS sum, 0 as sum_cash, 0 as sum_card
                    FROM expenses_manager ex
                    WHERE  `manager_id` = p_user_id AND  ex.`date` <=  p_enddate AND ex.isDeleted=0
                    
                    UNION ALL 
                    SELECT SUM( if(1,0 - pe.sum,pe.sum) ) AS sum, SUM(if(1,0-pe.sum_cash,pe.sum_cash)) as sum_cash, SUM(if(1,0-pe.sum_card,pe.sum_card)) as sum_card
                    FROM payments_expenses pe
                    WHERE  `manager_id` = p_user_id AND  pe.`date` <= p_enddate AND pe.isDeleted=0
                    
                ) AS v;
        end
SQL;
        $this->execute($sql);

    }





    public function create_get_user_role(){

        $sql = <<<SQL
        Create function `get_user_role`( p_user_id int)
        returns text deterministic
        begin
            
            declare role_name text default '';
            declare role_type int default 1;
            
                SELECT b.name into role_name 
                FROM auth_assignment as a, auth_item as b
                WHERE a.item_name = b.name and a.user_id = p_user_id and b.type = role_type LIMIT 1;
            
            return role_name;    
        end
SQL;
        $this->execute($sql);

    }



    public function create_get_user_sverka(){

        $sql = <<<SQL
        
        Create procedure `get_user_sverka`(in p_user_id int, in p_enddate datetime)
        begin
    
            declare user_role text;
            
            set user_role = get_user_role(p_user_id);
            if(user_role = "client" OR user_role = "clientExtended")
            then call get_client_sverka(p_user_id,p_enddate);
            else call get_manager_sverka(p_user_id,p_enddate);
            end if;
                
                
        end
SQL;
        $this->execute($sql);

    }





    public function create_view_clietn_list(){
        $sql = <<<SQL
        DROP VIEW if exists `client_list`;

CREATE VIEW `client_list` AS 
        SELECT  cl.`id`,
                cl.`name`,
                cl.`full_name`,
                cl.`description`,
                cl.`phone`,
                cl.`user_id`,
                cl.`client_category_id`,
                cl.`manager`,
                cl.`contract_number`,
                cl.`payment_clearing`,
                cl.`organisation_pay_id`,
                cl.`email`,
                cl.`isDeleted`,
                u.`username` as 'manager_username',
                u.`phone` as 'manager_phone',
                u.`name` as 'manager_name',
                u.`email` as 'manager_email',
                u2.`username` as 'user_username',
                u2.`phone` as 'user_phone',
                u2.`name` as 'user_name',
                u2.`email` as 'user_email',
                clc.`cc_title` as 'category_title',
                clc.`cc_description` as 'category_description',
                us.`sum` as 'sverka_sum',
                us.`sum_card` as 'sverka_sum_card',
                us.`sum_cash` as 'sverka_sum_cash'
        FROM client as cl
        LEFT JOIN user as u ON u.id = cl.manager
        LEFT JOIN user as u2 ON u2.id = cl.user_id
        LEFT JOIN user_sverka as us ON us.user_id = cl.user_id
        LEFT JOIN client_category as clc ON clc.cc_id = cl.client_category_id;
SQL;
    
        $this->execute($sql);
    }



    public function create_get_client_list(){
        $sql = <<<SQL
        Drop procedure if exists `get_client_list`;

        Create procedure `get_client_list`(in p_condition text,in p_limit int, in p_page int)
        begin
                
                declare v_condition text default "";
                declare v_limit int default 50;
                declare v_page int default 1;
                declare v_start int;
                
                
                if(p_condition != "") then set v_condition = p_condition; end if;
                
                if(p_limit > 0) then set v_limit = p_limit; end if;
                
                if(p_page > 0) then set v_page = p_page; end if;
                
                set v_start = v_page * v_limit - v_limit;
                
                if(v_condition != "") then
                    
                    SET @query = concat("SELECT * FROM client_list WHERE ", v_condition ," ORDER BY name ASC LIMIT ", v_start, v_limit);
                    
                    PREPARE st FROM @query;
                    
                    EXECUTE st;
                    
                else
                    SELECT * FROM client_list  ORDER BY name ASC LIMIT v_start, v_limit;
                end if;
                
                
                
        end
SQL;
    
        $this->execute($sql);
    }




    public function create_function_client_sverka(){
        $sql = <<<SQL
            Drop function if exists `f_get_client_sverka`;

            Create function `f_get_client_sverka`( p_user_id int, endDate datetime,p_out_sum tinyint,p_out_sum_cash tinyint,p_out_sum_card tinyint)
            returns double deterministic
            begin
                
                declare v_endDate text;
                
                declare v_sum double default 0;
                declare v_sum_cash double default 0;
                declare v_sum_card double default 0;
                declare return_sum double default 0;
                
                if(endDate != "") then set v_endDate = curdate();
                else set v_endDate = endDate();
                end if;
                
                SELECT  ROUND(SUM( sum ),2) as sum, 
                            ROUND(SUM( sum_cash ),2) as sum_cash, 
                            ROUND(SUM( sum_card ),2) as sum_card
                            INTO v_sum,v_sum_cash,v_sum_card
                            FROM (
                                SELECT SUM( ex.cost ) AS sum, 0 as sum_cash, 0 as sum_card
                                FROM expenses_manager ex
                                WHERE  `manager_id` = p_user_id AND  ex.`date` <=  v_endDate AND ex.isDeleted=0
                                
                                UNION ALL 
                                SELECT SUM( if(1,0 - pe.sum,pe.sum) ) AS sum, SUM(if(1,0-pe.sum_cash,pe.sum_cash)) as sum_cash, SUM(if(1,0-pe.sum_card,pe.sum_card)) as sum_card
                                FROM payments_expenses pe
                                WHERE  `manager_id` = p_user_id AND  pe.`date` <= v_endDate AND pe.isDeleted=0
                                
                                UNION ALL
                                SELECT  ROUND(SUM(a.`summa_us`),2) as 'sum',
                                        SUM(a.`summa_us` * at.`course`) as 'sum_cash', 
                                        SUM((c.payment_clearing/100 * a.`summa_us` * at.`course`) + (a.`summa_us` * at.`course`)) as sum_card
                                FROM autotruck at
                                INNER JOIN app a ON a.autotruck_id = at.id
                                INNER JOIN client c ON c.id = a.client
                                INNER JOIN app_status a_s ON a_s.id = at.status
                                INNER JOIN app_trace apt ON apt.autotruck_id = at.id
                                WHERE  c.`user_id` = p_user_id AND a_s.send_check = 1 AND apt.status_id = at.status AND a.isDeleted = 0 AND at.isDeleted = 0 AND apt.`trace_date` <= v_endDate
                            ) AS v;
                
                if(p_out_sum) then set return_sum = v_sum;
                elseif(p_out_sum_cash) then set return_sum =  v_sum_cash;
                elseif(p_out_sum_card) then set return_sum =  v_sum_card;
                end if;
                
                return return_sum;
            end
SQL;

        $this->execute($sql);
    }


    public function create_execute_all_users_sverka(){
        $sql = <<<SQL
        Create function `execute_all_users_sverka`()
        returns int deterministic
        begin
            
            declare role_name text default '';
            declare role_type int default 1;
            
            return role_type;    
        end
SQL;
        $this->execute($sql);
    }
}
