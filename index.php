<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Mini Games Hub</title>
<style>
  body { font-family: Arial; margin: 0; background:#222; color:#fff; display:flex; flex-direction:column; align-items:center; }
  h1 { margin-top: 10px; }
  #buttons { margin: 10px; }
  button { margin: 5px; padding: 10px; cursor:pointer; border:none; border-radius:5px; background:#555; color:#fff; }
  #gameContainer { width: 100%; max-width: 400px; margin: 10px auto; position:relative; }
  canvas { width: 100%; border: 2px solid #444; background:#fff; display:block; }
  #status { color: lightgreen; text-align:center; margin-top:5px; }
</style>
</head>
<body>

<h1>Mini Games Hub</h1>
<div id="buttons">
  <button onclick="showGame('tic')">Tic Tac Toe</button>
  <button onclick="showGame('block')">Block Blast</button>
  <button onclick="showGame('cat')">Cat Running</button>
  <button onclick="showGame('hunter')">Hunter Assassin</button>
</div>
<div id="gameContainer">
  <canvas id="gameCanvas" width="400" height="600"></canvas>
  <div id="status"></div>
</div>

<script>
const canvas = document.getElementById('gameCanvas');
const ctx = canvas.getContext('2d');
const statusDiv = document.getElementById('status');
let currentGame = null;

// Clear canvas and status
function resetCanvas() {
  ctx.clearRect(0, 0, canvas.width, canvas.height);
  statusDiv.textContent = '';
  canvas.onclick = null;
}

// ------------------ Game Switching ------------------
function showGame(gameName) {
  resetCanvas();
  currentGame = gameName;
  if (gameName==='tic') initTicTacToe();
  if (gameName==='block') initBlockBlast();
  if (gameName==='cat') initCatRunning();
  if (gameName==='hunter') initHunterAssassin();
}

// ------------------ 1. Tic Tac Toe ------------------
function initTicTacToe() {
  let board = Array(9).fill('');
  let currentPlayer = 'X';
  let gameActive = true;

  function drawBoard() {
    ctx.clearRect(0,0,canvas.width,canvas.height);
    ctx.strokeStyle='black';
    ctx.font='60px Arial';
    for (let i=0; i<3; i++) {
      for (let j=0; j<3; j++) {
        ctx.strokeRect(j*133, i*133, 133, 133);
        const mark = board[i*3+j];
        if(mark) ctx.fillText(mark, j*133+40, i*133+80);
      }
    }
  }

  function checkWin() {
    const wins=[[0,1,2],[3,4,5],[6,7,8],[0,3,6],[1,4,7],[2,5,8],[0,4,8],[2,4,6]];
    return wins.some(w=> board[w[0]] && board[w[0]]===board[w[1]] && board[w[1]]===board[w[2]]);
  }

  function handleClick(e){
    if(!gameActive) return;
    const rect = canvas.getBoundingClientRect();
    const x=e.clientX-rect.left;
    const y=e.clientY-rect.top;
    const i=Math.floor(y/133);
    const j=Math.floor(x/133);
    const idx=i*3+j;
    if(board[idx]) return;
    board[idx]=currentPlayer;
    drawBoard();
    if(checkWin()){
      statusDiv.textContent=currentPlayer+' wins!';
      gameActive=false;
    } else if(board.every(c=>c)){
      statusDiv.textContent='Draw!';
      gameActive=false;
    } else {
      currentPlayer=currentPlayer==='X'?'O':'X';
      statusDiv.textContent='Player '+currentPlayer+' turn';
    }
  }

  canvas.onclick = handleClick;
  statusDiv.textContent='Player X turn';
  drawBoard();
}

// ------------------ 2. Block Blast ------------------
function initBlockBlast() {
  const rows=6, cols=6;
  const blockSize=60;
  const blocks=[];
  let score=0;

  for(let i=0;i<rows;i++){
    blocks[i]=[];
    for(let j=0;j<cols;j++){
      blocks[i][j] = ['#e74c3c','#3498db','#2ecc71','#f1c40f','#9b59b6'][Math.floor(Math.random()*5)];
    }
  }

  function drawBlocks(){
    ctx.clearRect(0,0,canvas.width,canvas.height);
    for(let i=0;i<rows;i++){
      for(let j=0;j<cols;j++){
        ctx.fillStyle=blocks[i][j];
        ctx.fillRect(j*blockSize, i*blockSize, blockSize, blockSize);
        ctx.strokeRect(j*blockSize,i*blockSize,blockSize,blockSize);
      }
    }
    ctx.fillStyle='black';
    ctx.font='20px Arial';
    ctx.fillText('Score: '+score,10,canvas.height-10);
  }

  canvas.onclick=function(e){
    const rect=canvas.getBoundingClientRect();
    const x=e.clientX-rect.left;
    const y=e.clientY-rect.top;
    const col=Math.floor(x/blockSize);
    const row=Math.floor(y/blockSize);
    if(row<rows && col<cols){
      blocks[row][col]=null;
      score++;
      drawBlocks();
      statusDiv.textContent='Blocks Clicked: '+score;
    }
  }

  drawBlocks();
}

// ------------------ 3. Cat Running ------------------
function initCatRunning() {
  let catX=50, catY=500, catW=50, catH=50;
  let gravity=2, jumpPower=-20;
  let velocity=0;
  const obstacles=[];
  let score=0;
  let gameOver=false;

  function spawnObstacle(){
    const height=Math.random()*100+30;
    obstacles.push({x:canvas.width, y:canvas.height-height, w:30, h:height});
  }

  function draw(){
    ctx.clearRect(0,0,canvas.width,canvas.height);
    // Cat
    ctx.fillStyle='orange';
    ctx.fillRect(catX,catY,catW,catH);
    // Obstacles
    ctx.fillStyle='green';
    for(let obs of obstacles){
      ctx.fillRect(obs.x, obs.y, obs.w, obs.h);
      obs.x -=5;
      // collision
      if(catX<obs.x+obs.w && catX+catW>obs.x && catY<obs.y+obs.h && catY+catH>obs.y){
        gameOver=true;
      }
    }
    // Score
    ctx.fillStyle='black';
    ctx.font='20px Arial';
    ctx.fillText('Score: '+score,10,30);

    velocity += gravity;
    catY += velocity;
    if(catY+catH>canvas.height) { catY=canvas.height-catH; velocity=0; }
    if(catY<0) { catY=0; velocity=0; }

    if(!gameOver) requestAnimationFrame(draw);
    else statusDiv.textContent='Game Over! Score: '+score;
  }

  canvas.onclick=function(){ velocity=jumpPower; }

  setInterval(()=>{
    if(!gameOver) { spawnObstacle(); score++; }
  },1500);

  draw();
}

// ------------------ 4. Hunter Assassin ------------------
function initHunterAssassin() {
  let bullets=[];
  let targets=[];
  let score=0;

  function spawnTarget(){
    targets.push({x:Math.random()*(canvas.width-40), y:Math.random()*200, w:40, h:40});
  }

  function draw(){
    ctx.clearRect(0,0,canvas.width,canvas.height);
    // bullets
    ctx.fillStyle='red';
    bullets.forEach(b=>{ctx.fillRect(b.x,b.y,b.w,b.h); b.y-=10;});
    bullets=bullets.filter(b=>b.y>0);
    // targets
    ctx.fillStyle='blue';
    targets.forEach(t=>{ctx.fillRect(t.x,t.y,t.w,t.h);});
    // collision
    for(let i=bullets.length-1;i>=0;i--){
      for(let j=targets.length-1;j>=0;j--){
        const b=bullets[i], t=targets[j];
        if(b.x<b.x+t.w && b.x+b.w>t.x && b.y<b.y+t.h && b.y+b.h>t.y){
          bullets.splice(i,1);
          targets.splice(j,1);
          score++;
          break;
        }
      }
    }

    ctx.fillStyle='black';
    ctx.font='20px Arial';
    ctx.fillText('Score: '+score,10,30);
    requestAnimationFrame(draw);
  }

  canvas.onclick=function(e){
    const rect=canvas.getBoundingClientRect();
    const x=e.clientX-rect.left;
    const y=e.clientY-rect.top;
    bullets.push({x:x-5,y:canvas.height-20,w:10,h:20});
  }

  setInterval(spawnTarget,1500);
  draw();
}

// Start default game
showGame('tic');
</script>

</body>
</html>