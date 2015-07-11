<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
    </head>
    <body>
        <form method="post">
            <input type="text" name="username" placeholder="Username"/><br />
            <input type="password" name="password" placeholder="Password"/><br />
            <input type="submit" /><br />
        </form>
        <?php
            function objectToArray( $object )
            {
                if( !is_object( $object ) && !is_array( $object ) )
                {
                    return $object;
                }
                if( is_object( $object ) )
                {
                    $object = get_object_vars( $object );
                }
                return array_map( 'objectToArray', $object );
            }
            
            
            if($_SERVER["REQUEST_METHOD"] == "POST"){
                $username = $_POST["username"];
                $password = $_POST["password"];
                $params = array(
                    'uri' => 'http://publicportal.rest.powerschool.pearson.com/xsd',
                    'location' => 'https://school.maclay.org/pearson-rest/services/PublicPortalServiceJSON',
                    'login' => "pearson",
                    'password' => "m0bApP5",
                    'use' => SOAP_LITERAL
                );
                $client = new SoapClient(null , $params);
                $login = $client->__soapCall(
                    'login',
                    Array(
                        'username' => $username,
                        'password' => $password,
                        'userType' => 2
                    )
                );
                if ($login->userSessionVO === null) {
                    echo "Incorrect username or password";
                }
                else{
                    echo "Log in succeeded. Welcome $username";
//                    $studentParams = array(
//                        'uri' => 'http://student.rest.powerschool.pearson.com/xsd',
//                        'location' => 'https://school.maclay.org/pearson-rest/services/StudentServiceJSON',
//                        'login' => "pearson",
//                        'password' => "m0bApP5",
//                        'use' => SOAP_LITERAL
//                    );
//                    
//                    $studentClient = new SoapClient("studentservice.xml", $studentParams);
//                    echo '<pre>' . var_export($studentClient->__getFunctions(), true) . '</pre>';
//                    try{
//                        $username = $studentClient->__soapCall(
//                            "getStudentDetails",
//                            objectToArray($login)
//                        );
//                        echo '<pre>' . var_export($username, true) . '</pre>';
//                    } catch (Exception $ex) {
//                        echo $ex->getMessage();
//                    }
                    
                }
            }
            else{
                echo "Please log in";
            }
        ?>
    </body>
</html>
