<?php require 'connection.php'?>
<?php require 'libs.php'?>
<?php session_start();?>
<?php 

// ��������� ����� ����� ������ � �������    
if($_POST['system_link_author']){
   if(!empty($_POST['SYSTEM'])){
      $SYSTEM_ID_massiv = $_POST['SYSTEM']; 
      foreach($SYSTEM_ID_massiv as $item){
        $result = SystemLinkAuthor($item, $_SESSION['AUTHOR_ID']);
        if($result<>0) 
          $result_massiv[]=$result;
      }
      if(count($result_massiv)>0){
        $SYSTEM_ID = implode(',',$result_massiv);
        $SYSTEM_NAME_massiv = SysForHeder($SYSTEM_ID);
        $SYSTEM_NAME =implode(', ',$SYSTEM_NAME_massiv);
        if(count($result_massiv)>1){
            $_SESSION['res'] = "<p class='red'>������� <span style='color:blue;'>$SYSTEM_NAME</span> � ��� ��� ����</p>" ;
          }else{
            $_SESSION['res'] = "<p class='red'>������� <span style='color:blue;'>$SYSTEM_NAME</span> � ��� ��� ����</p>" ;
          }
      }else{
        $SYSTEM_ID = implode(',',$SYSTEM_ID_massiv);
        $SYSTEM_NAME_massiv = SysForHeder($SYSTEM_ID);
        $SYSTEM_NAME =implode(', ',$SYSTEM_NAME_massiv);
        if(count($SYSTEM_ID_massiv)>1){
          $_SESSION['res'] = "<p style='color: green'>������� <span style='color:blue;'>$SYSTEM_NAME</span> ���������</p>" ;
        }else{
          $_SESSION['res'] = "<p style='color: green'>������� <span style='color:blue;'>$SYSTEM_NAME</span> ���������</p>" ;
        }
      }           
    }else{
        $_SESSION['res'] = "<p class='red'>���������� �������� �������</p>" ;
    }
    header("Location: setting.php"); 
    exit;         
}

// ��������� ����� ���������� ����� �������

if($_POST['new_system_add']){
    $NEW_SYSTEM = trim($_POST['NEW_SYSTEM']);
    if(!empty($NEW_SYSTEM)){
        $result = NewSystemAdd($NEW_SYSTEM);
        if($result==0){
            $_SESSION['new_system_add'] = "<p style='color: green;'>��������� ����� �������:<span style='color:blue'> 
                                        $NEW_SYSTEM</span> <span style='color: gray'>(�������� �� ��� ����, ����� ������������ � �������)</span></p>"; 
        }else{
            $_SESSION['new_system_add'] = "<p class='red'>������� <span style='color:green'> 
                                        $NEW_SYSTEM</span> ��� ����!</p>"; 
        }  
    }else{
        $_SESSION['new_system_add'] = "<p class='red'>���������� ������� �������!</p>";
    }
   header("Location: setting.php");
   exit; 
}

// ��������� ����� ���������� ����� ������
/*
if($_POST['week_add']){
    $NEW_WEEK = $_POST['WEEK'];
    if(!empty($NEW_WEEK)){
        $result = NewWeekAdd($NEW_WEEK);
        if($result==0){
            $_SESSION['week_add'] = "<p style='color: green;'>��������� ����� ������:<span style='color:blue'> 
                                        $NEW_WEEK</span></p>"; 
        }else{
            $_SESSION['week_add'] = "<p class='red'>������ <span style='color:green'> 
                                        $NEW_WEEK</span> ��� ����!</p>"; 
        }  
    }else{
        $_SESSION['week_add'] = "<p class='red'>���������� ������� ������!</p>";
    }
   header("Location: setting.php");
   exit; 
}
*/
// ��������� ����� ���������� ����� ���� �������

if($_POST['topic_add']){
    $SYSTEM_ID = $_POST['SYSTEM'];
    $TOPIC = trim($_POST['TOPIC']);
    if(!empty($SYSTEM_ID) and !empty($TOPIC)){
       AddTopic($SYSTEM_ID, $_SESSION['AUTHOR_ID'], $TOPIC); 
       $_SESSION['topic_add'] = "<p style='color: green;'>��������� ����� ����:</br><span style='color:blue'> 
                                        $TOPIC</span></p>";
    }else{
        $_SESSION['topic_add'] = "<p class='red'>���������� ������� ������� � ����!</p>";
    }
    header("Location: setting.php");
    exit;
}


// ��������� ����� �������������� ���� �������

if($_POST['topic_edit']){
    $TOPIC_ID = $_POST['topic_id'];
    $TOPIC_NAME = $_POST['topic_name'];
    TopicEdit($TOPIC_ID, $TOPIC_NAME);
    
    header("Location: setting.php");
    exit;
}

// ��������� ����� �� ���� ��� ��������������

if($_GET['topic_id']){
    $TOPIC_ID = (int)$_GET['topic_id'];
    $_SESSION['topic_edit'] = TopicForEdit($TOPIC_ID);    
    header("Location: setting.php");
    exit;
}
  
?>     


<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>   
        <meta http-equiv="Content-Type" content="text/html; charset=windows-1251" />   
        <title>������������ ������</title>
    	<link href="style.css" type="text/css" rel="stylesheet" />    
