SVG_URI = "http://www.w3.org/2000/svg";
esc=document.getElementById("escutcheon");
circ=document.createElementNS(SVG_URI, "circle");
circ.setAttribute("r", 10)
circ.setAttribute("cx", 150)
circ.setAttribute("cy", 150)
circ.setAttribute("fill", "white")
esc.appendChild(circ);
//esc.appendChild(circ);