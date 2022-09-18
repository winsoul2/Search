<?php
// Create connection
$conn = mysqli_connect('127.0.0.1', 'root', '', 'uptest1_db');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ข้อมูลที่สนใจ</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>


    <div class="container">
        <h1 class="text-center">ข้อมูลที่สนใจ</h1>


        <hr>
        <label for="">ค้นหา</label>
        <input type="text" placeholder="" id="SearchName" name="SearchName" class="form-control">
        <input type="submit" id="btn" value="ค้นหา" class="btn btn-dark my-2" onclick="getData()">
        <!-- ปุ่มด้านขวา -->
        <form action="save.php" method="POST">
            <input type="button" id="btn" value="ค้นหาที่สนใจและบันทึก" class="btn btn-dark my-2" onclick="search()">

            <table class="table table-bordered" id="datasearch">
                <thead>
                    <tr>
                        <th>เลือก</th>
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
                window.fbAsyncInit = function() {
                    FB.init({
                        appId: 'your-app-id',
                        autoLogAppEvents: true,
                        xfbml: true,
                        version: 'v11.0'
                    });
                };
            </script>
            <script async defer crossorigin="anonymous" src="https://connect.facebook.net/en_US/sdk.js"></script>
            <!-- <script async defer crossorigin="anonymous" src="https://connect.facebook.net/es_LA/sdk.js"></script> -->
            <script></script>
            <script>
                function getData() {
                    var search = document.getElementById('SearchName').value;

                    // console.log(search);
                    var token = 'EAACt1uLZCTmoBAC2qyEYOR9lPZCfsYZCjMFBrz6R1cNaKnrrnvViSSU3NScv4TlQZCkCUq5xcPKyJ5lWzJZAzsNbl7ahUOFldn5ydk1ZA7f17BJmhLXyMOWSpXtjHB5pbpVZAqR7Uk7JIzAKzmZAexcpyFzNeFQSfOnUz86vSDn1kja5kwBhMmKYo35NaQIJQ7cZD'
                    var api = 'https://graph.facebook.com/search?type=adinterest&q=[' + search + ']&limit=20&locale=th&access_token=' + token;

                   


                    var table = '<thead><tr><th>เลือก</th><th>ลำดับ</th><th>ความสนใจ</th> <th>Audience Size</th><th>หัวข้อ</th> <th>Path</th><th>ค้นหา</th></tr></thead>'
                    fetch(api)

                        .then(response => response.json())
                        .then(data => {
                            console.log(data);
                            var DATA = data.data;

                            for (let i = 0; i < DATA.length; i++) {
                                // const element = array[i];
                                table += '<tr>' +
                                    '<td>' + '<input type="checkbox" name="name1" value = "' + DATA[i]["name"] + '">' + '</td>' +
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
                        });
                }

                function display(Total) {
                    var table = '<thead><tr><th>เลือก</th><th>ลำดับ</th><th>ความสนใจ</th> <th>Audience Size</th><th>หัวข้อ</th> <th>Path</th><th>ค้นหา</th></tr></thead>'
                    
                    for (k = 0; k < Total.length; k++) {
                        table += '<tr>' +
                            '<td>' + '<input type="checkbox" name="name2" value = "' + Total[k]["name"] + '">' + '</td>' +
                            '<td>' + Total[k]["id"] + '</td>' +
                            '<td>' + Total[k]["name"] + '</td>' +
                            '<td>' + Total[k]["audience_size"] + '</td>' +
                            '<td>' + Total[k]["path"] + '</td>' +
                            '<td>' + Total[k]["description"] + '</td>' +
                            '<td>' + Total[k]["topic"] + '</td>' +
                            '</tr>'
                    }
                    document.getElementById("datasearch").innerHTML = table;
                }

                async function search() {
                    var search = document.getElementById('datasearch');
                    var fisrt = document.querySelectorAll('tbody>tr>td>input');
                    var A = [];
                    var Total = [];
                    var check = true;
                    var count = 0;


                    fisrt.forEach(function(s) {
                        if (s.checked) {
                            A.push(s.value)
                            console.log(A)
                        }
                    })



                    var index = 0;
                    for (let i = 0; i < A.length; i++) {
                        var token = 'EAACt1uLZCTmoBAC2qyEYOR9lPZCfsYZCjMFBrz6R1cNaKnrrnvViSSU3NScv4TlQZCkCUq5xcPKyJ5lWzJZAzsNbl7ahUOFldn5ydk1ZA7f17BJmhLXyMOWSpXtjHB5pbpVZAqR7Uk7JIzAKzmZAexcpyFzNeFQSfOnUz86vSDn1kja5kwBhMmKYo35NaQIJQ7cZD'
                        var api = 'https://graph.facebook.com/search?type=adinterest&q=[' + A[i] + ']&limit=20&locale=th&access_token=' + token;
                        //รอให้ฟังชันทำงานเสร็จก่อน
                        await fetch(api)
                            .then(response => response.json())
                            .then(data => {
                                console.log(data);
                                var DATA = data.data;
                                check = true;
                                for (j = 0; j < DATA.length; j++) {
                                    check = true;
                                    for (p = 0; p < Total.length; p++) {
                                        if (DATA[j]["name"] == Total[p]["name"]) {
                                            check = false;
                                            break;
                                        }
                                    }
                                    if (check == true) {
                                        Total[index] = DATA[j]
                                        index++
                                    }

                                }
                            })
                            .then(() => {
                                console.log(Total)
                                $.ajax({
                                    type: "POST",
                                    url: "save.php",
                                    dataType: "json",
                                    data: { data: Total },
                                    success: function(data) {
                                        //location.reload(true);
                                    },
                                    error: function(e) {
                                        //$("#err").html(e).fadeIn();
                                    }
                                })

                                display(Total)
                            })
                    }
                }
            </script>


    </div>
</body>

</html>