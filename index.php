<?php require 'connection.php'?>
<?php require 'libs.php'?>
<?php session_start()?>
<?php 
// ��������� ����� ���������� ����������          
if($_POST['employee_add']){
    $FIO = $_POST['FIO'];
    if(!empty($FIO)){                    
        AddAuthors($FIO);
        $_SESSION['res'] = "<p style='color: red;'>�������� ���������: $FIO</p>";
        header("Location: index.php");
    	exit;
    }
    else{
        $_SESSION['res'] = "<p style='color: red;'>�� �� ������� ������</p>";
        header("Location: index.php");
    	exit;
    }
}

//��������� ����� �� ������ ��� ��������� ��� ��������� �������
if($_GET['author_id']){                
        $AUTHOR_ID = (int)$_GET['author_id'];
        $WEEK_ID = (int)$_GET['week_id']; 
        $_SESSION['author_week'] = LastReportsOneAuthor($AUTHOR_ID, $WEEK_ID);
        header("Location: index.php");
        exit;
}   

// ��������� ����� ������ ����������� �� ������� � ������
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
  $_SESSION['messeg_1'] = "</br></br><span style='color:red;'>���������� �������� ������� � ������</span>";
  header("Location: index.php");
  exit;   
}

// ��������� ����� ������ ����������� �� ������ � ������
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
  $_SESSION['messeg_2'] = "</br></br><span style='color:red;'>���������� �������� ������ � ������</span>"; 
  header("Location: index.php");
  exit;
} 
                  
// ���� insert ������
AutoAddWeeks();                             
   
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>   
        <meta http-equiv="Content-Type" content="text/html; charset=windows-1251" />  
        <title>������������ ������</title>
        <link href="style.css" type="text/css" rel="stylesheet"/>
