<!DOCTYPE html>
<html lang="en">
  <head>
    <?php
        $PAGE_TITLE = "GDPR View";
        require("head.php");
    ?>
    <style>
        .container {
            text-align: center;
        }
        table {
            margin-left: auto;
            margin-right: auto;
            border-collapse: collapse;
        }
        table td, table th{
            border: solid 1px #000;
            word-wrap: break-word;
        }
    </style>
    <script src="js/verify_credentials.js"></script>
    <script>
        function toggleJSON(){
            var json = document.getElementById("json");
            var toggle = document.getElementById("toggle");
            if(json.style.display == "none"){
                json.style.display = "inherit";
                toggle.innerHTML = "Hide JSON";
            }else{
                json.style.display = "none";
                toggle.innerHTML = "View JSON";
            }
        }


        function addInfo(){
            var output = document.getElementById("output");
            var username = getCookie("username");
            var password = document.getElementById("pass").value;
            var data = {"username": username, "password": password};
            var xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function(){
                if(this.readyState == 4 && this.status == 200){
                    output.innerHTML = "<button class='btn btn-secondary' id='toggle' onclick='javascript:toggleJSON();'>View JSON</button>";
                    output.innerHTML += "<br><div id='json' style='display: none;'></div>";
                    document.getElementById("json").innerHTML = "<br>" + this.responseText;
                    var raw_data = JSON.parse(this.responseText);
                    var tables = {};
                    for(let row of raw_data){
                        let key = String(Object.keys(row));
                        if(key in tables){
                            let html_row = document.createElement("tr");
                            for(col of Object.keys(row)){
                                let cell = document.createElement("td");
                                let cont = document.createTextNode(row[col]);
                                cell.appendChild(cont);
                                html_row.appendChild(cell);
                            }
                            tables[key].appendChild(html_row);
                        }else{
                            tables[key] = document.createElement("table");
                            let html_row = document.createElement("tr");
                            for(col of Object.keys(row)){
                                let cell = document.createElement("th");
                                let cont = document.createTextNode(col);
                                cell.appendChild(cont);
                                html_row.append(cell);
                            }
                            tables[key].appendChild(html_row);
                            let html_row2 = document.createElement("tr");
                            for(col of Object.keys(row)){
                                let cell = document.createElement("td");
                                let cont = document.createTextNode(row[col]);
                                cell.appendChild(cont);
                                html_row2.append(cell);
                            }
                            tables[key].appendChild(html_row2);
                        }
                    }
                    for(table of Object.keys(tables)){
                        output.appendChild(document.createElement("br"));
                        output.appendChild(tables[table]);
                    }
                }else if(this.readyState == 4 && this.status == 403){
                    output.innerHTML = "Password is incorrect.";
                }else if(this.readyState == 4){
                    output.innerHTML = "An unknown error occured.";
                }
            }
            xhttp.open("POST", "https://www.eigentrust.net:31415/gdpr_view", true);
            xhttp.setRequestHeader("Content-type", "application/json");
            xhttp.setRequestHeader("Accept", "text/plain");
            xhttp.send(JSON.stringify(data));
        }

        function addListeners(){
            var password = document.getElementById("pass")

            function click(event){
                if(event.key === "Enter"){
                    event.preventDefault();
                    document.getElementById("submit").click();
                }
            }

            password.addEventListener("keypress", click);
        }

    </script>
  </head>

  <body onload="javascript:addListeners();">
    <?php require("nav.php"); ?>
    <div class="container">
        <h1>GDPR View</h1>
        <br>
        Please enter your password:<br>
        <input type="password" id="pass"><br>
        <button style="margin:5px;" class="btn btn-primary" id="submit" onclick="javascript:addInfo();">Get GDPR Report</button>
        <p id="output"></p>
    </div>
  </body>
</html>
