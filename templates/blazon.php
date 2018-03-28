            <div style="text-align:center;overflow: none;" id="dotContainer">
<?php require "startingsvg.php"; ?>
                <div id="shieldCover" class="loadingScreen">
                    <h1 id="pleaseHeading">Please</h1>
                    <h2 id="waitHeading">Wait...</h2>
                </div>
            </div>
            <div id="bottomHalf">
                <div id="wrapper">
                    <div id="left">
                        <textarea id="blazonText" disabled></textarea>
                    </div>
                    <div id="middle"></div>
                    <div id="right">
                        <div id="shapeSelect">
                            <input type="radio" name="shape" value="B" onchange="changeShield(shieldB)" checked>Noble<br>
                            <input type="radio" name="shape" value="A" onchange="changeShield(ellipsePath)">Spiritual<br>
                        </div>
                        <div id="styleSelect">
                            <input type="radio" name="style" value="normal" onchange="changeHeraldryCSS('heraldry.css')" checked> Full colour<br>
                            <input type="radio" name="style" value="bw" onchange="changeHeraldryCSS('heraldry-bw.css')"> Line art<br>
                            <input type="radio" name="style" value="bw" onchange="changeHeraldryCSS('heraldry-not-shit.css')"> Not shit<br>
                        </div>
                    </div>
                </div>
                <div id="buttonContainer">
                    <button id="blazonButton" type="submit" onclick="drawUserBlazon()" disabled>Emblazon Arms</button>
                </div>
                <div id="syntax" style="display:none">
                    <h1>Syntax Tree</h1>
                    <div class="console" id="displayPara">
                        root<br>
                        -node1<br>
                        -node2<br>
                        --subnode1<br>
                        -node3<br>
                    </div>
                </div>
            </div>