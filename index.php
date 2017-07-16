<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>bash.js</title>
        <link rel="stylesheet" href="styles/style.css">
    </head>
    <body>
        <div class="wrapper" onclick="document.getElementsByClassName('inputbox')[0].focus(); ">
            <div class="bash" id="bash">
            </div>
        </div>
        <endora>
        <script type="text/javascript">
            var version = "Beta 0.1";
            var inputboxValue = "";
            var username = "";
            var machinename = "bashjs";
            var workingDirectory = "~";
            var loggedIn = false;
            function userPrefix() {
                return username + "@" + machinename + ":" + workingDirectory + "$";
            }
            function changeBottom() {
                var bashTextHeight = document.getElementById('bash').offsetHeight;
                if ((window.innerHeight - bashTextHeight) > 0) {
                    document.getElementById('bash').style.bottom = (window.innerHeight - bashTextHeight) + "px";
                } else {
                    document.getElementById('bash').style.bottom = "0px";
                }
            }
            function inputKeyDown(event) {
                var key = event.keyCode;
                if (key == 13) {
                    sendCommand();
                }
            }
            function sendCommand() {
                var inputValue = inputboxValue;
                inputboxValue = "";
                var inputElements = document.getElementsByClassName('inputbox');
                if (inputElements[0].type == "text") {
                    echo(inputValue + "<br>");
                } else {
                    echo("<br>")
                }
                if (loggedIn == true) {
                    inputValue = inputValue.trimLeft(" ");
                    if (inputValue.length == 0) {
                        lastEcho("");
                        return;
                    }
                    var inputValueSplitted = inputValue.split(" ");
                    switch (inputValueSplitted[0]) {
                        case "helloworld":
                            lastEcho("Hello World!")
                            return;
                        default:
                            lastEcho("Unknown command.");
                            return;
                    }
                } else {
                    if (inputValue == "") {
                        username = "";
                        inputElements[0].type = "text";
                        echo("login:");
                    } else {
                        if (username == "") {
                            username = inputValue;
                            echo("Password:");
                            inputElements[0].type = "password";
                        } else {
                            if (inputValue == "password") {
                                loggedIn = true;
                                inputElements[0].type = "text";
                                lastEcho("Welcome");
                            } else {
                                echo("Login incorrect<br>login:");
                                inputElements[0].type = "text";
                                username = "";
                            }
                        }
                    }
                }
            }
            function lastEcho(input) {
                echo(input);
                echo("<br>" + userPrefix());
            }
            function echo(input) {
                document.getElementById('bash').innerHTML+=input;
                var inputElements = document.getElementsByClassName('inputbox');
                for (var i = 0; i < inputElements.length; i++) {
                    //inputboxValue+=inputElements[i].value;
                    inputElements[i].parentNode.removeChild(inputElements[i]);
                }
                document.getElementById('bash').innerHTML+=" <input type='text' class='inputbox' onfocus='this.style.width = ((this.value.length + 1) * 8) + &quot;px&quot;;' onkeypress='this.style.width = ((this.value.length + 1) * 8) + &quot;px&quot;; inputboxValue = this.value;' value='" + inputboxValue + "' onkeydown='inputboxValue = this.value; inputKeyDown(event);'>";
                //document.getElementById('bash').innerHTML+=" <input type='text' class='inputbox' inputboxValue = this.value;' value='" + inputboxValue + "'>";
                inputElements[0].style.width = ((inputElements[0].value.length + 1) * 8) + "px";
                changeBottom();
                document.getElementsByClassName('inputbox')[0].focus();
            }
            changeBottom();
            echo("Bash.js " + version + " tty1 <br> <br> login:");

        </script>
    </body>
</html>
