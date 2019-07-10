<?php
      
// вывод списка авторов для селекта

function AllAuthors(){
    global $open_connection;
    $query = " select AUTHOR_ID, AUTHOR_NAME from MONITORING.REPORT_AUTHORS where author_id not in (1,4,2) " ;
    ora_parse($open_connection, $query, 0);         
	ora_exec($open_connection);
    while(ora_fetch_into($open_connection, $row))
    {
        $data[] = $row;
    }  
  return $data;      
} 


// добавление нового сотрудника

function AddAuthors($FIO){
    global $open_connection;
    $query = " insert into MONITORING.REPORT_AUTHORS (AUTHOR_NAME) values ('$FIO')" ; 
    ora_parse($open_connection, $query, 0);         
	ora_exec($open_connection);
} 


// вывод списка систем для селекта

function AllSystems(){
    global $open_connection;
    $query = " select SYSTEM_ID, SYSTEM_NAME from MONITORING.REPORT_SYSTEM " ;
    ora_parse($open_connection, $query, 0);         
	ora_exec($open_connection);
    while(ora_fetch_into($open_connection, $row))
    {
        $data[] = $row;
    } 
  return $data; 
}  


// вывод списка недель для селекта

function AllWeeks(){
    global $open_connection;
    $query = " select WEEK_ID, to_char(WEEK_DESCR, 'dd.mm.yyyy') as WEEK_DESCR
                 from REPORT_WEEKS
                where WEEK_ID between (select max(WEEK_ID) from REPORT_WEEKS)-7
                and (select max(WEEK_ID) from REPORT_WEEKS)
                order by WEEK_ID desc " ;
    ora_parse($open_connection, $query, 0);         
	ora_exec($open_connection);
    while(ora_fetch_into($open_connection, $row))
    {
        $data[] = $row;
    }  
  return $data; 
} 


// вывод списка недель по которым есть отчеты, для селекта 

function ActivWeeks(){
    global $open_connection;
    $query = "SELECT week_id, TO_CHAR(week_descr, 'dd.mm.yyyy') AS week_descr
              FROM report_weeks
              WHERE week_id IN (SELECT DISTINCT(week_id) FROM report_actions)
              ORDER BY week_id DESC";
    ora_parse($open_connection, $query, 0);
    ora_exec($open_connection);
    while(ora_fetch_into($open_connection, $row))
    {
        $data[] = $row;
    }
  return $data;  
} 


// вывод списка систем для заголовка таблицы отчетов

function SysForHeder($SYSTEM_ID){
    global $open_connection;
    $query = " select SYSTEM_NAME from MONITORING.REPORT_SYSTEM where SYSTEM_ID in ($SYSTEM_ID) " ;    
    ora_parse($open_connection, $query, 0);         
	ora_exec($open_connection);
    while(ora_fetch_into($open_connection, $row))
    {
        $data[] = $row[0];
    }  
  return $data;    
}


// вывод описания недели по ID

function WeekDescrForHeder($WEEK_ID){
    global $open_connection;
    $query = " select to_char(WEEK_DESCR, 'dd.mm.yyyy') as WEEK_DESCR
                 from MONITORING.REPORT_WEEKS where WEEK_ID = $WEEK_ID " ;    
    ora_parse($open_connection, $query, 0);         
	ora_exec($open_connection);
    ora_fetch_into($open_connection, $row);
    $data = $row[0];
    return $data;
}


// табличка результатов отчета по системам и неделе

function ReportSysWeek($SYSTEM_ID, $WEEK_ID){
    global $open_connection;
    $query = " select SYSTEM_NAME, TOPIC_NAME, to_char(WEEK_DESCR, 'dd.mm.yyyy') as WEEK_DESCR, ACTION, AUTHOR_NAME 
                                    from MONITORING.REPORT_TOPICS t
                                    join
                                    MONITORING.REPORT_ACTIONS a
                                    on  t.TOPIC_ID =  a.TOPIC_ID
                                    join
                                    MONITORING.REPORT_WEEKS w
                                    on a.WEEK_ID = w.WEEK_ID 
                                    join
                                    MONITORING.REPORT_AUTHORS ath
                                    on t.AUTHOR_ID = ath.AUTHOR_ID
                                    join
                                    MONITORING.REPORT_SYSTEM s
                                    on t.SYSTEM_ID = s.SYSTEM_ID
                                    where t.SYSTEM_ID in ($SYSTEM_ID) 
                                    and A.WEEK_ID = $WEEK_ID
                                    order by SYSTEM_NAME desc " ;    
    ora_parse($open_connection, $query, 0);         
	ora_exec($open_connection);
    while(@ora_fetch_into($open_connection, $row))
    {
        $data[] = $row;
    }  
  return $data; 
}


