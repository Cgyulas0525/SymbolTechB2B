<?php
$con=mysqli_connect("localhost", "root", "", "fitness");

if(!$con){
    echo "DB not Connected...";
}
else{
    $result=mysqli_query($con, "Select * from users");
    if($result>0){
        $xml = new DOMDocument("1.0");

// It will format the output in xml format otherwise
// the output will be in a single row
        $xml->formatOutput=true;
        $fitness=$xml->createElement("users");
        $xml->appendChild($fitness);
        while($row=mysqli_fetch_array($result)){
            $user=$xml->createElement("user");
            $fitness->appendChild($user);

            $uid=$xml->createElement("uid", $row['uid']);
            $user->appendChild($uid);

            $uname=$xml->createElement("uname", $row['uname']);
            $user->appendChild($uname);

            $email=$xml->createElement("email", $row['email']);
            $user->appendChild($email);

            $password=$xml->createElement("password", $row['password']);
            $user->appendChild($password);

            $description=$xml->createElement("description", $row['description']);
            $user->appendChild($description);

            $role=$xml->createElement("role", $row['role']);
            $user->appendChild($role);

            $pic=$xml->createElement("pic", $row['pic']);
            $user->appendChild($pic);

        }
        echo "<xmp>".$xml->saveXML()."</xmp>";
        $xml->save("report.xml");
    }
    else{
        echo "error";
    }
}
?>
