<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>bash.js</title>
        <link rel="stylesheet" href="styles/style.css">
        <link href="https://fonts.googleapis.com/css?family=Ubuntu+Mono:400,700" rel="stylesheet">
    </head>
    <body>
        <div class="wrapper" onclick="document.getElementsByClassName('inputbox')[0].focus(); ">
            <div class="bash" id="bash"></div>
        </div>
        <endora>
        <script src="https://rawgit.com/Caligatio/jsSHA/v2.3.1/src/sha256.js"></script>
        <script type="text/javascript">
            var version = "Beta";
            var inputboxValue = "";
            var activeUsername = "";
            var loggedIn = false;
            var machinename = "bashjs";
            var workingDirectory = "";
            var bashHistoryIndex = -1;
            if (localStorage.getItem("success") != "true") {
                var users = [
                    {name:"root", password:"SBNJTRN+FjG7owHVrKtue7eqdM4RhdRWVl71HXN2d7I=", bashHistory:[]},
                ];
                var bashHistory = [];
                var fileTree = new Directory("");
            } else {
                var users = JSON.parse(localStorage.getItem("users"));
                var bashHistory = JSON.parse(localStorage.getItem("bashHistory"));
                //var fileTree = new Directory("");
                //var fileTree = Object.assign({},fileTree,JSON.parse(localStorage.getItem("fileTree")));
            }
            window.onunload = function () {
                localStorage.setItem("users", JSON.stringify(users));
                localStorage.setItem("bashHistory", JSON.stringify(bashHistory));
                //localStorage.setItem("fileTree", JSON.stringify(fileTree));
                localStorage.setItem("success","true");
            }
            function userPrefix() {
                if (workingDirectory.printPath() == ("/home/" + activeUsername + "/")) {
                    return "<span class='userandmachine'>" + activeUsername + "@" + machinename + "</span>:<span class='workingdirectory'>" + "~" + "</span>$ ";
                } else {
                    return "<span class='userandmachine'>" + activeUsername + "@" + machinename + "</span>:<span class='workingdirectory'>" + workingDirectory.printPath().replace("/home/" + activeUsername, "~") + "</span>$ ";
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
            function TextFile (name, parent) {
                this.name = name;
                this.parent = parent;
                this.content = "";
            }
            function Directory (name, parent) {
                this.name = name;
                this.parent = parent;
                this.containsDirs = {};
                this.containsFiles = {};
                this.addFile = function (name) {
                    var newFile = new TextFile(name, this);
                    this.containsFiles[name] = newFile;
                    return newFile;
                }
                this.addDirectory = function (name) {
                    var newDir = new Directory(name, this);
                    newDir.containsDirs["."] = newDir;
                    newDir.containsDirs[".."] = newDir.parent;
                    this.containsDirs[name] = newDir;
                    return newDir;
                }
                this.listContents = function () {
                    var output = [];
                    for (var i in this.containsDirs) {
                        if (i[0] != ".") {output.push("<span class='workingdirectory'>" + this.containsDirs[i].name + "</span>");}
                    }
                    for (var i in this.containsFiles) {
                        if (i[0] != ".") {output.push(this.containsFiles[i].name);}
                    }
                    return output;
                }
                this.printPath = function (input) {
                    var pathSoFar = input || "";
                    // console.log(pathSoFar);
                    if (this.parent === undefined) {
                        return("/" + pathSoFar);

                    } else {
                        var returned = this.parent.printPath(this.name + "/" +  pathSoFar);
                        return returned;
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
                    if (bashHistory[0] != inputValue && inputValue.trimLeft(" ") != "") {
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
                            var inputParamsSplitted = inputParams.split(" ");
                            if (inputParamsSplitted.length > 1) {
                                if (inputParamsSplitted[inputParamsSplitted.length - 2] == ">>" || inputParamsSplitted[inputParamsSplitted.length - 2] == ">") {
                                    var targetFile = identifyFile(inputParamsSplitted[inputParamsSplitted.length - 1]);
                                    if (!targetFile) {
                                        if (identifyNonDir(inputParamsSplitted[inputParamsSplitted.length - 1])[0] == false) {
                                            lastEcho("bash: " + inputParamsSplitted[inputParamsSplitted.length - 1] + ": No such file or directory");
                                        } else {
                                            identifyNonDir(inputParamsSplitted[inputParamsSplitted.length - 1])[0].addFile(identifyNonDir(inputParamsSplitted[inputParamsSplitted.length - 1])[1]);
                                            targetFile = identifyFile(inputParamsSplitted[inputParamsSplitted.length - 1]);
                                        }

                                    }
                                    if (inputParamsSplitted[inputParamsSplitted.length - 2] == ">>") {
                                        targetFile.content+= ("\n" + inputParamsSplitted.slice(0,-2).join(" "));
                                    } else {
                                        targetFile.content = inputParamsSplitted.slice(0,-2).join(" ");
                                    }
                                    lastEcho("");
                                } else {
                                    lastEcho(inputParams);
                                }
                            } else {
                                lastEcho(inputParams);
                            }
                            return;
                        case "exit":
                            loggedIn = false;
                            activeUsername = "";
                            document.getElementById('bash').innerHTML = "";
                            echo("Bash.js " + version + " tty1 <br><br>login: ");
                            return;
                        case "clear":
                            document.getElementById('bash').innerHTML = "";
                            lastEcho("");
                            return;
                        case "pwd":
                            lastEcho(workingDirectory.printPath());
                            return;
                        case "ls":
                            if (identifyDir(inputParams.split(" ")[0]) == false) {
                                lastEcho("ls: cannot access '" + inputParams.split(" ")[0] + "': No such file or directory");
                            } else {
                                lastEcho(identifyDir(inputParams.split(" ")[0]).listContents().join("\n"));
                            }
                            break;
                        case "cd":
                            var targetDir = identifyDir(inputParams.split(" ")[0]);
                            if (inputParams.split(" ")[0] == "") {
                                workingDirectory = identifyDir("/home/" + activeUsername);
                                lastEcho("");
                            } else {
                                if (!targetDir) {
                                    lastEcho("bash: cd: " + inputParams.split(" ")[0] + ": No such file or directory");
                                } else {
                                    workingDirectory = targetDir;
                                    lastEcho("");
                                }
                            }
                            break;
                        case "mkdir":
                            if (identifyNonDir(inputParams.split(" ")[0])[0] == false) {
                                lastEcho("mkdir: cannot create directory '" + inputParams.split(" ")[0] + "': No such file or directory");
                            } else {
                                if (identifyNonDir(inputParams.split(" ")[0])[0].containsDirs[identifyNonDir(inputParams.split(" ")[0])[1]] === undefined) {
                                    identifyNonDir(inputParams.split(" ")[0])[0].addDirectory(identifyNonDir(inputParams.split(" ")[0])[1]);
                                    lastEcho("");
                                } else {
                                    lastEcho("mkdir: cannot create directory '" + inputParams.split(" ")[0] + "': File exists")
                                }
                            }
                            break;
                        case "touch":
                            if (identifyNonDir(inputParams.split(" ")[0])[0] == false) {
                                lastEcho("touch: cannot touch '" + inputParams.split(" ")[0] + "': No such file or directory");
                            } else {
                                if (identifyNonDir(inputParams.split(" ")[0])[0].containsFiles[identifyNonDir(inputParams.split(" ")[0])[1]] === undefined) {
                                    identifyNonDir(inputParams.split(" ")[0])[0].addFile(identifyNonDir(inputParams.split(" ")[0])[1]);
                                    lastEcho("");
                                } else {
                                    lastEcho("");
                                }
                            }
                            break;
                        case "cat":
                            if (identifyNonDir(inputParams.split(" ")[0])[0] == false) {
                                lastEcho("cat: " + inputParams.split(" ")[0] + ": No such file or directory");
                            } else if (identifyNonDir(inputParams.split(" ")[0])[0].containsFiles[identifyNonDir(inputParams.split(" ")[0])[1]] === undefined) {
                                lastEcho("cat: " + inputParams.split(" ")[0] + ": No such file or directory");
                            } else {
                                lastEcho(identifyNonDir(inputParams.split(" ")[0])[0].containsFiles[identifyNonDir(inputParams.split(" ")[0])[1]].content);
                            }
                            break;
                        case "nyancat":
                            lastEcho("<iframe src='https://www.youtube.com/embed/QH2-TGUlwu4?autoplay=1&controls=0&loop=1' frameBorder='0' width='200' height='200'>");
                            break;
                        default:
                            lastEcho(inputValueSplitted[0] + ": command not found");
                            return;
                    }
                } else {
                    if (inputValue == "") {
                        activeUsername = "";
                        inputElements[0].type = "text";
                        echo("login: ");
                    } else {
                        if (activeUsername == "") {
                            if (inputValue.indexOf("/") + 1) {
                                echo("Usernames can't contain slashes<br>login: ");
                                inputElements[0].type = "text";
                                activeUsername = "";
                            } else {
                                activeUsername = inputValue;
                                echo("Password: ");
                                inputElements[0].type = "password";
                            }
                        } else {
                            var shaObj = new jsSHA("SHA-256","TEXT");
                            shaObj.update(inputValue);
                            var passwordHash = shaObj.getHash("B64");
                            if (findUser(activeUsername)) {
                                if (findUser(activeUsername).password == passwordHash) {
                                    loggedIn = true;
                                    inputElements[0].type = "text";
                                    if (identifyDir("/home/" + activeUsername)) {
                                        workingDirectory = identifyDir("/home/" + activeUsername);
                                    } else {
                                        workingDirectory = fileTree.containsDirs["home"].addDirectory(activeUsername);
                                    }
                                    lastEcho("Welcome, " + activeUsername + "!");
                                } else {
                                    echo("Login incorrect<br>login: ");
                                    inputElements[0].type = "text";
                                    activeUsername = "";
                                }
                            } else {
                                echo("Login incorrect<br>login: ");
                                inputElements[0].type = "text";
                                activeUsername = "";
                            }
                        }
                    }
                }
            }
            function identifyDir(input) {
                if (input[0] == "/") {
                    var output = fileTree;
                    input = input.split("/");
                    input.shift();
                    if (input[input.length - 1] == "") {input.pop();}
                    for (var i in input) {
                        output = output.containsDirs[input[i]];
                    }
                    var outputDir = output;
                    if (outputDir === undefined) {
                        return false;
                    } else {
                        return outputDir;
                    }
                } else {
                    var output = workingDirectory;
                    input = input.split("/");
                    if (input[input.length - 1] == "") {input.pop();}
                    for (var i in input) {
                        output = output.containsDirs[input[i]];
                    }
                    var outputDir = output;
                    if (outputDir === undefined) {
                        return false;
                    } else {
                        return outputDir;
                    }
                }
            }
            function identifyNonDir(input) {
                var split = input.split("/");
                if (split[split.length - 1] == "") {split.pop();}
                var file = split[split.length - 1];
                var path = split.slice(0, -1);
                if (path.length == 0) {
                    path = "";
                } else if (path[0] == ""){
                    path = "/";
                } else {
                    path = path.join("/");
                }
                var toReturn = [identifyDir(path),file];
                return toReturn;
            }
            function identifyFile(input) {
                if (identifyNonDir(input)[0].containsFiles[identifyNonDir(input)[1]] === undefined) {
                    return false;
                } else {
                    return identifyNonDir(input)[0].containsFiles[identifyNonDir(input)[1]];
                }
            }
            function lastEcho(input) {
                echo(input);
                if (input == "") {
                    echo(userPrefix());
                } else {
                    echo("<br>" + userPrefix());
                }
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
            function findUser(username) {
                if (users.find(function (element) {return element.name == username;}) === undefined) {
                    return false;
                } else {
                    return users.find(function (element) {return element.name == username;});
                }
            }
            fileTree.addDirectory("home");
            changeBottom();
            echo("Bash.js " + version + " tty1 <br><br>login: ");

        </script>
    </body>
</html>