</head>
<body>	    
    
    <div id="wrapper" >  
    
    <a href="report_edit.php" style="margin: 30px;"> << ���������� </a>
    
            
           
        <!--  ��������� � ������� ������� ����������  -->
                             
        <?php  
          
		  $result = FamilyAuthor($_SESSION['AUTHOR_ID']);
          echo "<h6>��������� ���������� <span style='color:green;'>$result</span></h6>"; 
		?>
        
         <!-- ����� ���������� ���� -->
         
         <div class="topic_add"> 
            <form action="" method="POST" >
             <h4>�������� ����� ����:</h4>
             <p>��� �������: &nbsp; 
             <?php
                $result = SystemDescrForAuthor($_SESSION['AUTHOR_ID']);
    			echo "<select name='SYSTEM'>";	
                foreach($result as $item){
                    echo "<option value='$item[0]'>$item[1]</option>";
                } 
    			echo "</select>"; 
    	     ?>
             </p>       
             <p>��������: 
             <br/>  
             <input class="with" type="text" name="TOPIC" /> 
             <span>������: �������� ���� </span></p>
             <input class="submit" type="submit" name="topic_add" value="��������" /> 
             
             <?php
                if($_SESSION['topic_add']){
                    echo $_SESSION['topic_add'];
                    unset($_SESSION['topic_add']);
                }            
               
             ?>
                          
           </form>
           
        <!-- ������ ��� ������ -->
        
        <div class="topic_order"> 
        <?php
        
             $topic_massiv = TopicAuthor($_SESSION['AUTHOR_ID']);                
                echo "<h4>���� ����:</h4>";
                if(!empty($topic_massiv)){
                    foreach($topic_massiv as $item){
                    //echo "<span style='color:green;'>$item[1] ($item[2])</span></br>";
                    echo "<a href='?topic_id=$item[0]' title='�������������'>$item[1]</a> ($item[2])</br>";
                    } 
                    echo "</p>"; 
                }else{
                    echo "<span style='color:green;'>� ��� ���� ��� ����������� ���.</span>";
                }
        ?>
        </div>
        
        <!-- �����(�����������) �������������� ���� -->
                  
         <?php         
         if(isset($_SESSION['topic_edit'])){
            $result = $_SESSION['topic_edit']; ?> 
            <div class="topic_edit">           
            <form method="POST">
                <h4>�������������� ����</h4> 
                <p><label>
                <input type="hidden" name="topic_id" value="<?php echo "$result[0]"; ?>" />
                <input type="text" name="topic_name" style="width: 300px;" value=" <?php echo "$result[1]"; ?> " />
                <span>�������: <?php echo "$result[2]"; ?></span>                
                <input class="submit" type="submit" name="topic_edit" value="���������"/>
                </label></p>            
            </form>
            </div>            
         <?php
         unset($_SESSION['topic_edit']);
         }
         ?>
         
        </div>
       
           
         <!-- ����� ����� ������� � ������� -->
         
         <div class="sys_link_auth"> 
         <form method="POST" >
             <h4>������� ��� ���� �������(��) �� ������ ������: </h4>
             <p>
             <?php 
    	 	 $result = AllSystems();
             echo "<select multiple name='SYSTEM[]' size='4' >";
        	 foreach($result as $item){ 	                    
        				    $SYSTEM_ID = $item[0];
                        	$SYSTEM_NAME = $item[1];
                            echo "<option value='$SYSTEM_ID'>$SYSTEM_NAME</option>";												
        			  } 
       	     echo "</select>"; 				
             ?>    
             <input class="submit" type="submit" name="system_link_author" value="�������" />
             </p>
             <?php          
             if($_SESSION['res']){
                 echo $_SESSION['res'];
                 unset($_SESSION['res']);
             }             
             echo '<p style="color:green;"><span style="color:blue;">���� �������: </span>';         
             $result = SystemDescrForAuthor($_SESSION['AUTHOR_ID']);
             if(!empty($result)){
                foreach($result as $item){
                $data[] = $item[1];
                }
                echo implode(', ',$data).'</p>';            
             }else{
              echo '����������� ������ ���� ��� </p>';
             }             
             ?>
         </form>
         
          <!-- ����� ���������� ����� ������� -->
          
         <form method="POST" >
             <h4>�������� ����� ������� (���� ����� ��� ��� � ����� ������):</h4>    
             <p><label>
             <input type="text" name="NEW_SYSTEM" />  
             <input class="submit" type="submit" name="new_system_add" value="��������" /> </label></p>
             <p style="color: gray; ">������: ����</p>
             <?php              
                 if($_SESSION['new_system_add']){
                    echo $_SESSION['new_system_add'];
                    unset($_SESSION['new_system_add']);
                 }
             ?>
         </form>
         
         </div>   
         
         <!-- ����� ���������� ����� ������ -->
             
      <!--   <div class="week_add"> 
         <form method="POST">
         
             <h4>�������� ����� ������ (���� ����� ��� ���):</h4>
             
             <p><label><input type="text" name="WEEK" />
             
             <input class="submit" type="submit" name="week_add" value="��������" />  
             </label></p> 
             <p style="color: gray;">������: 30.03.2015</p> 
         </form>
         
           <div> 
           <?php  
         /*    $result = AllWeeks();
             echo "<p style='color:blue;'>��� ����:</br>";
    	  	 foreach($result as $item)   
    			{ 	                    
    				echo "<span style='color:green;'>$item[1]</span></br>";												
    			}  */
            ?>
            </p>
            </div>
            
            <?php
           /*     if($_SESSION['week_add']){
                echo $_SESSION['week_add'];
                unset($_SESSION['week_add']);
                } */
            ?>
         </div>  -->
         
       
            
              
          
    <?php ora_logoff($connection); ?>     
            
    </div>
     
</body>
</html>
