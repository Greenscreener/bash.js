<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>bash.js</title>
        <link rel="stylesheet" href="styles/style.css">
    </head>
    <body>
        <div class="wrapper" onclick="document.getElementsByClassName('inputbox')[0].focus(); ">
            <div class="bash" id="bash"></div>
        </div>
        <endora>
        <script type="text/javascript">
            var version = "Beta 0.2";
            var inputboxValue = "";
            var username = "";
            var machinename = "bashjs";
            var workingDirectory = "";
            var loggedIn = false;
            var bashHistory = [];
            var bashHistoryIndex = -1;
            function userPrefix() {
                if (workingDirectory == ("/home/" + username)) {
                    return username + "@" + machinename + ":" + "~" + "$ ";
                } else {
                    return username + "@" + machinename + ":" + workingDirectory + "$ ";
                }
            }
            function changeBottom() {
                var bashTextHeight = document.getElementById('bash').offsetHeight;
                if ((window.innerHeight - bashTextHeight) > 0) {
                    document.getElementById('bash').style.bottom = (window.innerHeight - bashTextHeight) + "px";
                } else {
                    document.getElementById('bash').style.bottom = "0px";
                }
            }
            function Directory (name) {
                this.name = name;
                this.containsDirs = [];
                this.containsFiles = [];
                this.addFile = function (name) {
                    this.containsFiles[name] = "";
                }
                this.addDirectory = function (name) {
                    this.containsDirs[name] = (new Directory(name));
                }
                this.listContents = function () {
                    for (var i in this.containsDirs) {
                        echo("<br>" + this.containsDirs[i].name);
                    }
                    for (var i in this.containsFiles) {
                        echo("<br>" + this.containsFiles[i]);
                    }
                }
            }
            function historyScroll(direction) {
                if (loggedIn == true) {
                    if (bashHistoryIndex == -1) {
                        bashHistory[-1] = inputboxValue;
                    }
                    if (direction == "up" && bashHistoryIndex < (bashHistory.length -1)) {
                        bashHistoryIndex++;
                    } else if (direction == "down" && bashHistoryIndex != -1) {
                        bashHistoryIndex--;
                    }

                    document.getElementsByClassName('inputbox')[0].value = bashHistory[bashHistoryIndex];
                    inputboxValue = bashHistory[bashHistoryIndex];
                    var inputElements = document.getElementsByClassName('inputbox');
                    inputElements[0].style.width = ((inputElements[0].value.length + 1) * 8) + "px";
                    //console.log(bashHistoryIndex + ": " + bashHistory[bashHistoryIndex]);
                }
            }
            function inputKeyDown(event) {
                var key = event.keyCode;
                const downArrow = 40;
                const upArrow = 38;
                const enter = 13;
                switch (key) {
                    case enter:
                        sendCommand();
                        break;
                    case upArrow:
                        historyScroll("up");
                        break;
                    case downArrow:
                        historyScroll("down");
                        break;
                    default:
                        break;
                }

            }

            function sendCommand() {
                bashHistoryIndex = -1;
                var inputValue = inputboxValue;
                inputboxValue = "";
                var inputElements = document.getElementsByClassName('inputbox');
                if (inputElements[0].type == "text") {
                    echo(inputValue + "<br>");
                } else {
                    echo("<br>")
                }
                if (loggedIn == true) {
                    if (bashHistory[0] != inputValue) {
                        bashHistory.unshift(inputValue);
                    }
                    inputValue = inputValue.trimLeft(" ");
                    if (inputValue.length == 0) {
                        lastEcho("");
                        return;
                    }
                    var inputValueSplitted = inputValue.split(" ");
                    var inputParams = inputValue.replace(inputValueSplitted[0], "");
                    inputParams = inputParams.trimLeft(" ");
                    switch (inputValueSplitted[0]) {
                        case "helloworld":
                            lastEcho("Hello World!")
                            return;
                        case "echo":
                            lastEcho(inputParams);
                            return;
                        case "exit":
                            loggedIn = false;
                            username = "";
                            document.getElementById('bash').innerHTML = "";
                            bashHistory = [];
                            echo("Bash.js " + version + " tty1 <br><br>login: ");
                            return;
                        case "clear":
                            document.getElementById('bash').innerHTML = "";
                            lastEcho("");
                            return;
                        case "pwd":
                            lastEcho(workingDirectory);
                            return;
                        default:
                            lastEcho(inputValueSplitted[0] + ": command not found");
                            return;
                    }
                } else {
                    if (inputValue == "") {
                        username = "";
                        inputElements[0].type = "text";
                        echo("login: ");
                    } else {
                        if (username == "") {
                            username = inputValue;
                            echo("Password: ");
                            inputElements[0].type = "password";
                        } else {
                            if (inputValue == "password") {
                                loggedIn = true;
                                inputElements[0].type = "text";
                                lastEcho("Welcome, " + username + "!");
                                workingDirectory = "/home/" + username;
                                fileTree.containsDirs["home"].addDirectory(username);
                            } else {
                                echo("Login incorrect<br>login: ");
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
                document.getElementById('bash').innerHTML+="<input type='text' class='inputbox' onfocus='this.style.width = ((this.value.length + 1) * 8) + &quot;px&quot;;' onkeypress='this.style.width = ((this.value.length + 1) * 8) + &quot;px&quot;; inputboxValue = this.value;' value='" + inputboxValue + "' onkeydown='inputboxValue = this.value; inputKeyDown(event);'>";
                //document.getElementById('bash').innerHTML+=" <input type='text' class='inputbox' inputboxValue = this.value;' value='" + inputboxValue + "'>";
                inputElements[0].style.width = ((inputElements[0].value.length + 1) * 8) + "px";
                changeBottom();
                document.getElementsByClassName('inputbox')[0].focus();
            }
            var fileTree = new Directory("");
            fileTree.addDirectory("home");
            changeBottom();
            echo("Bash.js " + version + " tty1 <br><br>login: ");

        </script>
    </body>
</html>
