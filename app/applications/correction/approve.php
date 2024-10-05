<?php
 session_start();
require '../../../includes/db-config.php';

$action = $_REQUEST['action'];
if($action=='approve')
{
    $query = 'UPDATE correction_form SET `status`=1 WHERE students_id='.$_REQUEST['id'];
    $update = $conn->query($query);
    if($update)
    {
        if(array_key_exists('Subjects',$_REQUEST['remark']))
        {
            unset($_REQUEST['remark']['Subjects']);
        }
        $updatedData = '';
            foreach($_REQUEST['remark'] as $column => $value)
            {
                if($column=='Student_Name')
                {
                    $first_name= null;
                    $middle_name= null;
                    $last_name= null;
                    $nameArr = explode(' ',$value);
                    if(count($nameArr)==1)
                    {
                        $first_name = $nameArr[0];
                    }
                    elseif(count($nameArr)==2)
                    {
                        $first_name = $nameArr[0];
                        $middle_name = $nameArr[1];
                    }
                    elseif(count($nameArr)>2)
                    {
                        $first_name = $nameArr[0];
                        $middle_name = $nameArr[1];
                        unset($nameArr[0],$nameArr[1]);
                        $last_name = implode(' ',$nameArr);
                    }
                    $updatedData .= 'First_Name='."'$first_name'".', '.'Middle_Name='."'$middle_name'".', '.'Last_Name='."'$last_name'";
                }
                else
                {
                    $updatedData .= ', '."$column" .'='."'$value'";
                }
            }
            $query1 = "UPDATE Students SET $updatedData WHERE ID=".$_REQUEST['id'];
            $updateStudent = $conn->query($query1);
            if($updateStudent)
            {
                echo json_encode(['status'=>200, 'message'=>'Request approved successfully!']);
            }else{
                echo json_encode(['status'=>400, 'message'=>'Something went wrong!']);
            }
    }else{
        echo json_encode(['status'=>400, 'message'=>'Something went wrong!']);
    }
}
else
{
    $query = 'UPDATE correction_form SET `status`=2 WHERE students_id='.$_REQUEST['id'] ;
    $update = $conn->query($query);
    if($update)
    {
        echo json_encode(['status'=>200, 'message'=>'Request rejected successfully!']);
    }else{
        echo json_encode(['status'=>400, 'message'=>'Something went wrong!']);
    }
}

?>