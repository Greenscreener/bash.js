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
        return Object.assign(new Directory, value);
    } else if (value.type == "TextFile") {
        return Object.assign(new TextFile, value);
    } else {
        return value;
    }
}