// вывод фио автора и описания недели для заголовка таблицы отчетов

function AthWeekForHeder($AUTHOR_ID, $WEEK_ID){
    global $open_connection;
    $query = " select AUTHOR_NAME, to_char(WEEK_DESCR, 'dd.mm.yyyy') as WEEK_DESCR from 
                             MONITORING.REPORT_AUTHORS, MONITORING.REPORT_WEEKS
                             where AUTHOR_ID = $AUTHOR_ID
                             and WEEK_ID = $WEEK_ID  " ;    
    ora_parse($open_connection, $query, 0);         
	ora_exec($open_connection);
    ora_fetch_into($open_connection, $row);
    $data[0] = $row[0];
    $data[1] = $row[1];
    return $data;    
}


// табличка результатов отчета по автору и неделе

function ReportAthWeek($AUTHOR_ID, $WEEK_ID){
    global $open_connection;
    $query = "select  TOPIC_NAME,  SYSTEM_NAME, to_char(WEEK_DESCR, 'dd.mm.yyyy') as WEEK_DESCR, ACTION 
                from MONITORING.REPORT_ACTIONS a
                join
                MONITORING.REPORT_TOPICS t 
                on a.TOPIC_ID = t.TOPIC_ID
                join
                MONITORING.REPORT_SYSTEM s
                on t.SYSTEM_ID = s.SYSTEM_ID
                join
                MONITORING.REPORT_WEEKS w
                on a.WEEK_ID = w.WEEK_ID
                where a.AUTHOR_ID = $AUTHOR_ID 
                and a.WEEK_ID = $WEEK_ID " ; 
    ora_parse($open_connection, $query, 0);         
	ora_exec($open_connection);
    while(@ora_fetch_into($open_connection, $row))
    {
        $data[] = $row;
    }
  return $data; 
}  


// табличка просмотра последних отчетов всех авторов

function LastReports(){
    global $open_connection;
    $query = " select ath.AUTHOR_ID, ath.AUTHOR_NAME, WEEK_ID, to_char(WEEK_DESCR, 'dd.mm.yyyy') as WEEK_DESCR from 
                            MONITORING.REPORT_AUTHORS ath
                            join
                                (select AUTHOR_ID, max(WEEK_ID) WEEK_ID_M
                                from MONITORING.REPORT_ACTIONS
                                where author_id not in (1,4,2,9)
                                group by AUTHOR_ID) a
                            on  a.AUTHOR_ID = ath.AUTHOR_ID
                            join
                            MONITORING.REPORT_WEEKS w
                            on WEEK_ID_M = w.WEEK_ID " ; 
    ora_parse($open_connection, $query, 0);         
	ora_exec($open_connection);
    while(@ora_fetch_into($open_connection, $row))
    {
        $data[] = $row;
    }
  return $data; 
}


// Табличка с последними отчетами выбранного сотрудника

function LastReportsOneAuthor($AUTHOR_ID, $WEEK_ID){
    global $open_connection;
    $query = " select AUTHOR_NAME, SYSTEM_NAME, to_char(WEEK_DESCR, 'dd.mm.yyyy') as WEEK_DESCR, TOPIC_NAME, ACTION
                from MONITORING.REPORT_ACTIONS a
                join 
                MONITORING.REPORT_AUTHORS ath
                on A.AUTHOR_ID = ATH.AUTHOR_ID and ATH.AUTHOR_ID = $AUTHOR_ID
                join 
                MONITORING.REPORT_WEEKS w
                on A.WEEK_ID = W.WEEK_ID and  W.WEEK_ID = $WEEK_ID                            
                join
                MONITORING.REPORT_TOPICS t 
                on a.TOPIC_ID = t.TOPIC_ID
                join
                MONITORING.REPORT_SYSTEM s
                on t.SYSTEM_ID = s.SYSTEM_ID " ; 
                
    ora_parse($open_connection, $query, 0);         
	ora_exec($open_connection);
    while(@ora_fetch_into($open_connection, $row))
    {
        $data[] = $row;
    }
  return $data; 
}

                                          // СТРАНИЦА ЛИЧНЫХ НАСТРОЕК АВТОРА

