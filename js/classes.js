function TextFile (name, parent) {
    this.name = name;
    this.parent = parent;
    this.content = "";
    this.type = "TextFile";
}
function Directory (name, parent) {
    this.name = name;
    this.parent = parent;
    this.containsDirs = {};
    this.containsFiles = {};
    this.type = "Directory";
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
