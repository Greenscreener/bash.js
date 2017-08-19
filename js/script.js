//dojo.require("dojox.json.ref");
var version = "Beta";
var inputboxValue = "";
var activeUsername = "";
var loggedIn = false;
var machinename = "bashjs";
var workingDirectory = "";
var bashHistoryIndex = -1;
var commandRunning = false;
var commandRunningId = "";
if (localStorage.getItem("success") != "true") {
    var users = [
        {name:"root", password:"SBNJTRN+FjG7owHVrKtue7eqdM4RhdRWVl71HXN2d7I=", bashHistory:[]},
    ];
    var bashHistory = [];
    var fileTree = new Directory("");
    fileTree.addDirectory("home");
} else {
    var users = JSON.parse(localStorage.getItem("users"));
    var bashHistory = JSON.parse(localStorage.getItem("bashHistory"));
    var fileTree = Object.assign(new Directory(), JSON.retrocycle(JSON.parse(localStorage.getItem("fileTree"),jsonReviver)));
}
window.onunload = function () {
    localStorage.setItem("users", JSON.stringify(users));
    localStorage.setItem("bashHistory", JSON.stringify(bashHistory));
    localStorage.setItem("fileTree", JSON.stringify(JSON.decycle(fileTree)));
    localStorage.setItem("success","true");
}
changeBottom();
echo("Bash.js " + version + " tty1 <br><br>login: ");