// Вывод фамилии автора для заголовка

function FamilyAuthor($AUTHOR_ID){
    global $open_connection;
    $query = " select AUTHOR_NAME from MONITORING.REPORT_AUTHORS
                                               where AUTHOR_ID = $AUTHOR_ID " ; 
    ora_parse($open_connection, $query, 0);         
	ora_exec($open_connection);
    while(@ora_fetch_into($open_connection, $row))
    {
        $data = $row[0];
    }
  return $data; 
}

// добавление связи автора
function SystemLinkAuthor($SYSTEM_ID, $AUTHOR_ID){
    global $open_connection;
    $query = " select SYSTEM_ID, AUTHOR_ID from MONITORING.REPORT_AUTHOR2SYSTEM " ; 
    ora_parse($open_connection, $query, 0);         
	ora_exec($open_connection);
    $flag = 0;
    while(ora_fetch_into($open_connection, $row)){        
        if(($row[0]==$SYSTEM_ID)&&($row[1]==$AUTHOR_ID))
            $flag = $SYSTEM_ID;
    }
    if($flag<>0){
        return $flag;       
    }else{
        $query = " insert into MONITORING.REPORT_AUTHOR2SYSTEM (SYSTEM_ID, AUTHOR_ID)
                      values( $SYSTEM_ID, $AUTHOR_ID )" ; 
        ora_parse($open_connection, $query, 0);         
    	ora_exec($open_connection);
        return $flag;
    }
}

// вывод списка систем для отдельного автора

function SystemDescrForAuthor($AUTHOR_ID){
    global $open_connection;
    $query = " select s.SYSTEM_ID, SYSTEM_NAME
                            from MONITORING.REPORT_SYSTEM s
                            join
                            MONITORING.REPORT_AUTHOR2SYSTEM ath
                            on s.SYSTEM_ID = ath.SYSTEM_ID
                            where ATH.AUTHOR_ID = $AUTHOR_ID " ;    
    ora_parse($open_connection, $query, 0);         
	ora_exec($open_connection);
    while(ora_fetch_into($open_connection, $row)){
        $data[] = $row;
    }    
    return $data;
}

// добавление новой системы

function NewSystemAdd($SYSTEM){
    global $open_connection;
    $query = " select case when SYSTEM_NAME like ('%$SYSTEM%') then 1 else 0 end FLAG
                                                from MONITORING.REPORT_SYSTEM " ;    
    ora_parse($open_connection, $query, 0);         
	ora_exec($open_connection);
    $flag = 0;
    while(ora_fetch_into($open_connection, $row)){
        if($row[0]==1)$flag = 1;        
    }    
    if($flag<>1){
        global $open_connection;
        $query = " insert into MONITORING.REPORT_SYSTEM (SYSTEM_NAME) values('$SYSTEM') " ;    
        ora_parse($open_connection, $query, 0);         
    	ora_exec($open_connection); 
        return $flag;
    } else {return $flag;}
}

// добавление новой недели

function NewWeekAdd($NEW_WEEK){
    global $open_connection;
    $query = " select case when WEEK_DESCR like to_date('$NEW_WEEK', 'dd.mm.yyyy') then 1 else 0 end FLAG
                                                from MONITORING.REPORT_WEEKS " ;    
    ora_parse($open_connection, $query, 0);         
	ora_exec($open_connection);
    $flag = 0;
    while(ora_fetch_into($open_connection, $row)){
        if($row[0]==1)$flag = 1;        
    }    
    if($flag<>1){
        global $open_connection;
        $query = " insert into MONITORING.REPORT_WEEKS (WEEK_DESCR) values(to_date('$NEW_WEEK', 'dd.mm.yyyy'))" ;    
        ora_parse($open_connection, $query, 0);         
    	ora_exec($open_connection); 
        return $flag;
    } else {return $flag;}
}

// добавление новой темы отчетов

