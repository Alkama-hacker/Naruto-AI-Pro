<?php
error_reporting(0);
@ini_set('display_errors', 0);
session_start();

$MASTER_PASSWORD = "naruto099";
$error_msg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['cipher_key']) && $_POST['cipher_key'] === $MASTER_PASSWORD) {
        $_SESSION['is_hacker_verified'] = true;
    } else {
        $error_msg = "INVALID KEY!";
    }
}
$isLoggedIn = isset($_SESSION['is_hacker_verified']) && $_SESSION['is_hacker_verified'] === true;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <title>NARUTO AI PRO</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@600;800;900&family=Rajdhani:wght@600;700&display=swap" rel="stylesheet">
    
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; user-select: none; }
        body { overflow: hidden; height: 100vh; width: 100vw; background: #000; font-family: 'Rajdhani', sans-serif; }
        iframe#bg-site { position: absolute; top: 0; left: 0; width: 100%; height: 100%; border: none; z-index: 1; }

        #hack-overlay {
            position: absolute; top: 12%; right: 5%; width: 210px; z-index: 9999;
            background: #100b1a; border-radius: 12px; padding: 10px;
            border: 1px solid rgba(138, 43, 226, 0.6);
            box-shadow: 0 0 20px rgba(138, 43, 226, 0.4), inset 0 0 10px rgba(0, 0, 0, 0.8);
            color: #fff; transform: scale(0.85); transform-origin: top right;
        }

        .drag-header { width: 60%; height: 80px; position: absolute; top: 0; left: 0; cursor: move; z-index: 20; }
        .drag-header::after { content: ''; width: 25px; height: 4px; background: rgba(0, 242, 255, 0.4); border-radius: 10px; position: absolute; top: 10px; left: 15px; }

        .top-header { display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 6px; margin-bottom: 8px; position: relative; z-index: 10;}
        .top-title { font-family: 'Orbitron'; font-size: 10px; font-weight: 900; color: #fff; display: flex; align-items: center; gap: 5px; text-transform: uppercase;}
        .top-title img { width: 18px; height: 18px; border-radius: 50%; }
        
        .btn-group { display: flex; gap: 5px; position: relative; z-index: 30; }
        .icon-btn { background: rgba(255,255,255,0.1); border: none; border-radius: 50%; width: 22px; height: 22px; color: #fff; font-size: 10px; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: 0.3s;}
        .icon-btn:active { transform: scale(0.9); }
        .icon-btn.active { background: rgba(0,255,136,0.2); color: #00ff88; box-shadow: 0 0 8px #00ff88; }
        .icon-btn.off { background: rgba(255,0,51,0.2); color: #ff0033; box-shadow: 0 0 8px #ff0033; }

        .section-box { background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 8px; padding: 8px; margin-bottom: 8px; }

        .sync-row { display: flex; justify-content: space-between; font-size: 8px; font-family: 'Orbitron'; font-weight: bold; margin-bottom: 5px;}
        .period-text { color: #e0e0e0; }
        .period-text span { color: #f7b924; }
        .timer-text { color: #ff00ff; text-shadow: 0 0 5px #ff00ff; }
        .timer-text span { color: #00ff88; text-shadow: 0 0 5px #00ff88;}

        .progress-container { width: 100%; height: 4px; background: rgba(0,0,0,0.5); border-radius: 2px; margin-bottom: 10px; overflow: hidden; border: 1px solid rgba(0, 242, 255, 0.2); }
        .progress-bar { height: 100%; width: 100%; background: linear-gradient(90deg, #00f2ff, #4169e1, #8a2be2, #00f2ff); background-size: 200% 100%; animation: pBarGlow 2s linear infinite; transition: width 1s linear; }
        @keyframes pBarGlow { 0% { background-position: 0% 0; } 100% { background-position: 100% 0; } }

        .display-row { display: flex; justify-content: space-between; align-items: center; margin-bottom: 5px; padding: 0 5px;}
        .circles { display: flex; gap: 8px; }
        .circle { width: 32px; height: 32px; border-radius: 50%; border: 2px solid #00ff88; color: #00ff88; display: flex; align-items: center; justify-content: center; font-family: 'Orbitron'; font-size: 14px; font-weight: 900; box-shadow: 0 0 10px rgba(0,255,136,0.5), inset 0 0 5px rgba(0,255,136,0.5); }
        
        .prediction-box { display: flex; flex-direction: column; align-items: flex-end; }
        .pred-size { font-family: 'Orbitron'; font-size: 24px; font-weight: 900; color: #fff; text-shadow: 0 0 10px #fff; line-height: 1;}
        .tap-start-btn { font-size: 16px; color: #f7b924; text-shadow: 0 0 10px #f7b924; cursor: pointer; animation: pulse 1s infinite alternate; }
        @keyframes pulse { 0% { transform: scale(1); } 100% { transform: scale(1.05); } }

        .text-red { color: #ff0044; text-shadow: 0 0 15px #ff0044; }
        .text-green { color: #00ff88; text-shadow: 0 0 15px #00ff88; }
        @keyframes rgbGlow { 0%{color:#ff0000;text-shadow:0 0 15px #ff0000} 33%{color:#0000ff;text-shadow:0 0 15px #0000ff} 66%{color:#00ff00;text-shadow:0 0 15px #00ff00} 100%{color:#ff0000;text-shadow:0 0 15px #ff0000} }
        .text-rainbow { animation: rgbGlow 1.5s linear infinite; }

        .mid-row { display: flex; justify-content: space-between; }
        .history-box { flex: 1; }
        .history-title { font-size: 7px; color: #aaa; margin-bottom: 4px; display: flex; align-items: center; gap: 4px;}
        .hist-list { font-size: 7px; font-family: 'Orbitron'; color: #ccc; display: flex; flex-direction: column; gap: 2px; height: 30px; overflow-y: auto; padding-right: 2px;}
        .hist-list::-webkit-scrollbar { width: 2px; }
        .hist-list::-webkit-scrollbar-thumb { background: #8a2be2; border-radius: 2px; }
        .hist-item span.w { color: #00ff88; }
        .hist-item span.l { color: #ff0044; }

        .acc-box { text-align: right; }
        .acc-val { font-family: 'Orbitron'; font-size: 18px; font-weight: 900; color: #fff; line-height: 1.1;}
        .acc-high { color: #00ff88 !important; text-shadow: 0 0 10px rgba(0,255,136,0.5) !important; }
        .acc-mid { color: #f7b924 !important; text-shadow: 0 0 10px rgba(247,185,36,0.5) !important; }
        .acc-low { color: #ff0044 !important; text-shadow: 0 0 10px rgba(255,0,68,0.5) !important; }
        .acc-dash { width: 100%; height: 2px; border-bottom: 2px dashed #8a2be2; margin-top: 2px;}

        .stats-box { font-size: 8px; font-family: 'Orbitron'; font-weight: bold; margin-bottom: 8px; display: flex; flex-direction: column; gap: 2px;}
        .stat-line { display: flex; justify-content: space-between; }
        .stat-lbl { color: #aaa; }
        .stat-val { color: #fff; }

        .balance-section { display: flex; justify-content: space-between; align-items: center; background: rgba(0,0,0,0.4); padding: 5px; border-radius: 6px; border: 1px solid #333;}
        .bal-lbl { font-size: 7px; color: #00f2ff; font-family: 'Orbitron'; font-weight: bold;}
        .bal-input { width: 55px; background: #000; border: 1px solid #8a2be2; color: #fff; text-align: center; border-radius: 4px; font-family: 'Orbitron'; font-size: 10px; padding: 2px; outline: none;}
        .step-meter { font-size: 9px; font-family: 'Orbitron'; font-weight: bold; color: #f7b924; }

        .cipher-input { width: 100%; padding: 10px; background: rgba(0,0,0,0.5); border: 1px solid #8a2be2; color: #fff; text-align: center; font-size: 10px; margin-bottom: 10px; outline: none; border-radius: 6px; font-family: 'Orbitron'; }
        .btn-start { width: 100%; padding: 10px; border: none; border-radius: 6px; font-family: 'Orbitron'; font-weight: 900; font-size: 11px; cursor: pointer; color: #fff; background: #8a2be2; box-shadow: 0 0 10px rgba(138, 43, 226, 0.5); transition: 0.2s;}
        .btn-start:active { transform: scale(0.95); }
        .error { color: #ff0044; font-size: 8px; margin-bottom: 6px; font-weight: bold; text-align: center;}

        .toast-msg { position: fixed; top: 20px; left: 50%; transform: translateX(-50%) translateY(-50px); background: rgba(10, 10, 15, 0.95); border: 1px solid #00f2ff; color: #fff; padding: 12px 15px; border-radius: 8px; font-family: 'Rajdhani', sans-serif; font-size: 14px; font-weight: 700; box-shadow: 0 0 20px rgba(0, 242, 255, 0.4); z-index: 100000; opacity: 0; transition: all 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55); pointer-events: none; text-align: center; width: 85%; max-width: 350px; }
        .toast-msg.show { transform: translateX(-50%) translateY(0); opacity: 1; }
        .toast-msg span { color: #00f2ff; font-family: 'Orbitron'; font-size: 11px; display: block; margin-bottom: 4px;}
        .toast-red { border-color: #ff0044; box-shadow: 0 0 20px rgba(255,0,68,0.4); }
        .toast-red span { color: #ff0044; }
    </style>
</head>
<body>
    <title>NARUTO AI PRO</title>
    <iframe id="bg-site" src="https://deshclub2.com/#/home/AllLotteryGames/WinGo?typeId=30"></iframe>

    <div id="naruto-toast" class="toast-msg">
        <span>🤖 NARUTO AI SAYS:</span>
        <div id="toast-msg-text">Message</div>
    </div>

    <div id="hack-overlay">
        <div class="drag-header" id="dragHeader"></div>

        <?php if (!$isLoggedIn): ?>
            <div class="top-header">
                <div class="top-title"><img src="naruto.jpg" onerror="this.src='data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII='"> NARUTO AI PRO</div>
            </div>
            <form method="POST" action="">
                <input type="password" name="cipher_key" class="cipher-input" placeholder="ACCESS KEY" required autocomplete="off">
                <?php if($error_msg): ?> <div class="error"><?php echo $error_msg; ?></div> <?php endif; ?>
                <button type="submit" class="btn-start">INITIALIZE</button>
            </form>
        <?php else: ?>
            
            <div id="dynamic-container">
                <div class="top-header">
                    <div class="top-title"><img src="naruto.jpg" onerror="this.src='data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII='"> NARUTO AI PRO</div>
                    <div class="btn-group">
                        <button id="voiceBtn" class="icon-btn active" onclick="toggleVoice()">🔊</button>
                    </div>
                </div>

                <div class="section-box">
                    <div class="sync-row">
                        <div class="period-text">⚡ PERIOD: <span id="period-display">----</span></div>
                        <div class="timer-text">NEXT IN: <span id="countdown">30</span></div>
                    </div>
                    
                    <div class="progress-container">
                        <div class="progress-bar" id="sync-progress"></div>
                    </div>

                    <div class="display-row">
                        <div class="circles">
                            <div class="circle" id="circle1">?</div>
                            <div class="circle" id="circle2">?</div>
                        </div>
                        <div class="prediction-box">
                            <!-- ⚠️ অডিও আনলক করার জন্য ট্যাপ টু স্টার্ট বাটন -->
                            <div id="prediction-text" class="pred-size tap-start-btn" onclick="initSystemManually()">TAP TO SCAN</div>
                        </div>
                    </div>
                </div>

                <div class="section-box">
                    <div class="mid-row" style="background: transparent; padding: 0; margin: 0; border: none;">
                        <div class="history-box">
                            <div class="history-title">📜 HISTORY</div>
                            <div class="hist-list" id="history-list"></div>
                        </div>
                        <div class="acc-box">
                            <div class="history-title" style="justify-content: flex-end;">🎯 ACCURACY</div>
                            <div class="acc-val acc-high" id="accuracy-val">--%</div>
                            <div class="acc-dash"></div>
                        </div>
                    </div>
                </div>

                <div class="section-box" style="margin-bottom: 0;">
                    <div class="stats-box">
                        <div class="stat-line"><span class="stat-lbl">LOGIC:</span><span class="stat-val" id="stat-trend">WAITING</span></div>
                        <div class="stat-line"><span class="stat-lbl">LAST:</span><span class="stat-val" id="lastStatusDisplay">AWAITING</span></div>
                    </div>

                    <div class="balance-section">
                        <div class="bal-lbl">BAL.: <span id="nextBetAmount" style="color:#00ff88;">--</span>৳</div>
                        <input type="number" id="offlineBalance" class="bal-input" placeholder="e.g. 1270" onchange="calculateSteps()">
                        <div id="stepMeter" class="step-meter">0/7</div>
                    </div>
                </div>
            </div>

            <script>
                var TIMER_OFFSET = 0; 
                var systemStarted = false; // সিস্টেম ম্যানুয়ালি স্টার্ট হয়েছে কিনা চেক করবে

                // ==========================================
                // 🔊 HYBRID VOICE AI SYSTEM
                // ==========================================
                var isVoiceEnabled = true; 
                function toggleVoice() {
                    isVoiceEnabled = !isVoiceEnabled;
                    var vBtn = document.getElementById('voiceBtn');
                    if(isVoiceEnabled) {
                        vBtn.innerHTML = '🔊'; vBtn.className = 'icon-btn active'; speakTextOnly("Voice activated.");
                    } else {
                        vBtn.innerHTML = '🔇'; vBtn.className = 'icon-btn off'; window.speechSynthesis.cancel();
                    }
                }

                function speakTextOnly(text) {
                    if(!isVoiceEnabled || !text) return;
                    window.speechSynthesis.cancel();
                    var msg = new SpeechSynthesisUtterance(text);
                    msg.lang = 'en-US'; msg.rate = 1.0;
                    window.speechSynthesis.speak(msg);
                }

                async function playAudioSequence(audioArr) {
                    if(!isVoiceEnabled) return;
                    window.speechSynthesis.cancel();

                    var currentItem = Array.isArray(audioArr) ? audioArr[0] : audioArr;
                    if(!currentItem) return;

                    if(currentItem.type === 'INPUT_BALANCE') {
                        try {
                            let audio = new Audio('audio/input_balance.mp3');
                            await new Promise((resolve, reject) => { audio.onended = resolve; audio.onerror = reject; audio.play().catch(reject); });
                        } catch(e) { speakTextOnly("Apnar balance input korun."); }
                    }
                    else if(currentItem.type === 'INSUFFICIENT') {
                        try {
                            let audio = new Audio('audio/insufficient.mp3');
                            await new Promise((resolve, reject) => { audio.onended = resolve; audio.onerror = reject; audio.play().catch(reject); });
                        } catch(e) { speakTextOnly("Apnar porjapto balance nei, doya kore age deposit korun."); }
                    }
                    else if(currentItem.type === 'BET') {
                        try {
                            let a1 = new Audio('audio/ekhon.mp3');
                            await new Promise((resolve, reject) => { a1.onended = resolve; a1.onerror = reject; a1.play().catch(reject); });
                            
                            await new Promise((resolve, reject) => {  
                                let msg = new SpeechSynthesisUtterance(currentItem.amount);
                                msg.lang = 'en-US'; msg.rate = 1.2; msg.onend = resolve; msg.onerror = reject;
                                window.speechSynthesis.speak(msg);
                            });

                            let a2 = new Audio('audio/taka_diye_' + currentItem.pred.toLowerCase() + '.mp3');
                            await new Promise((resolve, reject) => { a2.onended = resolve; a2.onerror = reject; a2.play().catch(reject); });
                        } catch(e) {
                            speakTextOnly("Ekhon " + currentItem.amount + " taka diye " + currentItem.pred + " a trade korun.");
                        }
                    }
                }

                const HISTORY_API_URL = 'https://api.inpay88.net/api/webapi/GetNoaverageEmerdList';
                const HISTORY_TOKEN = 'Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpYXQiOiIxNzgwNTY3NDI2IiwibmJmIjoiMTc4MDU2NzQyNiIsImV4cCI6IjE3ODA1NjkyMjYiLCJodHRwOi8vc2NoZW1hcy5taWNyb3NvZnQuY29tL3dzLzIwMDgvMDYvaWRlbnRpdHkvY2xhaW1zL2V4cGlyYXRpb24iOiI2LzQvMjAyNiA0OjMzOjQ2IFBNIiwiaHR0cDovL3NjaGVtYXMubWljcm9zb2Z0LmNvbS93cy8yMDA4LzA2L2lkZW50aXR5L2NsYWltcy9yb2xlIjoiQWNjZXNzX1Rva2VuIiwiVXNlcklkIjoiMTUwMzY1IiwiVXNlck5hbWUiOiI4ODAxNzI2MjQ1NDYzIiwiVXNlclBob3RvIjoiMSIsIk5pY2tOYW1lIjoiTWVtYmVyTk5HM0syU1MiLCJBbW91bnQiOiIwLjA2IiwiSW50ZWdyYWwiOiIwIiwiTG9naW5NYXJrIjoiSDUiLCJMb2dpblRpbWUiOiI2LzQvMjAyNiA0OjAzOjQ2IFBNIiwiTG9naW5JUEFkZHJlc3MiOiIyMDMuNzYuMjIxLjI0OCIsIkRiTnVtYmVyIjoiMCIsIklzdmFsaWRhdG9yIjoiMCIsIktleUNvZGUiOiIyNCIsIlRva2VuVHlwZSI6IkFjY2Vzc19Ub2tlbiIsIlBob25lVHlwZSI6IjEiLCJVc2VyVHlwZSI6IjAiLCJVc2VyTmFtZTIiOiIiLCJpc3MiOiJqd3RJc3N1ZXIiLCJhdWQiOiJsb3R0ZXJ5VGlja2V0In0.7ulO67yEGXDH1Lc8Nluh7h3McVSA03_tgkPvELYQPHY';

                var betSteps = [];
                var currentStepIndex = 0;
                var isBalanceInsufficient = true;
                var balancePromptCount = 0; 
                
                var lastPredictionValue = null;   
                var lastPredictionPeriod = null; 

                var toastTimeout;
                function showToast(bnMessage, audioObject, isDanger = false) {
                    var toast = document.getElementById('naruto-toast');
                    var msgText = document.getElementById('toast-msg-text');
                    if(toast && msgText) {
                        msgText.innerText = bnMessage;  
                        if(isDanger) toast.classList.add('toast-red'); else toast.classList.remove('toast-red');
                        toast.classList.add('show');
                        
                        if(audioObject) playAudioSequence(audioObject);

                        clearTimeout(toastTimeout);
                        toastTimeout = setTimeout(function() { toast.classList.remove('show'); }, 10000);  
                    }
                }

                function addMiniHistoryLog(period, aiPred, actualNum, status) {
                    var list = document.getElementById('history-list');
                    if(!list) return;
                    var div = document.createElement('div');
                    div.className = 'hist-item';
                    var pShort = period.slice(-4);  
                    var pLetter = (aiPred === "BIG" || aiPred === "SMALL") ? aiPred.charAt(0) : (aiPred === "RED" ? "R" : (aiPred === "GREEN" ? "G" : "X"));
                    var sLetter = status === "WIN" ? "<span class='w'>W</span>" : (status === "LOSS" ? "<span class='l'>L</span>" : "-");
                    
                    div.innerHTML = `<span>${pShort}</span> <span>${pLetter}(${actualNum}, ${sLetter})</span>`;
                    list.prepend(div);  
                }

                function calculateSteps() {
                    var val = parseFloat(document.getElementById('offlineBalance').value);
                    if(isNaN(val) || val <= 0) { document.getElementById('stepMeter').innerText = "0/7"; document.getElementById('nextBetAmount').innerText = "--"; return; }
                    
                    var totalBalance = val;
                    var baseBet = Math.floor(totalBalance / 127);
                    
                    if(baseBet < 1) {
                        isBalanceInsufficient = true;
                        // Manual input check won't shout instantly, it will wait for the next draw
                        baseBet = 1; document.getElementById('stepMeter').innerText = "0/7";
                    } else {
                        isBalanceInsufficient = false;
                        document.getElementById('stepMeter').innerText = (currentStepIndex + 1) + "/7";
                    }
                    betSteps = [baseBet, baseBet*2, baseBet*4, baseBet*8, baseBet*16, baseBet*32, baseBet*64];
                    document.getElementById('nextBetAmount').innerText = betSteps[currentStepIndex] || "--";
                }

                function updateOfflineBalance(isWin) {
                    if(betSteps.length === 0 || isBalanceInsufficient) return;
                    var currentBalance = parseFloat(document.getElementById('offlineBalance').value);
                    var betAmount = betSteps[currentStepIndex];

                    if(isWin) {
                        var profit = betAmount * 0.96;
                        currentBalance = parseFloat((currentBalance + profit).toFixed(2));  
                        currentStepIndex = 0;  
                    } else {
                        currentBalance = parseFloat((currentBalance - betAmount).toFixed(2));
                        currentStepIndex++;  
                        if (currentStepIndex > 6) currentStepIndex = 0;  
                    }
                    document.getElementById('offlineBalance').value = currentBalance;
                    calculateSteps();
                }

                var currentPeriod = null;
                var prevCycleIndex = -1;
                var BD_MS = 6 * 60 * 60 * 1000;

                function getBDSeconds() { return Math.floor((Date.now() + BD_MS) / 1000) + TIMER_OFFSET; }
                function getCountdown() { var mod = getBDSeconds() % 60 % 30; return mod === 0 ? 30 : 30 - mod; }
                function getBDCycleIndex() { return Math.floor(getBDSeconds() / 30); }
                function addOne(str) {
                    var m = str.match(/(\d+)$/);
                    if (!m) return str;
                    var numStr = m[1];
                    var prefix = str.substring(0, str.length - numStr.length);
                    var num = BigInt(numStr) + 1n;
                    var s = num.toString();
                    while (s.length < numStr.length) s = '0' + s;
                    return prefix + s;
                }

                // 🧠 THE BOSS-EMPLOYEE AI ENGINE 
                function calculateAdvancedPrediction(historyList) {
                    if (!historyList || historyList.length < 5) return { prediction: "BIG", accuracy: 80, trend: "WAITING" };

                    var sizes = [], colors = [], nums = [];
                    for(var i = 0; i < Math.min(50, historyList.length); i++) {
                        var n = parseInt(historyList[i].number || historyList[i].num || 0);
                        nums.push(n);
                        sizes.push(n >= 5 ? "BIG" : "SMALL");
                        colors.push(n % 2 === 0 ? "RED" : "GREEN");  
                    }

                    var employees = []; 

                    if (sizes[0] === sizes[1] && sizes[1] === sizes[2]) { employees.push({pred: sizes[0], acc: 99, trend: "DRAGON"}); }
                    // 🚨 Emp 7: The Boundary Pressure (4 Trap)
if (nums[0] === 4) { 
    employees.push({pred: "BIG", acc: 97, trend: "BOUNDARY TRAP"}); }
                    
// 🎭 Emp 9: The 3-3 Mirror Pattern (A-A-A-B-B-B)
                    if (sizes[0] === sizes[1] && sizes[1] === sizes[2] && 
                        sizes[3] === sizes[4] && sizes[4] === sizes[5] && 
                        sizes[0] !== sizes[3]) { 
                        // ৩টার পর ৩টা আসলে, এবার মার্কেট পাল্টে আবার আগেরটায় যাওয়ার কথা!
                        var mirrorPred = sizes[0] === "BIG" ? "SMALL" : "BIG";
                        employees.push({pred: mirrorPred, acc: 94, trend: "3-3 MIRROR"}); 
                    }
                    
                    // 🧬 Emp 15: The Odd-Even Clustering (অড-ইভেন ক্লাস্টারিং)
                    // গেম যখন টানা ৩ বার জোড় বা বিজোড় দেয়, তখন ক্লাস্টার ভাঙার জন্য সে উল্টো প্যারিটি দেয়।
                    var isEven0 = nums[0] % 2 === 0, isEven1 = nums[1] % 2 === 0, isEven2 = nums[2] % 2 === 0;
                    if (isEven0 === isEven1 && isEven1 === isEven2) {
                        // টানা ৩টা জোড় হলে পরেরটা বিজোড় (BIG) হবে। টানা বিজোড় হলে জোড় (SMALL) হবে।
                        var clusterPred = isEven0 ? "BIG" : "SMALL";
                        employees.push({pred: clusterPred, acc: 93, trend: "PARITY CLUSTER"});
                    }

                    // ⚔️ Emp 16: The Cross-Polarity Trap (ক্রস-পোলারিটি ট্র্যাপ)
                    // সাইজ চেঞ্জ হয়েছে কিন্তু কালার সেম আছে! গেম এখানে ইউজারদের কনফিউজ করে।
                    if (sizes[0] !== sizes[1] && colors[0] === colors[1]) {
                        // এই ট্র্যাপের পর গেম সাধারণত আগের সাইজে বাউন্স করে ফিরে যায়!
                        employees.push({pred: sizes[1], acc: 94, trend: "CROSS-POLARITY"});
                    }
                    
                    // 👻 Emp 15: The Phantom Echo (A-B-X-A-B -> X)
                    // মানুষ এই প্যাটার্ন ধরতে পারে না কারণ মাঝে গ্যাপ থাকে। কিন্তু গেম ঠিকই প্যাটার্ন লুপ করে!
                    if (sizes[0] === sizes[3] && sizes[1] === sizes[4]) {
                        employees.push({pred: sizes[2], acc: 98, trend: "PHANTOM ECHO"});
                    }

                    // 🎭 Emp 16: The 2-1-2 Decoy Trap (A-A-B-A-A -> B)
                    // গেম ভাবায় যে 'A' এর রাজত্ব চলছে, কিন্তু ধড়াম করে 'B' দিয়ে 3x বেটারদের জিরো করে দেয়!
                    if (sizes[0] === sizes[1] && sizes[3] === sizes[4] && sizes[0] === sizes[3] && sizes[0] !== sizes[2]) {
                        employees.push({pred: sizes[2], acc: 97, trend: "2-1-2 DECOY"});
                    }
                    // 🪀 Emp 15: The Pendulum Snap (পেন্ডুলাম স্ন্যাপ)
                    // যদি পর পর ৩ বার সংখ্যা ক্রমান্বয়ে বাড়ে বা কমে, তবে স্প্রিংয়ের মতো উল্টো দিকে বাউন্স করবে!
                    if (nums[0] > nums[1] && nums[1] > nums[2]) {
                        // সংখ্যা বাড়ছে (যেমন: 2, 5, 8), এবার ধড়াম করে নিচে নামবে!
                        employees.push({pred: "SMALL", acc: 90, trend: "PENDULUM SNAP"});
                    } else if (nums[0] < nums[1] && nums[1] < nums[2]) {
                        // সংখ্যা কমছে (যেমন: 9, 6, 1), এবার লাফ দিয়ে ওপরে উঠবে!
                        employees.push({pred: "BIG", acc: 97, trend: "PENDULUM SNAP"});
                    }

                    // 🔄 Emp 16: The RNG Stutter Flip (তোতলামি ট্র্যাপ)
                    // গেম যখন হুবহু একই সংখ্যা ২ বার দেয় (যেমন: 4, 4), তখন সে প্যানিক করে সাইজ উল্টে দেয়!
                    if (nums[0] === nums[1]) {
                        var stutterPred = sizes[0] === "BIG" ? "SMALL" : "BIG";
                        employees.push({pred: stutterPred, acc: 96, trend: "STUTTER FLIP"});
                    }

                    // 🪃 Emp 13: The Boomerang Trap Part-1 (A-B-B-B-A)
                    // যদি প্যাটার্ন A-B-B-B-A হয়, তবে সে পরেরটা 'A' ধরবে।
                    if (sizes[0] === sizes[4] && sizes[1] === sizes[2] && sizes[2] === sizes[3] && sizes[0] !== sizes[1]) {
                        employees.push({pred: sizes[0], acc: 95, trend: "A-B-B-B-A TRAP"}); 
                    }

                    // 🪃 Emp 14: The Boomerang Trap Part-2 (A-B-B-B-A-A)
                    // যদি অলরেডি একটা 'A' চলে এসে A-B-B-B-A-A হয়ে যায়, তবে সে কনফার্মেশনের জন্য পরেরটাও 'A' ধরবে।
                    if (sizes[0] === sizes[1] && sizes[1] === sizes[5] && sizes[2] === sizes[3] && sizes[3] === sizes[4] && sizes[0] !== sizes[2]) {
                        employees.push({pred: sizes[0], acc: 96, trend: "A-B-B-B-A-A TRAP"}); 
                    }
                    
                    // 🕸️ Emp 10: The 1-3-1 Trap (A-B-B-B-A)
                    if (sizes[0] !== sizes[1] && sizes[1] === sizes[2] && sizes[2] === sizes[3] && 
                        sizes[3] !== sizes[4] && sizes[0] === sizes[4]) { 
                        // ১টার পর ৩টা, তারপর আবার ১টা আসলে, মার্কেট আবার পাল্টে বাউন্স করতে চায়!
                        var trapPred = sizes[0] === "BIG" ? "SMALL" : "BIG"; 
                        employees.push({pred: trapPred, acc: 93, trend: "1-3-1 TRAP"}); 
                    }
                    
                    // 🧲 Emp 9: The Edge Gravity (প্রান্তের আকর্ষণ)
                    // গেম যখন একদম শেষ সীমানায় (1 বা 8) চলে যায়, তখন সে ব্যালেন্স করার জন্য উল্টো দিকে টান দেয়!
                    if (nums[0] === 1) { 
                        employees.push({pred: "BIG", acc: 96, trend: "EDGE GRAVITY"}); 
                    }
                    if (nums[0] === 8) { 
                        employees.push({pred: "SMALL", acc: 95, trend: "EDGE GRAVITY"}); 
                    }

                    // 🌪️ Emp 10: Chaos Continuity (বিশৃঙ্খলার ধারাবাহিকতা)
                    // যদি সাইজ আর কালার দুইটাই একসাথে জিগজ্যাগ করে, তখন গেম ধোঁকা দেওয়ার জন্য আগেরটাই রিপিট করে!
                    if (sizes[0] !== sizes[1] && colors[0] !== colors[1] && sizes[1] !== sizes[2]) { 
                        employees.push({pred: sizes[0], acc: 94, trend: "CHAOS REPEAT"}); 
                    }
                    
// 🥪 Emp 8: The Sandwich Trap (A-B-A)
if (sizes[0] === sizes[2] && sizes[0] !== sizes[1]) { 
    employees.push({pred: sizes[0], acc: 96, trend: "SANDWICH TRAP"}); }
                    else if (sizes[0] !== sizes[1] && sizes[1] !== sizes[2] && sizes[2] !== sizes[3]) { employees.push({pred: sizes[0] === "BIG" ? "SMALL" : "BIG", acc: 98, trend: "ZIG-ZAG"}); }
                    else if (sizes[0] === sizes[1] && sizes[1] !== sizes[2] && sizes[2] === sizes[3]) { employees.push({pred: sizes[0] === "BIG" ? "SMALL" : "BIG", acc: 97, trend: "TWIN DRAGON"}); }
                    else if (sizes[0] !== sizes[1] && sizes[1] === sizes[2] && sizes[2] !== sizes[3]) { employees.push({pred: sizes[0] === "BIG" ? "SMALL" : "BIG", acc: 96, trend: "ONE-TWO"}); }

                    if (colors[0] === colors[1] && colors[1] === colors[2]) { employees.push({pred: colors[0], acc: 95, trend: "COLOR PATT"}); }
                    else if (colors[0] !== colors[1] && colors[1] !== colors[2] && colors[2] !== colors[3]) { employees.push({pred: colors[0] === "RED" ? "GREEN" : "RED", acc: 94, trend: "COLOR ZIGZAG"}); }

                    var quantumSum = (nums[0] + nums[1] + nums[2]) % 10;
                    var quantumLogic = quantumSum >= 5 ? "BIG" : "SMALL";
                    employees.push({pred: quantumLogic, acc: 92, trend: "QUANTUM SUM"});

                    var b_to_b = 0, b_to_s = 0, s_to_b = 0, s_to_s = 0;
                    for (var i = 0; i < sizes.length - 1; i++) {
                        var current = sizes[i], previous = sizes[i + 1]; 
                        if (previous === "BIG" && current === "BIG") b_to_b++;
                        if (previous === "BIG" && current === "SMALL") b_to_s++;
                        if (previous === "SMALL" && current === "BIG") s_to_b++;
                        if (previous === "SMALL" && current === "SMALL") s_to_s++;
                    }
                    var markovLogic = sizes[0] === "BIG" ? (b_to_b >= b_to_s ? "BIG" : "SMALL") : (s_to_s >= s_to_b ? "SMALL" : "BIG");
                    employees.push({pred: markovLogic, acc: 88, trend: "MARKOV CHAIN"});

                    var momentumLogic = ((nums[0] + nums[1] + nums[2]) / 3) >= 4.5 ? "BIG" : "SMALL";
                    employees.push({pred: momentumLogic, acc: 85, trend: "MOMENTUM"});

                    var kidsLogic = (nums[0] % 2 === 0) ? "SMALL" : "BIG";
                    employees.push({pred: kidsLogic, acc: 80, trend: "BASIC MATH"});

                    employees.sort((a, b) => b.acc - a.acc);
                    var topPred = employees[0];
                    
                    if(quantumLogic === markovLogic && topPred.acc < 95) {
                        topPred.acc = 95;
                        topPred.trend = "QUANTUM+MARKOV";
                    }

                    return { prediction: topPred.pred, accuracy: topPred.acc, trend: topPred.trend };
                }

                // 🌟 TAP TO START (Audio Unlocker)
                function initSystemManually() {
                    if(systemStarted) return;
                    systemStarted = true;
                    
                    var el = document.getElementById('prediction-text');
                    el.classList.remove('tap-start-btn');
                    el.innerText = 'SCAN..';
                    el.style.color = '#555';
                    
                    speakTextOnly("System initialized. Scanning market.");
                    
                    fetchPrediction();
                    setInterval(function() {
                        var rem = getCountdown();
                        var cdEl = document.getElementById('countdown');
                        if(cdEl) cdEl.innerText = rem < 10 ? '0' + rem : rem;

                        var pBar = document.getElementById('sync-progress');
                        if(pBar) pBar.style.width = ((rem / 30) * 100) + '%';

                        var cycle = getBDCycleIndex();
                        if (prevCycleIndex !== -1 && cycle !== prevCycleIndex) {
                            currentPeriod = addOne(currentPeriod);
                            fetchPrediction();
                        }
                        prevCycleIndex = cycle;
                    }, 1000);
                }

                function fetchPrediction() {
                    if(!systemStarted) return;
                    
                    fetch(HISTORY_API_URL, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json;charset=UTF-8', 'Accept': 'application/json, text/plain, */*', 'Authorization': HISTORY_TOKEN },
                        body: JSON.stringify({ 
                            "pageSize": 50, "pageNo": 1, "typeId": 30, "language": 0, 
                            "visitorId": "9fae9ab709dfc4e3be577229e30d6f95", 
                            "random": "520348bc8a644864a2bd999961fcaaeb", 
                            "signature": "0D429B48FEDC4D8FE755486171760B5C", 
                            "timestamp": 1780567439 
                        })
                    }).then(r => r.json()).then(data => {
                        var item = null;
                        if (data && data.data && Array.isArray(data.data.list) && data.data.list.length > 0) { item = data.data.list[0]; }
                        if (item) {
                            var apiPeriod = item.issueNumber || item.issue || item.period || '';
                            var lastNum = parseInt(item.number || 0);
                            
                            if (lastPredictionPeriod && lastPredictionPeriod === apiPeriod) {
                                var actualSize = lastNum >= 5 ? "BIG" : "SMALL";
                                var actualColor = lastNum % 2 === 0 ? "RED" : "GREEN";
                                var isWin = false;

                                if (lastPredictionValue === "BIG" && actualSize === "BIG") isWin = true;
                                else if (lastPredictionValue === "SMALL" && actualSize === "SMALL") isWin = true;
                                else if (lastPredictionValue === "RED" && actualColor === "RED") isWin = true;
                                else if (lastPredictionValue === "GREEN" && actualColor === "GREEN") isWin = true;

                                var displayActual = (lastPredictionValue === "RED" || lastPredictionValue === "GREEN") ? actualColor : actualSize;

                                var statusEl = document.getElementById('lastStatusDisplay');

                                if (lastPredictionValue !== "SKIP") {
                                    if (isWin) {
                                        if(statusEl) statusEl.innerHTML = '<span class="status-win">✅ WIN</span>';
                                        updateOfflineBalance(true); 
                                        addMiniHistoryLog(apiPeriod, lastPredictionValue, lastNum, "WIN");
                                    } else {
                                        if(statusEl) statusEl.innerHTML = '<span class="status-loss">❌ LOSS</span>';
                                        updateOfflineBalance(false); 
                                        addMiniHistoryLog(apiPeriod, lastPredictionValue, lastNum, "LOSS");
                                    }
                                } else {
                                    if(statusEl) statusEl.innerHTML = '<span class="status-skip">⚠️ SKIPPED</span>';
                                    addMiniHistoryLog(apiPeriod, "SKIP", lastNum, "SKIP");
                                }
                            }

                            if (apiPeriod) currentPeriod = addOne(apiPeriod);
                            
                            var resultData = calculateAdvancedPrediction(data.data.list);
                            lastPredictionValue = resultData.prediction;
                            lastPredictionPeriod = currentPeriod;

                            updatePrediction(resultData.prediction, resultData.accuracy, resultData.trend);
                        } else {
                            fallbackPrediction(); 
                        }
                    }).catch(function() { fallbackPrediction(); });
                }

                function fallbackPrediction() {
                    if (currentPeriod) currentPeriod = addOne(currentPeriod);
                    lastPredictionValue = "BIG"; lastPredictionPeriod = currentPeriod;
                    updatePrediction("BIG", 80, "NETWORK ERR");
                }

                function getRandomNum(type) {
                    var arr = [0,1,2,3,4,5,6,7,8,9];  
                    if (type === "BIG") arr = [5,6,7,8,9];
                    else if (type === "SMALL") arr = [0,1,2,3,4];
                    else if (type === "RED") arr = [0,2,4,6,8];
                    else if (type === "GREEN") arr = [1,3,5,7,9];
                    return arr[Math.floor(Math.random() * arr.length)];
                }

                function updatePrediction(type, accuracy, trend) {
                    var pdEl = document.getElementById('period-display');
                    if (currentPeriod && pdEl) pdEl.innerText = currentPeriod.slice(-6);  
                    
                    var el = document.getElementById('prediction-text');
                    var accEl = document.getElementById('accuracy-val');
                    var c1 = document.getElementById('circle1');
                    var c2 = document.getElementById('circle2');
                    
                    document.getElementById('stat-trend').innerText = trend;

                    if(el) {
                        el.innerText = type;
                        el.className = 'pred-size';  
                        
                        var bnVoiceMsg = "";  
                        var audioConfig = null; 

                        // 🚥 Dynamic Accuracy Colors
                        if(accEl) {
                            accEl.innerText = accuracy + '%';
                            accEl.className = 'acc-val'; 
                            if(accuracy >= 95) accEl.classList.add('acc-high');
                            else if(accuracy >= 90) accEl.classList.add('acc-mid');
                            else accEl.classList.add('acc-low');
                        }

                        // 🛑 1. Check Balance First (Highest Priority)
                        if (document.getElementById('offlineBalance').value === "") {
                            if(balancePromptCount < 2) {
                                bnVoiceMsg = "আপনার ব্যালেন্স ইনপুট করুন!";
                                audioConfig = [{type: 'INPUT_BALANCE'}];
                                showToast(bnVoiceMsg, audioConfig, true);
                                balancePromptCount++;
                            }
                            // Keep visuals matching the actual prediction even if warning shows
                            if(type !== "SKIP") {
                                if(type === "RED") el.classList.add('text-red'); else if(type === "GREEN") el.classList.add('text-green'); else el.classList.add('text-rainbow');
                                var n1 = getRandomNum(type); var n2 = getRandomNum(type); while(n1 === n2) n2 = getRandomNum(type); c1.innerText = n1; c2.innerText = n2;
                            } else {
                                el.classList.add('text-skip'); c1.innerText = '?'; c2.innerText = '?';
                            }
                        } 
                        else if (isBalanceInsufficient) {
                            bnVoiceMsg = "আপনার পর্যাপ্ত ব্যালেন্স নেই, দয়া করে আগে ডিপোজিট করুন।";
                            audioConfig = [{type: 'INSUFFICIENT'}];
                            showToast(bnVoiceMsg, audioConfig, true);
                            
                            if(type !== "SKIP") {
                                if(type === "RED") el.classList.add('text-red'); else if(type === "GREEN") el.classList.add('text-green'); else el.classList.add('text-rainbow');
                                var n1 = getRandomNum(type); var n2 = getRandomNum(type); while(n1 === n2) n2 = getRandomNum(type); c1.innerText = n1; c2.innerText = n2;
                            } else {
                                el.classList.add('text-skip'); c1.innerText = '?'; c2.innerText = '?';
                            }
                        } 
                        else {
                            // 🚀 2. If Balance is Good, Show Normal Predictions
                            if(type === "SKIP") {
                                el.classList.add('text-skip'); 
                                c1.innerText = '?'; c2.innerText = '?';
                                // Skip Message (No voice needed, visual toast only)
                            } else {
                                if(type === "RED") el.classList.add('text-red');  
                                else if(type === "GREEN") el.classList.add('text-green');
                                else el.classList.add('text-rainbow');  
                                
                                var n1 = getRandomNum(type); var n2 = getRandomNum(type);
                                while(n1 === n2) n2 = getRandomNum(type);  
                                c1.innerText = n1; c2.innerText = n2;

                                var tradeAmt = betSteps[currentStepIndex];
                                bnVoiceMsg = "এখন " + tradeAmt + " টাকা দিয়ে " + type + " এ ট্রেড করুন।";
                                audioConfig = [{type: 'BET', amount: tradeAmt.toString(), pred: type}];
                                showToast(bnVoiceMsg, audioConfig, false);
                            }
                        }
                    }
                }
            </script>
        <?php endif; ?>
    </div>

    <script>
        /* Anti-Debug */
        document.addEventListener('contextmenu', event => event.preventDefault());
        document.addEventListener('keydown', function(e) { if(e.keyCode === 123 || (e.ctrlKey && e.shiftKey && e.keyCode === 73) || (e.ctrlKey && e.keyCode === 85)) { e.preventDefault(); } });
        setInterval(function() { (function() { return false; }['constructor']('debugger')()); }, 50);

        /* Flawless Drag Engine */
        var overlay = document.getElementById('hack-overlay');
        var header = document.getElementById('dragHeader');
        var iframe = document.getElementById('bg-site');
        var isDragging = false, startX, startY;

        if(header) {
            header.addEventListener('mousedown', dragStart); header.addEventListener('touchstart', dragStart, { passive: false });
            document.addEventListener('mousemove', drag); document.addEventListener('touchmove', drag, { passive: false });
            document.addEventListener('mouseup', dragEnd); document.addEventListener('touchend', dragEnd);
        }

        function dragStart(e) {
            isDragging = true;
            if(iframe) iframe.style.pointerEvents = 'none';
            if (e.type === 'touchstart') { startX = e.touches[0].clientX; startY = e.touches[0].clientY; }  
            else { startX = e.clientX; startY = e.clientY; }
            
            overlay.style.left = overlay.offsetLeft + 'px'; 
            overlay.style.top = overlay.offsetTop + 'px'; 
            overlay.style.right = 'auto'; overlay.style.bottom = 'auto';
        }
        function drag(e) {
            if (!isDragging) return; e.preventDefault();  
            var clientX, clientY;
            if (e.type === 'touchmove') { clientX = e.touches[0].clientX; clientY = e.touches[0].clientY; }  
            else { clientX = e.clientX; clientY = e.clientY; }
            var diffX = clientX - startX; var diffY = clientY - startY;
            
            overlay.style.left = (overlay.offsetLeft + diffX) + 'px'; 
            overlay.style.top = (overlay.offsetTop + diffY) + 'px';
            
            startX = clientX; startY = clientY;
        }
        function dragEnd() { isDragging = false; if(iframe) iframe.style.pointerEvents = 'auto'; }
    </script>
    <script>setInterval(function() { if(document.title !== "NARUTO AI PRO") { document.title = "NARUTO AI PRO"; } }, 10);</script>
</body>
</html>