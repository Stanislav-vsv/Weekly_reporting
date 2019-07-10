<?php require 'connection.php'?>
<?php require 'libs.php'?>
<?php session_start();?>
<?php 

if($_POST['AUTHOR_ID']){
   $_SESSION['AUTHOR_ID'] = $_POST['AUTHOR_ID'];
   header("Location:report_edit.php");
   exit;
}
$AUTHOR_ID = $_SESSION['AUTHOR_ID']; 


// ��������� ����� ������ ���� � ������ ��� ������    
if($_POST['select_tp_wk'] and (empty($_POST['TOPIC_ID']) or empty($_POST['WEEK_ID'])) and !$_POST['ACTION_DESC'] ){   
    $_SESSION['empty_topic_week']="<p class='red'>����������, �������� ���� ������ � ������.</p>"; 
    header("Location:report_edit.php");
    exit;
}elseif($_POST['select_tp_wk'] and !empty($_POST['TOPIC_ID']) and !empty($_POST['WEEK_ID'])){
    $flag = CheckUniqueAction($_POST['WEEK_ID'], $AUTHOR_ID, $_POST['TOPIC_ID']);
    if($flag<>0){
        $_SESSION['messeg']= "<p class='red'>���������� ��� ���� ���� � ������ ��� ����.</p>";
        unset($_SESSION['TOPIC_ID'], $_SESSION['WEEK_ID']);
        header("Location:report_edit.php");
        exit;
    }else{
        $_SESSION['TOPIC_ID'] = $_POST['TOPIC_ID'];
        $_SESSION['WEEK_ID'] = $_POST['WEEK_ID'];
        header("Location:report_edit.php");
        exit;            
    } 
}


// ��������� ����� ���������� ����� ���������� (��������� ����)
if($_POST['action_add'] and $_POST['ACTION_DESC']){
    $x = trim($_POST['ACTION_DESC']);
    if(empty($x)){
       $_SESSION['messeg']= "<p class='red'>�� �� ������� ����������.</p>"; 
       header("Location:report_edit.php");
       exit;
    }else{
        AddAction($_SESSION['WEEK_ID'], $AUTHOR_ID, $_SESSION['TOPIC_ID'], $_POST['ACTION_DESC']);
        $_SESSION['messeg']= '<p style="color: green;">���������� ���������.</p>';
        unset($_SESSION['TOPIC_ID'], $_SESSION['WEEK_ID'], $_SESSION['ACTION_DESC']);
        header("Location:report_edit.php");
        exit;
    }
}


// ��������� ����� ������������� ����������� (� ������� ��������� �����������)   
if($_POST['action_edit']){
    ActionEdit($_POST['WEEK_DESCR_edit'], $_POST['SYSTEM_NAME_edit'], $AUTHOR_ID, $_POST['TOPIC_ID_edit'], $_POST['ACTION_edit']);
    $_SESSION['TOPIC_ID_edit'] = $_POST['TOPIC_ID_edit'];
    $_SESSION['WEEK_DESCR_edit'] = $_POST['WEEK_DESCR_edit']; 
    header("Location:report_edit.php");
    exit;
} 

?>     


<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>   
        <meta http-equiv="Content-Type" content="text/html; charset=windows-1251" />   
        <title>������������ ������</title>
        <link href="style.css" type="text/css" rel="stylesheet"/>       