function AddTopic($SYSTEM_ID, $AUTHOR_ID, $TOPIC){
    global $open_connection;
    $query = " insert into MONITORING.REPORT_TOPICS ( SYSTEM_ID, AUTHOR_ID, TOPIC_NAME)
                                              values($SYSTEM_ID, $AUTHOR_ID, '$TOPIC')" ; 
    ora_parse($open_connection, $query, 0);         
	ora_exec($open_connection);
} 

// список тем сотрудника

function TopicAuthor($AUTHOR_ID){
    global $open_connection;
    $query = " select TOPIC_ID, TOPIC_NAME, SYSTEM_NAME
                from MONITORING.REPORT_TOPICS t 
                join
                MONITORING.REPORT_SYSTEM s
                on t.SYSTEM_ID = s.SYSTEM_ID
                where AUTHOR_ID = $AUTHOR_ID
                order by TOPIC_ID desc " ;    
    ora_parse($open_connection, $query, 0);         
	ora_exec($open_connection);
    while(ora_fetch_into($open_connection, $row)){
        $data[] = $row;
    }    
    return $data;
}

                                    // СТРАНИЦА РЕДАКТИРОВАНИЯ и ДОБАВЛЕНИЯ ОТЧЕТОВ

// талица с последними отчетами

function LastActionAuthor($AUTHOR_ID){
    global $open_connection;
    $query = " select t.TOPIC_ID, TOPIC_NAME, SYSTEM_NAME, to_char(WEEK_DESCR, 'dd.mm.yyyy') as WEEK_DESCR, ACTION 
                from MONITORING.REPORT_ACTIONS a
                 join
                MONITORING.REPORT_TOPICS t 
                on a.TOPIC_ID = t.TOPIC_ID
                 join
                MONITORING.REPORT_SYSTEM s
                on t.SYSTEM_ID = s.SYSTEM_ID
                 join
                MONITORING.REPORT_WEEKS w
                on a.WEEK_ID = w.WEEK_ID
                where a.AUTHOR_ID = $AUTHOR_ID 
                and a.WEEK_ID = (select max(WEEK_ID) from MONITORING.REPORT_ACTIONS where AUTHOR_ID = $AUTHOR_ID) " ;    
    ora_parse($open_connection, $query, 0);         
	ora_exec($open_connection);
    while(ora_fetch_into($open_connection, $row)){
        $data[] = $row;
    }
    return $data;    
}

// Вывод названия темы для заголовка

function TopicDesc($TOPIC_ID){
    global $open_connection;
    $query = " select TOPIC_NAME from MONITORING.REPORT_TOPICS where TOPIC_ID = $TOPIC_ID " ; 
    ora_parse($open_connection, $query, 0);         
	ora_exec($open_connection);
    while(@ora_fetch_into($open_connection, $row))
    {
        $data = $row[0];
    }
  return $data; 
}


// Табличка активностей других авторов по системе, которая относится к теме, по которой добавляется активность 

function ActionOtherAuthor($AUTHOR_ID, $WEEK_ID, $TOPIC_ID){
    global $open_connection;
    $query = " select ath.AUTHOR_NAME, to_char(w.WEEK_DESCR, 'dd.mm.yyyy') as WEEK_DESCR, TOP.TOPIC_NAME, act.ACTION
                from MONITORING.REPORT_ACTIONS act
                 join  MONITORING.REPORT_AUTHORS ath
                on act.AUTHOR_ID = ath.AUTHOR_ID 
                 join MONITORING.REPORT_WEEKS w
                on act.WEEK_ID = w.WEEK_ID
                join MONITORING.REPORT_TOPICS top
                on top.TOPIC_ID = ACT.TOPIC_ID 
                where act.TOPIC_ID in (
                    select  TOPIC_ID from MONITORING.REPORT_TOPICS
                    where SYSTEM_ID in (
                         select SYSTEM_ID from MONITORING.REPORT_TOPICS where TOPIC_ID = $TOPIC_ID
                     ))
                and act.WEEK_ID = $WEEK_ID
                and  act.AUTHOR_ID not in ($AUTHOR_ID) " ; 
    ora_parse($open_connection, $query, 0);         
	ora_exec($open_connection);
    while(@ora_fetch_into($open_connection, $row))
    {
        $data[] = $row;
    }
  return $data; 
}


