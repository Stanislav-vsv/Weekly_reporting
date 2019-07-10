<?php require 'connection.php'?>
<?php require 'libs.php'?>
<?php session_start();?>
<?php 

// обработка формы связи автора и системы    
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
            $_SESSION['res'] = "<p class='red'>Системы <span style='color:blue;'>$SYSTEM_NAME</span> у Вас уже есть</p>" ;
          }else{
            $_SESSION['res'] = "<p class='red'>Система <span style='color:blue;'>$SYSTEM_NAME</span> у Вас уже есть</p>" ;
          }
      }else{
        $SYSTEM_ID = implode(',',$SYSTEM_ID_massiv);
        $SYSTEM_NAME_massiv = SysForHeder($SYSTEM_ID);
        $SYSTEM_NAME =implode(', ',$SYSTEM_NAME_massiv);
        if(count($SYSTEM_ID_massiv)>1){
          $_SESSION['res'] = "<p style='color: green'>Системы <span style='color:blue;'>$SYSTEM_NAME</span> добавлены</p>" ;
        }else{
          $_SESSION['res'] = "<p style='color: green'>Система <span style='color:blue;'>$SYSTEM_NAME</span> добавлена</p>" ;
        }
      }           
    }else{
        $_SESSION['res'] = "<p class='red'>Пожалуйста выбирете систему</p>" ;
    }
    header("Location: setting.php"); 
    exit;         
}

// обработка формы добавления новой системы

if($_POST['new_system_add']){
    $NEW_SYSTEM = trim($_POST['NEW_SYSTEM']);
    if(!empty($NEW_SYSTEM)){
        $result = NewSystemAdd($NEW_SYSTEM);
        if($result==0){
            $_SESSION['new_system_add'] = "<p style='color: green;'>Добавлена новая система:<span style='color:blue'> 
                                        $NEW_SYSTEM</span> <span style='color: gray'>(выберете ее для себя, чтобы использовать в отчетах)</span></p>"; 
        }else{
            $_SESSION['new_system_add'] = "<p class='red'>Система <span style='color:green'> 
                                        $NEW_SYSTEM</span> уже есть!</p>"; 
        }  
    }else{
        $_SESSION['new_system_add'] = "<p class='red'>Пожалуйста укажите систему!</p>";
    }
   header("Location: setting.php");
   exit; 
}

// обработка формы добавления новой недели
/*
if($_POST['week_add']){
    $NEW_WEEK = $_POST['WEEK'];
    if(!empty($NEW_WEEK)){
        $result = NewWeekAdd($NEW_WEEK);
        if($result==0){
            $_SESSION['week_add'] = "<p style='color: green;'>Добавлена новая неделя:<span style='color:blue'> 
                                        $NEW_WEEK</span></p>"; 
        }else{
            $_SESSION['week_add'] = "<p class='red'>Неделя <span style='color:green'> 
                                        $NEW_WEEK</span> уже есть!</p>"; 
        }  
    }else{
        $_SESSION['week_add'] = "<p class='red'>Пожалуйста укажите неделю!</p>";
    }
   header("Location: setting.php");
   exit; 
}
*/
// обработка формы добавления новой темы отчетов

if($_POST['topic_add']){
    $SYSTEM_ID = $_POST['SYSTEM'];
    $TOPIC = trim($_POST['TOPIC']);
    if(!empty($SYSTEM_ID) and !empty($TOPIC)){
       AddTopic($SYSTEM_ID, $_SESSION['AUTHOR_ID'], $TOPIC); 
       $_SESSION['topic_add'] = "<p style='color: green;'>Добавлена новая тема:</br><span style='color:blue'> 
                                        $TOPIC</span></p>";
    }else{
        $_SESSION['topic_add'] = "<p class='red'>Пожалуйста укажите систему и тему!</p>";
    }
    header("Location: setting.php");
    exit;
}


// обработка формы редактирования темы отчетов

if($_POST['topic_edit']){
    $TOPIC_ID = $_POST['topic_id'];
    $TOPIC_NAME = $_POST['topic_name'];
    TopicEdit($TOPIC_ID, $TOPIC_NAME);
    
    header("Location: setting.php");
    exit;
}

// обработка клика по теме для редактирования

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
        <title>Еженедельные отчеты</title>
    	<link href="style.css" type="text/css" rel="stylesheet" />    
