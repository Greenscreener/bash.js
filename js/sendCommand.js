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
        if (commandRunning == false) {
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
                case "passwd":
                    commandRunning = true;
                    echo("Changing password for " + activeUsername + ".\n");
                    echo("(current) UNIX password: ");
                    inputElements[0].type = "password";
                    commandRunningId = "passwd1";
                    break;
                default:
                    lastEcho(inputValueSplitted[0] + ": command not found");
                    return;
            }
        } else {
            window.globInputValue = inputValue;
            switch(commandRunningId) {
                case "passwd1":
                    var shaObj = new jsSHA("SHA-256","TEXT");
                    shaObj.update(globInputValue);
                    globInputValue = undefined;
                    window.oldPassword = shaObj.getHash("B64");
                    if (shaObj.getHash("B64") != findUser(activeUsername).password) {
                        echo("passwd: Authentication token manipulation error\n");
                        lastEcho("passwd: password unchanged");
                    } else {
                        echo("Enter new UNIX password: ");
                        inputElements[0].type = "password";
                        commandRunningId = "passwd2";
                    }
                    break;
                case "passwd2":
                    window.password1 = globInputValue;
                    globInputValue = undefined;
                    echo("Retype new UNIX password: ");
                    inputElements[0].type = "password";
                    commandRunningId = "passwd3";
                    break;
                case "passwd3":
                    var password2 = globInputValue;
                    globInputValue = undefined;
                    if (password1 != password2) {
                        echo("Sorry, passwords do not match\n");
                        echo("passwd: Authentication token manipulation error\n");
                        lastEcho("passwd: password unchanged");
                    } else {
                        var shaObj = new jsSHA("SHA-256","TEXT");
                        shaObj.update(password1);
                        if (oldPassword == shaObj.getHash("B64")) {
                            echo("Password unchanged\n");
                            echo("Enter new UNIX password: ");
                            inputElements[0].type = "password";
                            commandRunningId = "passwd2";
                        } else {
                            findUser(activeUsername).password = shaObj.getHash("B64");
                            lastEcho("passwd: password updated successfully");
                        }
                    }
                    break;
                default:
                    break;
            }
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