// Вывод названия системы для заголовка, по ID названия темы

function SystDesc($TOPIC_ID){
    global $open_connection;
    $query = " select SYSTEM_NAME from MONITORING.REPORT_SYSTEM
                where SYSTEM_ID in (select SYSTEM_ID from MONITORING.REPORT_TOPICS where TOPIC_ID = $TOPIC_ID) " ; 
    ora_parse($open_connection, $query, 0);         
	ora_exec($open_connection);
    while(@ora_fetch_into($open_connection, $row))
    {
        $data = $row[0];
    }
  return $data; 
}


// проверка уникальности активности для темы и недели

function CheckUniqueAction($WEEK_ID, $AUTHOR_ID, $TOPIC_ID){
    global $open_connection;
    $query = " select WEEK_ID, AUTHOR_ID, TOPIC_ID from MONITORING.REPORT_ACTIONS " ; 
    ora_parse($open_connection, $query, 0);         
	ora_exec($open_connection);
    $flag = 0;
    while(ora_fetch_into($open_connection, $row)){        
        if(($row[0]==$WEEK_ID)&&($row[1]==$AUTHOR_ID)&&($row[2]==$TOPIC_ID))
            $flag = 1;
    }
    if($flag<>0){
        return $flag;       
    }else{
        return $flag;
    }
}

// добавление новой активности

function AddAction($WEEK_ID, $AUTHOR_ID, $TOPIC_ID, $REPORT){
    global $open_connection;
    $query = " insert into MONITORING.REPORT_ACTIONS (WEEK_ID,  AUTHOR_ID, TOPIC_ID, ACTION)  
                                   values ($WEEK_ID, $AUTHOR_ID, $TOPIC_ID, '$REPORT' ) " ; 
    ora_parse($open_connection, $query, 0);         
	ora_exec($open_connection);
}


// update активности

function ActionEdit($WEEK_DESCR, $SYSTEM_NAME, $AUTHOR_ID, $TOPIC_ID, $ACTION){
    global $open_connection;
    $query = " update MONITORING.REPORT_ACTIONS 
                                set ACTION = '$ACTION'
                                where  WEEK_ID = (select WEEK_ID from MONITORING.REPORT_WEEKS where WEEK_DESCR like  to_date('$WEEK_DESCR', 'dd.mm.yyyy') )
                                and AUTHOR_ID = $AUTHOR_ID
                                and TOPIC_ID = $TOPIC_ID" ; 
    ora_parse($open_connection, $query, 0);         
	ora_exec($open_connection);
}


// авто insert недель

function AutoAddWeeks(){
    global $open_connection;
    $query = " insert into MONITORING.REPORT_WEEKS(WEEK_DESCR)
                SELECT monday 
                FROM
                (select trunc(sysdate, 'IW') - (LEVEL-1)*7 AS monday from dual CONNECT BY trunc(sysdate, 'IW') - (LEVEL-1)*7 > SYSDATE -30) 
                WHERE monday  NOT IN ( SELECT WEEK_DESCR FROM REPORT_WEEKS)
                order by monday " ; 
    $a = ora_parse($open_connection, $query, 0);         
	ora_exec($open_connection);
}


// вывод темы для редактирования 

function TopicForEdit($TOPIC_ID){
    global $open_connection;
    $query = " SELECT topic_id, topic_name, system_name
                FROM monitoring.report_topics t
                JOIN
                monitoring.report_system s
                ON t.system_id = s.system_id
                WHERE topic_id = $TOPIC_ID " ; 
    ora_parse($open_connection, $query, 0);         
	ora_exec($open_connection);
    while(@ora_fetch_into($open_connection, $row))
    {
        $data = $row;
    }
  return $data; 
}


// редактирование названия темы

function TopicEdit($TOPIC_ID, $TOPIC_NAME){
    global $open_connection;
    $TOPIC_NAME = trim($TOPIC_NAME);
    $query = " UPDATE monitoring.report_topics
                SET topic_name = '$TOPIC_NAME'
                WHERE topic_id = $TOPIC_ID " ;
    ora_parse($open_connection, $query, 0);
    ora_exec($open_connection);                
}








  
  
  
  
  
  

?>