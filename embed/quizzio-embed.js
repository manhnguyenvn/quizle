var script = document.createElement("script");
script.innerHTML = "window.addEventListener('message', function(e) { document.getElementById('quizzio-iframe').style.height = e.data + 'px'; } , false);";
document.body.appendChild(script);