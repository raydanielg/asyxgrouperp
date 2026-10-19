{{-- Loading Screen --}}
<div id="asyxLoadingScreen" style="position:fixed; inset:0; z-index:99999; background:#ffffff; display:flex; flex-direction:column; align-items:center; justify-content:center; transition: opacity 0.5s ease;">

  {{-- Round ring loader --}}
  <div class="ls-ring-wrap">
    <div class="ls-ring"></div>
    <div class="ls-ring ls-ring-2"></div>
    <div class="ls-dot"></div>
  </div>

</div>

<style>
.ls-ring-wrap {
  position: relative;
  width: 64px;
  height: 64px;
}
.ls-ring {
  position: absolute;
  inset: 0;
  border-radius: 50%;
  border: 3px solid transparent;
  border-top-color: #111827;
  border-right-color: #111827;
  animation: ls-spin 0.8s cubic-bezier(.5,.2,.5,.8) infinite;
}
.ls-ring-2 {
  inset: 8px;
  border-top-color: #F6891F;
  border-right-color: transparent;
  animation: ls-spin-rev 1.2s linear infinite;
}
.ls-dot {
  position: absolute;
  top: 50%;
  left: 50%;
  width: 8px;
  height: 8px;
  margin: -4px 0 0 -4px;
  border-radius: 50%;
  background: #111827;
  animation: ls-dotpulse 1s ease-in-out infinite;
}
@keyframes ls-spin {
  to { transform: rotate(360deg); }
}
@keyframes ls-spin-rev {
  to { transform: rotate(-360deg); }
}
@keyframes ls-dotpulse {
  0%, 100% { transform: scale(1); opacity: 0.9; }
  50% { transform: scale(0.6); opacity: 0.5; }
}
#asyxLoadingScreen.ls-hidden {
  opacity: 0;
  pointer-events: none;
}
</style>

<script>
(function() {
  var screen = document.getElementById('asyxLoadingScreen');
  if (!screen) return;

  function hideScreen() {
    screen.classList.add('ls-hidden');
    setTimeout(function() { if (screen.parentNode) screen.remove(); }, 600);
  }

  // If page already loaded, hide immediately
  if (document.readyState === 'complete') {
    setTimeout(hideScreen, 800);
  } else {
    window.addEventListener('load', function() {
      setTimeout(hideScreen, 800);
    });
  }

  // Fallback - always hide after 3s no matter what
  setTimeout(function() {
    if (document.getElementById('asyxLoadingScreen')) {
      hideScreen();
    }
  }, 3000);
})();
</script>
