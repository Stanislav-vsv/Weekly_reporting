<?php require 'connection.php'?>
<?php require 'libs.php'?>
<?php session_start()?>
<?php 
// обработка формы добавления сотрудника          
if($_POST['employee_add']){
    $FIO = $_POST['FIO'];
    if(!empty($FIO)){                    
        AddAuthors($FIO);
        $_SESSION['res'] = "<p style='color: red;'>Добавлен сотрудник: $FIO</p>";
        header("Location: index.php");
    	exit;
    }
    else{
        $_SESSION['res'] = "<p style='color: red;'>Вы не указали автора</p>";
        header("Location: index.php");
    	exit;
    }
}

//обработка клика по автору при просмотре его последних отчетов
if($_GET['author_id']){                
        $AUTHOR_ID = (int)$_GET['author_id'];
        $WEEK_ID = (int)$_GET['week_id']; 
        $_SESSION['author_week'] = LastReportsOneAuthor($AUTHOR_ID, $WEEK_ID);
        header("Location: index.php");
        exit;
}   

// обработка формы показа активностей по системе и неделе
if($_POST['show_act_sys'] and !empty($_POST['SYSTEM']) and !empty($_POST['WEEK'])){
        $_SESSION['SYSTEM_ID_m'] = $_POST['SYSTEM'];
        $_SESSION['WEEK_ID'] = $_POST['WEEK'];  
        header("Location: index.php#table");
        exit;          
} 
if($_POST['show_act_sys'] and ((empty($_POST['SYSTEM']) and empty($_POST['WEEK']))
         or(empty($_POST['SYSTEM']) and !empty($_POST['WEEK']))
         or(!empty($_POST['SYSTEM']) and empty($_POST['WEEK'])))
){
  $_SESSION['messeg_1'] = "</br></br><span style='color:red;'>Пожалуйста выбирете Систему и Неделю</span>";
  header("Location: index.php");
  exit;   
}

// обработка формы показа активностей по автору и неделе
if($_POST['show_act_ath'] and !empty($_POST['AUTHOR_ID']) and !empty($_POST['WEEK'])){
                $_SESSION['$AUTHOR_ID'] = $_POST['AUTHOR_ID'];
                $_SESSION['$WEEK_ID'] = $_POST['WEEK'];
                header("Location: index.php");
                exit; 
}
if($_POST['show_act_ath'] and ((empty($_POST['AUTHOR_ID']) and empty($_POST['WEEK']))
          or(empty($_POST['AUTHOR_ID']) and !empty($_POST['WEEK']))
          or(!empty($_POST['AUTHOR_ID']) and empty($_POST['WEEK'])))
){
  $_SESSION['messeg_2'] = "</br></br><span style='color:red;'>Пожалуйста выбирете Автора и Неделю</span>"; 
  header("Location: index.php");
  exit;
} 
                  
// авто insert недель
AutoAddWeeks();                             
   
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>   
        <meta http-equiv="Content-Type" content="text/html; charset=windows-1251" />  
        <title>Еженедельные отчеты</title>
        <link href="style.css" type="text/css" rel="stylesheet"/>