</head>
<body>	    
    
    <div id="wrapper" >  
    
    <a href="report_edit.php" style="margin: 30px;"> << активности </a>
    
            
           
        <!--  Заголовок с выводом Фамилии сотрудника  -->
                             
        <?php  
          
		  $result = FamilyAuthor($_SESSION['AUTHOR_ID']);
          echo "<h6>Настройки сотрудника <span style='color:green;'>$result</span></h6>"; 
		?>
        
         <!-- Форма добавления темы -->
         
         <div class="topic_add"> 
            <form action="" method="POST" >
             <h4>Добавить новую тему:</h4>
             <p>Для системы: &nbsp; 
             <?php
                $result = SystemDescrForAuthor($_SESSION['AUTHOR_ID']);
    			echo "<select name='SYSTEM'>";	
                foreach($result as $item){
                    echo "<option value='$item[0]'>$item[1]</option>";
                } 
    			echo "</select>"; 
    	     ?>
             </p>       
             <p>Название: 
             <br/>  
             <input class="with" type="text" name="TOPIC" /> 
             <span>Пример: Загрузка Ядра </span></p>
             <input class="submit" type="submit" name="topic_add" value="Добавить" /> 
             
             <?php
                if($_SESSION['topic_add']){
                    echo $_SESSION['topic_add'];
                    unset($_SESSION['topic_add']);
                }            
               
             ?>
                          
           </form>
           
        <!-- Список тем автора -->
        
        <div class="topic_order"> 
        <?php
        
             $topic_massiv = TopicAuthor($_SESSION['AUTHOR_ID']);                
                echo "<h4>Ваши темы:</h4>";
                if(!empty($topic_massiv)){
                    foreach($topic_massiv as $item){
                    //echo "<span style='color:green;'>$item[1] ($item[2])</span></br>";
                    echo "<a href='?topic_id=$item[0]' title='Редактировать'>$item[1]</a> ($item[2])</br>";
                    } 
                    echo "</p>"; 
                }else{
                    echo "<span style='color:green;'>У Вас пока нет добавленных тем.</span>";
                }
        ?>
        </div>
        
        <!-- Форма(всплывающая) редактирования темы -->
                  
         <?php         
         if(isset($_SESSION['topic_edit'])){
            $result = $_SESSION['topic_edit']; ?> 
            <div class="topic_edit">           
            <form method="POST">
                <h4>Редактирование темы</h4> 
                <p><label>
                <input type="hidden" name="topic_id" value="<?php echo "$result[0]"; ?>" />
                <input type="text" name="topic_name" style="width: 300px;" value=" <?php echo "$result[1]"; ?> " />
                <span>Система: <?php echo "$result[2]"; ?></span>                
                <input class="submit" type="submit" name="topic_edit" value="Сохранить"/>
                </label></p>            
            </form>
            </div>            
         <?php
         unset($_SESSION['topic_edit']);
         }
         ?>
         
        </div>
       
           
         <!-- Форма связи системы с автором -->
         
         <div class="sys_link_auth"> 
         <form method="POST" >
             <h4>Выбрать для себя систему(мы) из общего списка: </h4>
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
             <input class="submit" type="submit" name="system_link_author" value="Выбрать" />
             </p>
             <?php          
             if($_SESSION['res']){
                 echo $_SESSION['res'];
                 unset($_SESSION['res']);
             }             
             echo '<p style="color:green;"><span style="color:blue;">Ваши системы: </span>';         
             $result = SystemDescrForAuthor($_SESSION['AUTHOR_ID']);
             if(!empty($result)){
                foreach($result as $item){
                $data[] = $item[1];
                }
                echo implode(', ',$data).'</p>';            
             }else{
              echo 'добавленных систем пока нет </p>';
             }             
             ?>
         </form>
         
          <!-- Форма добавления новой системы -->
          
         <form method="POST" >
             <h4>Добавить новую систему (если такой еще нет в общем списке):</h4>    
             <p><label>
             <input type="text" name="NEW_SYSTEM" />  
             <input class="submit" type="submit" name="new_system_add" value="Добавить" /> </label></p>
             <p style="color: gray; ">Пример: Ядро</p>
             <?php              
                 if($_SESSION['new_system_add']){
                    echo $_SESSION['new_system_add'];
                    unset($_SESSION['new_system_add']);
                 }
             ?>
         </form>
         
         </div>   
         
         <!-- Форма добавления новой недели -->
             
      <!--   <div class="week_add"> 
         <form method="POST">
         
             <h4>Добавить новую неделю (если такой еще нет):</h4>
             
             <p><label><input type="text" name="WEEK" />
             
             <input class="submit" type="submit" name="week_add" value="Добавить" />  
             </label></p> 
             <p style="color: gray;">Пример: 30.03.2015</p> 
         </form>
         
           <div> 
           <?php  
         /*    $result = AllWeeks();
             echo "<p style='color:blue;'>Уже есть:</br>";
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
