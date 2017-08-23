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

function historyScroll(direction) {
    if (loggedIn == true) {
        if (bashHistoryIndex == -1) {
            findUser(activeUsername).bashHistory[-1] = inputboxValue;
        }
        if (direction == "up" && bashHistoryIndex < (findUser(activeUsername).bashHistory.length -1)) {
            bashHistoryIndex++;
        } else if (direction == "down" && bashHistoryIndex != -1) {
            bashHistoryIndex--;
        }

        document.getElementsByClassName('inputbox')[0].value = findUser(activeUsername).bashHistory[bashHistoryIndex];
        inputboxValue = findUser(activeUsername).bashHistory[bashHistoryIndex];
        var inputElements = document.getElementsByClassName('inputbox');
        inputElements[0].style.width = ((inputElements[0].value.length + 1) * 8) + "px";
        //console.log(bashHistoryIndex + ": " + findUser(activeUsername).bashHistory[bashHistoryIndex]);
    }
}
function inputKeyDown(event) {
    var key = event.keyCode;
    var downArrow = 40;
    var upArrow = 38;
    var enter = 13;
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

function lastEcho(input) {
    echo(input);
    commandRunning = false;
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
function jsonReviver(key,value) {
    if (value.type == "Directory") {
        return Object.assign(new Directory(), value);
    } else if (value.type == "TextFile") {
        return Object.assign(new TextFile(), value);
    } else {
        return value;
    }
}
function addUser(newUsername,newPassword) {
    if (findUser(newUsername)) {
        return false;
    }
    var shaObj = new jsSHA("SHA-256","TEXT");
    shaObj.update(newPassword);
    users.push({name:newUsername,password:shaObj.getHash("B64"),bashHistory:[]});
    return findUser(newUsername);
}
function loadGitHubAjax() {
  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            var githubCommits = JSON.parse(this.responseText);
            var latestCommitDate = new Date(githubCommits[0].commit.author.date);
            document.getElementById('welcomemessage').innerHTML = "Welcome, " + activeUsername + " to the JavaScript Bash Emulator. \n\nLatest commit on GitHub: \nHash: <a href='https://github.com/Greenscreener/bash.js/commit/" + githubCommits[0].sha + "'>" + githubCommits[0].sha + "</a>\nMessage: " + githubCommits[0].commit.message + "\nAuthor: " + githubCommits[0].commit.author.name + "\nTime: " + latestCommitDate.toUTCString();
            changeBottom();
        }
  };
  xhttp.open("GET", "https://api.github.com/repos/Greenscreener/bash.js/commits", true);
  xhttp.send();
}
function welcomeMessage() {
    if (document.getElementById('welcomeMessage') === undefined) document.getElementById('welcomemessage').parentNode.removeChild(document.getElementById('welcomemessage'));
    lastEcho("<p id='welcomemessage'></p>");
    loadGitHubAjax();
}
