/*based on http://mysite.verizon.net/res148h4j/javascript/script_exact_cubic.html#the%20source%20code*/
function bezierRoots(a, b, c, d)
{ 
    var A=b/a;
    var B=c/a;
    var C=d/a;
 
    var Q, R, D, S, T, Im;
 
    var Q = (3*B - Math.pow(A, 2))/9;
    var R = (9*A*B - 27*C - 2*Math.pow(A, 3))/54;
    var D = Math.pow(Q, 3) + Math.pow(R, 2);    // polynomial discriminant
 
    var t=[];
 
    if (D >= 0){                                 // complex or duplicate roots
        var S = Math.sign(R + Math.sqrt(D))*Math.pow(Math.abs(R + Math.sqrt(D)),(1/3));
        var T = Math.sign(R - Math.sqrt(D))*Math.pow(Math.abs(R - Math.sqrt(D)),(1/3));
 
        t[0] = -A/3 + (S + T);                    // real root
        t[1] = -A/3 - (S + T)/2;                  // real part of complex root
        t[2] = -A/3 - (S + T)/2;                  // real part of complex root
        Im = Math.abs(Math.sqrt(3)*(S - T)/2);    // complex part of root pair   
 
        //discard complex roots
        if (Im!=0){
            t[1]=-Infinity;
            t[2]=-Infinity;
        }
 
    }else{                                          // distinct real roots
        var th = Math.acos(R/Math.sqrt(-Math.pow(Q, 3)));
        t[0] = 2*Math.sqrt(-Q)*Math.cos(th/3) - A/3;
        t[1] = 2*Math.sqrt(-Q)*Math.cos((th + 2*Math.PI)/3) - A/3;
        t[2] = 2*Math.sqrt(-Q)*Math.cos((th + 4*Math.PI)/3) - A/3;
    }
 
    //discard out of spec roots
    for (var i=0;i<3;i++){
        if (t[i]<0 || t[i]>1.0){
			t[i]=-Infinity;
		}
	}
 
    t.sort(); //sort ascending, putting invalid roots (-Infinity) first
	t=t.slice(t.lastIndexOf(-Infinity)+1);//lop off invalid roots

    return t;
}