<?php
include('../config/db.php');

$hospitals = [
    ['Civil Hospital Karachi', 'Liaquatabad Road', 'Karachi', '021-99215740'],
    ['Aga Khan University Hospital', 'Stadium Road', 'Karachi', '021-34861000'],
    ['Jinnah Postgraduate Medical Centre', 'Rafiqui Shaheed Road', 'Karachi', '021-99201300'],
    ['Indus Hospital', 'Korangi Industrial Area', 'Karachi', '021-35112709'],
    ['South City Hospital', 'Clifton', 'Karachi', '021-35374000']
];

foreach($hospitals as $h){

    mysqli_query($conn, "
        INSERT INTO hospitals (name, address, city, phone)
        VALUES ('$h[0]', '$h[1]', '$h[2]', '$h[3]')
    ");
}

echo "Fake hospitals inserted successfully!";
?>