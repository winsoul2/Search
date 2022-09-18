<?php
//
//$curl = curl_init();
//
//curl_setopt_array($curl, array(
//    CURLOPT_URL => '"https://graph.facebook.com/oauth/access_token?client_id={your-app-id}&client_secret={your-app-secret}&grant_type=client_credentials"',
//    CURLOPT_RETURNTRANSFER => true,
//    CURLOPT_ENCODING => "",
//    CURLOPT_MAXREDIRS => 10,
//    CURLOPT_TIMEOUT => 30,
//    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
//    CURLOPT_CUSTOMREQUEST => "GET",
//    CURLOPT_HTTPHEADER => array(
//        "accept: application/json",
//        "authorization: Bearer YOUR_ACCESS_TOKEN",
//    ),
//));
//
//$response = curl_exec($curl);
//$err = curl_error($curl);
//
//curl_close($curl);
//
//if ($err) {
//    echo "cURL Error #:" . $err;
//} else {
//    echo $response;
//}
//?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ข้อมูลที่สนใจ</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
</head>

<body>


<div class="container">
    <h1 class="text-center">ข้อมูลที่สนใจ</h1>
    <hr>

    <label for="">ค้นหา</label>
    <input type="text" placeholder="" id="SearchName" name="SearchName" class="form-control" onchange="getData()">
    <input type="submit" id="btn" value="Search" class="btn btn-dark my-2">


    <table class="table table-bordered" id="datasearch">
        <thead>
        <tr>
            <th>ลำดับ</th>
            <th>ความสนใจ</th>
            <th>Audience Size</th>
            <th>หัวข้อ</th>
            <th>Path</th>
            <th>ค้นหา</th>

        </tr>
        </thead>
    </table>

    <script>
        function getData() {
            var search = document.getElementById('SearchName').value;

            FB.getLoginStatus(function(response) {
                if (response.status === 'connected') {
                    var accessToken = response.authResponse.accessToken;

                    var api = 'https://graph.facebook.com/4133323316775445/accounts?access_token=' +accessToken

                    console.log(response)

                    var table = '<thead><tr><th>ลำดับ</th><th>ความสนใจ</th> <th>Audience Size</th><th>หัวข้อ</th> <th>Path</th><th>ค้นหา</th></tr></thead>'
                    fetch(api)

                        .then(response => response.json())
                        .then(data => {
                            console.log(data);
                            var DATA = data.data;
                            for (let i = 0; i < DATA.length; i++) {
                                // const element = array[i];
                                table += '<tr>' +
                                    '<td>' + DATA[i]["id"] + '</td>' +
                                    '<td>' + DATA[i]["name"] + '</td>' +
                                    '<td>' + DATA[i]["audience_size"] + '</td>' +
                                    '<td>' + DATA[i]["path"] + '</td>' +
                                    '<td>' + DATA[i]["description"] + '</td>' +
                                    '<td>' + DATA[i]["topic"] + '</td>' +
                                    '</tr>'
                                // console.log(DATA[i]["id"])

                            }
                            document.getElementById("datasearch").innerHTML = table;
                            // }
                        });
                
                }
            });
        }

            // console.log(search);
            // var token = 'EAACt1uLZCTmoBABleG9k7oLFP6FnkQdSF54wpbb0ZAfd9vDDZAfLQYA2i6d9jI8yujGSAxbw2mw2oHzKSIUqZBGHnMi6Wh1vHaPV4eY1zpubboRMFJ2lj1bERUgVfAz4JR4q5ytF8oaeE5luRjpPpKg43HCoVd6nZAJNg0VgWjhBqVvP0VqK3elhpZBZBmKTtY8EnxKmsNzjHsScVpx0tjQeOv8Xwp2eJRFMZCGZBWb8yZBzAcS3i20AehYijL9BnD2wQZD';
            // var access_token = 'EAACt1uLZCTmoBADrmShBdbLWAZCwaiFN82lo2krwHkyI1J08wTjanmsftho83bl5jzEnDaotU0yIQ533ZBGOXocgfuN05iEM4pMTZBFukVcDAU32iFOHON4cZCZCLr4vbWZAfAtKOu7a8xrCPHCE0ZCEwbB2ZB3g1yv6tZBJMM8EggZABzPxFiBoKqtWsBZAS7DoEJgZD';
            // // var api = 'https://graph.facebook.com/search?type=adinterest&q=[' + search + ']&limit=20&locale=th&access_token=' + token;
            //
            // var api = 'https://graph.facebook.com/debug_token?input_token=' + token + '&access_token=' + access_token;

            // var api = 'https://graph.facebook.com/191138442989162/accounts?access_token=['+ accessToken +']';


        //     var table = '<thead><tr><th>ลำดับ</th><th>ความสนใจ</th> <th>Audience Size</th><th>หัวข้อ</th> <th>Path</th><th>ค้นหา</th></tr></thead>'
        //     fetch(api)
        //
        //         .then(response => response.json())
        //         .then(data => {
        //             console.log(data);
        //             var DATA = data.data;
        //             for (let i = 0; i < DATA.length; i++) {
        //                 // const element = array[i];
        //                 table += '<tr>' +
        //                     '<td>' + DATA[i]["id"] + '</td>' +
        //                     '<td>' + DATA[i]["name"] + '</td>' +
        //                     '<td>' + DATA[i]["audience_size"] + '</td>' +
        //                     '<td>' + DATA[i]["path"] + '</td>' +
        //                     '<td>' + DATA[i]["description"] + '</td>' +
        //                     '<td>' + DATA[i]["topic"] + '</td>' +
        //                     '</tr>'
        //                 // console.log(DATA[i]["id"])
        //
        //             }
        //             document.getElementById("datasearch").innerHTML = table;
        //         // }
        //         );
        // }
    </script>


</div>
</body>

</html>