</head>
<body>

    <div id="wrapper">    
    
        <h2>������������ ���������� ����</h2>  
         <!-- ����� ������ ����������� �� ������� � ������ -->
         
         <div id="show_act"> 
          <form method="POST">
        
           <h3>������� �����������:</h3>             
           <p>C������(��): &nbsp; <label>           
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
              &nbsp; ������: &nbsp;              
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
            <input class="submit" type="submit" name="show_act_sys" value="��������" />
            <?php
                echo $_SESSION['messeg_1'];              
             ?>
            </label>
            </p>                
          </form>           
          
           <!-- ����� ������ ����������� �� ������ � ������ -->
           
           <form method="POST">
             
            <p>���������: &nbsp; <label>
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
            &nbsp; ������: &nbsp;
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
             <input class="submit" type="submit" name="show_act_ath" value="��������" />
             <?php
                echo $_SESSION['messeg_2'];          
             ?>
             </label>
             </p>
          </form>
          </div>              
        
         <!-- ����� ������ ���������� ��� �������������� ������  -->                     
         
         <div id="form_report_edit"> 
          <form method="POST" action="report_edit.php">          
               
         <h3>�������������� �������:</h3>
         <p>���������: &nbsp; <label>
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
         
        
         <!-- ����� ���������� ������ ���������� -->
         
         <div id="form_employee_add"> 
         <form method="POST">
         
             <h3>�������� ������ ����������:</h3>
             <p><label><input type="text" name="FIO" />                          
             <input class="submit" type="submit" name="employee_add" value="��������" /> </label></p>
             <p style="color: gray;">������: ������ �.�.</p>
         
         </form>
         <?php
         if(isset($_SESSION['res'])){
            echo $_SESSION['res'];
         }
         
         ?>
         </div>
                 
        <div class="clear"></div>
          
        <!-- �������� ��������� ������� �� ������� � ������  -->
            <a name='table'></a>
            <br/>
            <div class='table_show_sys'>
            <?php
            if($_SESSION['SYSTEM_ID_m'] and $_SESSION['WEEK_ID']){
            $SYSTEM_ID = implode(',',$_SESSION['SYSTEM_ID_m']);
            $WEEK_ID = $_SESSION['WEEK_ID'];
            //��������� � �������� � �������
                $result = SysForHeder($SYSTEM_ID);
                if(count($_SESSION['SYSTEM_ID_m']) > 1){
                    echo "<p>���������� �� �������� ";                                        
                    $massiv = implode(', ',$result);                      
                    echo "<span style='color: green;'>$massiv</span>"; 
                }
                else{
                    echo "<p> ���������� �� ������� <span style='color: green;'>$result[0]</span>"; 
                }
                echo " �� ������ ";
                $result = WeekDescrForHeder($WEEK_ID);
                echo "<span style='color: green;'>$result</span>";
                echo ": ";
                // ���� ��������
                $result = ReportSysWeek($SYSTEM_ID, $WEEK_ID);
                if(!empty($result)){
                   echo "</p>"; 
                   echo "<table>";
			       echo "<tr>
                        <th>�������</th>
						<th class='tema'>����</th> 
						<th>������</th> 
						<th>����������</th>
                        <th class='auth'>�����</th> 					
					  </tr>";
                    foreach ($result as $item){
                        echo "<tr>";
                        foreach ($item as $row) echo "<td>$row</td>";                    
                        echo "</tr>"; 
                    }  
                	echo "</table>"; 
                } else {
                    echo '<span style="color:red;"> �� ������ ������ ������� ���.</span></p>';
                }                        
              } 
             ?> 
             </div>
          
         <!-- �������� ��������� ������� �� ������ � ������  -->
            <div class='table_show_sys'>
            <?php
             if($_SESSION['$AUTHOR_ID'] and $_SESSION['$WEEK_ID']){
                $AUTHOR_ID = $_SESSION['$AUTHOR_ID'];
                $WEEK_ID = $_SESSION['$WEEK_ID'];
                $result = AthWeekForHeder($AUTHOR_ID, $WEEK_ID);
                echo "<p> ���������� ���������� <span style='color: green;'>$result[0]</span> 
                               �� ������ <span style='color: green;'>$result[1]</span>: ";               
                // ���� ��������
                $result = ReportAthWeek($AUTHOR_ID, $WEEK_ID);
                if(!empty($result)){
                   echo "</p>"; 
                   echo "<table>";
			       echo "<tr>
						<th class='tema'>����</th> 
                        <th>�������</th>
						<th>������</th> 
						<th>����������</th>					
					  </tr>";
                    foreach ($result as $item){
                        echo "<tr>";
                        foreach ($item as $row) echo "<td>$row</td>";                    
                        echo "</tr>"; 
                    }  
                	echo "</table>"; 
                } else {
                    echo "<span style='color:red;'> �� ������ ������ � ���������� ������� ���.</span></p>";
                }                       
              } 
            ?> 
            </div> 
        
        
         <!--  �������� ��� ��������� ������� ���� �����������  -->
          
           <div class="last_reports">
           <h3>��������� ������ �����������:</h3>
           <?php 
           $result = LastReports();
           if(!empty($result)){
           echo "<table>";
           echo "<tr>                        
    				<th>���������</th> 
    				<th>������</th> 		
   			    </tr>";
                
           foreach ($result as $item){
                        echo "<tr>";                         
                        echo "<td><a href='?author_id=$item[0]&week_id=$item[2]'>$item[1]</a></td>";   
                        echo "<td>$item[3]</td>";               
                        echo "</tr>"; 
                    }  
                	echo "</table>"; 
           }else{
                echo "<p>���� ������ ���</p>";
           }           
           ?>           
           </div>
         
         
         <!--  �������� � ���������� �������� ���������� ����������  -->
           
           <div class="report_author"> 
           <?php
            if(isset($_SESSION['author_week'])){ 
                $result = $_SESSION['author_week'];                
                echo '<h4>����� ���������� <span style="color: green;">'.$result[0][0].'</span> �� ������ <span style="color: green;">'.$result[0][2].'</span> </h4>'; 
                echo "<table>";
    				echo "<tr>  
                            <th>����</th> 
                            <th>�������</th>
                            <th>����������</th>	
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