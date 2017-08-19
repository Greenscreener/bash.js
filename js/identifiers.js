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
function findUser(username) {
    if (users.find(function (element) {return element.name == username;}) === undefined) {
        return false;
    } else {
        return users.find(function (element) {return element.name == username;});
    }
}