</head>
<body>

    <div id="wrapper">    
    
        <h2>Еженедельная отчетность ОСАС</h2>  
         <!-- Форма вывода активностей по системе и неделе -->
         
         <div id="show_act"> 
          <form method="POST">
        
           <h3>Просмор активностей:</h3>             
           <p>Cистема(мы): &nbsp; <label>           
              <?php
                $result = AllSystems();
                echo "<select multiple name='SYSTEM[]' >";
    			foreach($result as $item)   
    			  { 	                    
    				    $SYSTEM_ID = $item[0];
                    	$SYSTEM_NAME = $item[1];
                        echo "<option value='$SYSTEM_ID'>$SYSTEM_NAME</option>";												
    			  } 
    			echo "</select>"; 
              ?>               
              &nbsp; Неделя: &nbsp;              
              <?php 
                $result = ActivWeeks();
                echo "<select name='WEEK' size='4'>";
    			foreach($result as $item)   
    			  { 	                    
    				    $WEEK_ID = $item[0];
                    	$WEEK_DESCR = $item[1];
                        echo "<option value='$WEEK_ID'>$WEEK_DESCR</option>";												
    			  } 
    			echo "</select>"; 
              ?>           
            <input class="submit" type="submit" name="show_act_sys" value="Показать" />
            <?php
                echo $_SESSION['messeg_1'];              
             ?>
            </label>
            </p>                
          </form>           
          
           <!-- Форма вывода активностей по автору и неделе -->
           
           <form method="POST">
             
            <p>Сотрудник: &nbsp; <label>
               <?php 
                $result = AllAuthors();	
                echo "<select name='AUTHOR_ID' size='4'>";
    			foreach($result as $item)   
    			  { 	                    
    				    $AUTHOR_ID = $item[0];
                    	$AUTHOR_NAME = $item[1];
                        echo "<option value='$AUTHOR_ID' >$AUTHOR_NAME</option>";												
    			  } 
    			echo "</select>";
		    	?>
            &nbsp; Неделя: &nbsp;
                <?php 
                $result = ActivWeeks();
                echo "<select name='WEEK' size='4'>";
    			foreach($result as $item)   
    			  { 	                    
    				    $WEEK_ID = $item[0];
                    	$WEEK_DESCR = $item[1];
                        echo "<option value='$WEEK_ID'>$WEEK_DESCR</option>";												
    			  } 
    			echo "</select>";
    			?>
             &nbsp;
             <input class="submit" type="submit" name="show_act_ath" value="Показать" />
             <?php
                echo $_SESSION['messeg_2'];          
             ?>
             </label>
             </p>
          </form>
          </div>              
        
         <!-- Форма выбора сотрудника для редактирования отчета  -->                     
         
         <div id="form_report_edit"> 
          <form method="POST" action="report_edit.php">          
               
         <h3>Редактирование отчетов:</h3>
         <p>Сотрудник: &nbsp; <label>
         <?php  
            $result = AllAuthors();	
            echo "<select name='AUTHOR_ID' size='4' onChange='submit();'>";
			foreach($result as $item)   
			  { 	                    
				    $AUTHOR_ID = $item[0];
                	$AUTHOR_NAME = $item[1];
                    echo "<option value='$AUTHOR_ID' >$AUTHOR_NAME</option>";												
			  } 
			echo "</select>"; 
         ?> 
         </label></p>
         </form>
         </div>         
         
        
         <!-- Форма добавления нового сотрудника -->
         
         <div id="form_employee_add"> 
         <form method="POST">
         
             <h3>Добавить нового сотрудника:</h3>
             <p><label><input type="text" name="FIO" />                          
             <input class="submit" type="submit" name="employee_add" value="Добавить" /> </label></p>
             <p style="color: gray;">Пример: Иванов И.И.</p>
         
         </form>
         <?php
         if(isset($_SESSION['res'])){
            echo $_SESSION['res'];
         }
         
         ?>
         </div>
                 
        <div class="clear"></div>
          
        <!-- Табличка просмотра отчетов по системе и неделе  -->
            <a name='table'></a>
            <br/>
            <div class='table_show_sys'>
            <?php
            if($_SESSION['SYSTEM_ID_m'] and $_SESSION['WEEK_ID']){
            $SYSTEM_ID = implode(',',$_SESSION['SYSTEM_ID_m']);
            $WEEK_ID = $_SESSION['WEEK_ID'];
            //Заголовок с системой и неделей
                $result = SysForHeder($SYSTEM_ID);
                if(count($_SESSION['SYSTEM_ID_m']) > 1){
                    echo "<p>Активности по системам ";                                        
                    $massiv = implode(', ',$result);                      
                    echo "<span style='color: green;'>$massiv</span>"; 
                }
                else{
                    echo "<p> Активности по системе <span style='color: green;'>$result[0]</span>"; 
                }
                echo " за неделю ";
                $result = WeekDescrForHeder($WEEK_ID);
                echo "<span style='color: green;'>$result</span>";
                echo ": ";
                // Сама табличка
                $result = ReportSysWeek($SYSTEM_ID, $WEEK_ID);
                if(!empty($result)){
                   echo "</p>"; 
                   echo "<table>";
			       echo "<tr>
                        <th>Система</th>
						<th class='tema'>Тема</th> 
						<th>Неделя</th> 
						<th>Активность</th>
                        <th class='auth'>Автор</th> 					
					  </tr>";
                    foreach ($result as $item){
                        echo "<tr>";
                        foreach ($item as $row) echo "<td>$row</td>";                    
                        echo "</tr>"; 
                    }  
                	echo "</table>"; 
                } else {
                    echo '<span style="color:red;"> За данный период отчетов нет.</span></p>';
                }                        
              } 
             ?> 
             </div>
          
         <!-- Табличка просмотра отчетов по автору и неделе  -->
            <div class='table_show_sys'>
            <?php
             if($_SESSION['$AUTHOR_ID'] and $_SESSION['$WEEK_ID']){
                $AUTHOR_ID = $_SESSION['$AUTHOR_ID'];
                $WEEK_ID = $_SESSION['$WEEK_ID'];
                $result = AthWeekForHeder($AUTHOR_ID, $WEEK_ID);
                echo "<p> Активности сотрудника <span style='color: green;'>$result[0]</span> 
                               за неделю <span style='color: green;'>$result[1]</span>: ";               
                // Сама табличка
                $result = ReportAthWeek($AUTHOR_ID, $WEEK_ID);
                if(!empty($result)){
                   echo "</p>"; 
                   echo "<table>";
			       echo "<tr>
						<th class='tema'>Тема</th> 
                        <th>Система</th>
						<th>Неделя</th> 
						<th>Активность</th>					
					  </tr>";
                    foreach ($result as $item){
                        echo "<tr>";
                        foreach ($item as $row) echo "<td>$row</td>";                    
                        echo "</tr>"; 
                    }  
                	echo "</table>"; 
                } else {
                    echo "<span style='color:red;'> За данный период у сотрудника отчетов нет.</span></p>";
                }                       
              } 
            ?> 
            </div> 
        
        
         <!--  Табличка для последних отчетов всех сотрудников  -->
          
           <div class="last_reports">
           <h3>Последние отчеты сотрудников:</h3>
           <?php 
           $result = LastReports();
           if(!empty($result)){
           echo "<table>";
           echo "<tr>                        
    				<th>Сотрудник</th> 
    				<th>Неделя</th> 		
   			    </tr>";
                
           foreach ($result as $item){
                        echo "<tr>";                         
                        echo "<td><a href='?author_id=$item[0]&week_id=$item[2]'>$item[1]</a></td>";   
                        echo "<td>$item[3]</td>";               
                        echo "</tr>"; 
                    }  
                	echo "</table>"; 
           }else{
                echo "<p>Пока ничего нет</p>";
           }           
           ?>           
           </div>
         
         
         <!--  Табличка с последними отчетами выбранного сотрудника  -->
           
           <div class="report_author"> 
           <?php
            if(isset($_SESSION['author_week'])){ 
                $result = $_SESSION['author_week'];                
                echo '<h4>Отчет сотрудника <span style="color: green;">'.$result[0][0].'</span> за неделю <span style="color: green;">'.$result[0][2].'</span> </h4>'; 
                echo "<table>";
    				echo "<tr>  
                            <th>Тема</th> 
                            <th>Система</th>
                            <th>Активность</th>	
    					  </tr>";
                foreach($result as $item){
                    echo '<tr>';
                    echo "<td>$item[3]</td>";
                    echo "<td>$item[1]</td>";
                    echo "<td>$item[4]</td>";
                    echo '</tr>';
                }                      
                echo "</table>";
                }
           ?> 
           </div>
           
           <div class="clear"></div>
    <?php  
       ora_logoff($connection);
       session_unset();
       session_destroy();
     ?>     
    
    </div>
     
</body>
</html>    