</head>
<body>	    
    
    <div id="wrapper" style="color: #696969;">  
    
    <a href="index.php"> ������� </a>
    <a href="setting.php" style="margin-left: 800px;"> ��� ��������� </a>
            
           
        <!--  ��������� � ������� ������� ����������  -->
                             
        <?php         
          $result = FamilyAuthor($AUTHOR_ID);
          echo "<h5>�������������� ��������� ������� ���������� <span style='color:green;'>$result</span></h5>"; 
                
        ?>
        
        <!--  ����� ������ ������� � ������ ��� ����������  -->
          
          <div class="select_tp_wk"> 
            <form class="tp_wk" method="POST" >
             <h3>�������� ����� ����������:</h3>
             <p>����: &nbsp; <label>
             <select name="TOPIC_ID" size="4">
             <?php
                $result_topic = TopicAuthor($AUTHOR_ID);
                foreach($result_topic as $item){
                    echo "<option value='$item[0]'>$item[1] <span>($item[2])</span></option>";
                }
             ?>
             </select>   
             &nbsp; ������: &nbsp; 
             <select name="WEEK_ID" size="4">   
             <?php
                $result_week = AllWeeks();
                foreach($result_week as $item){
                    echo "<option value='$item[0]'>$item[1]</option>";
                } 
             ?> 
             </select>
           
           <input class="submit" type="submit" name="select_tp_wk" value="�������" /> </label></p>
           </form>
           
           <?php 
                if(count($result_topic)==0){
                    echo "<p class='red'>� ��� ���� ��� ��� ��� �����������, �� ������ �� �������� � <a href='setting.php'>���� ����������</a></p>";
                }            
           ?>
           
           
           <!-- ����� �������� ���������� -->
           <?php 
             if($_SESSION['TOPIC_ID'] and $_SESSION['WEEK_ID'] and !$_SESSION['ACTION_DESC']){
                $topic_desc = TopicDesc($_SESSION['TOPIC_ID']);
                $week_desc = WeekDescrForHeder($_SESSION['WEEK_ID']);
           ?>     
                 <form class="action_add" method="POST" >             
                 <h4>������� ���������� �� ���� <span style='color:green;'><?php echo $topic_desc; ?></span> 
                              �� ������ <span style='color:green;'><?php echo $week_desc; ?></span>: </h4>                 
                 <textarea name="ACTION_DESC"> </textarea>
                 <input class="submit" type="submit" name="action_add" value="��������"/>
                 </form>
           <?php
             }     
           ?> 
           
           
           <?php 
           echo $_SESSION['empty_topic_week']; 
           echo $_SESSION['messeg']; 
           unset($_SESSION['empty_topic_week'], $_SESSION['messeg']);
           
           // �������� ����������� ������ ������� �� ������� � ������� ���������� ���� ����������� ����������
           
           if($_SESSION['TOPIC_ID'] and $_SESSION['WEEK_ID']){
               $system_desc = SystDesc($_SESSION['TOPIC_ID']); 
               echo "<div class='actions'><h4>���������� ������ ������� �� ������� <span style='color:green;'>$system_desc</span> 
                              �� ������: <span style='color:green;'>$week_desc:</span></h4>"; 
               $actions = ActionOtherAuthor($AUTHOR_ID, $_SESSION['WEEK_ID'], $_SESSION['TOPIC_ID']);
               if(!empty($actions)){
               ?> 
                  <table>
                  <tr><th>�����</th>
                      <th>������</th>
                      <th>����</th>
                      <th>���������</th></tr>
                 <?php
                  foreach($actions as $item){
                    echo '<tr>';
                       foreach($item as $data){
                        echo "<td>$data</td>";
                       } 
                    echo '</tr>'; 
                  } 
                  echo '</table></div>'; 
                   
                  }else{
                    echo '<p>����������� ������ ������� ���� ���.</p></div>';
                  }
              }
              
              ?>  
              
           </div>
            
           <!--  �������� � ���������� ������������  -->
           <div class="last_action">
           <h3>���� ��������� ����������:</h3>          
           <?php 
                
                $result = LastActionAuthor($AUTHOR_ID);  
                if(!empty($result)){
                    echo "<table>";
    				echo "<tr>                        
    						<th>����</th> 
                            <th>�������</th>
    						<th>������</th> 
    						<th>����������</th> 
                            </form>					
    					  </tr>";
    				foreach ($result as $item)   
    				  {                                              
						echo "<tr>";                                        
					    echo "<form action='' method='POST' name='action_edit'>"; 
						echo "<td>
                              <input type='hidden' name='TOPIC_ID_edit' value='$item[0]' /> 
                              <textarea class='text_input' name='TOPIC_NAME_edit' readonly='readonly'>$item[1]</textarea></td>";
                        echo "<td><textarea class='td_input' name='SYSTEM_NAME_edit' readonly='readonly'>$item[2]</textarea></td>";
                        echo "<td><textarea class='td_input' name='WEEK_DESCR_edit' readonly='readonly'>$item[3]</textarea></td>";
                        
                        if($item[0]==$_SESSION['TOPIC_ID_edit']
                            and $item[3]==$_SESSION['WEEK_DESCR_edit']){
                            echo "<td><textarea class='text' name='ACTION_edit' style='background-color: #F0FFF0; float: left;' title='�� ������ ��������� � ��������� �������� ����������'>$item[4]</textarea> 
                                </br></br><span  style='color: #8FBC8F; margin-left: 10px;'>������</span></br></br></br><input class='submit_input' type='submit' name='action_edit' value='Save' /></td>";
                        }else{
                            echo "<td><textarea class='text' name='ACTION_edit' title='�� ������ �������� � ��������� �������� ����������'>$item[4]</textarea> 
                                <input class='submit_input' type='submit' name='action_edit' value='Save' /></td>";
                        }      
						echo "</form>";
						echo "</tr>";
    				  }    				  
    				echo "</table>";
                } else {
                    echo "<p>���� ������ ���</p>";
                }
                unset ($_SESSION['WEEK_DESCR_edit'], $_SESSION['TOPIC_ID_edit'], $_SESSION['ACTION_edit']);
                
			?>	
            </div>        
    
   <?php ora_logoff($connection); ?>  
       
</div>     
</body>
</html>
