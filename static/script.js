document.getElementById("body").addEventListener("click", function () {
    alert("AAAAA");

    var xhttp = new XMLHttpRequest();
    xhttp.open("GET", "best", true);
    xhttp.setRequestHeader('Content-type', 'application/json');
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            console.log(JSON.parse(this.responseText))
        }
    };
    xhttp.send();
});