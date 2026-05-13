<?php
include('../config/db.php');

$demo = [
    ['Ali Ahmed', '2018-05-10', 'Male', 'A+'],
    ['Sara Khan', '2019-09-21', 'Female', 'B+'],
    ['Hassan Raza', '2020-12-01', 'Male', 'O+'],
    ['Ayesha Noor', '2017-03-15', 'Female', 'AB+'],
    ['Usman Tariq', '2016-07-30', 'Male', 'B-']
];

foreach($demo as $d){

    $unique = 'CHILD_' . strtoupper(uniqid());

    mysqli_query($conn, "
        INSERT INTO child_profiles 
        (parent_user_id, child_name, dob, gender, blood_group, unique_reg_id)
        VALUES 
        (1, '$d[0]', '$d[1]', '$d[2]', '$d[3]', '$unique')
    ");
}

echo "Demo children inserted successfully!";
?>