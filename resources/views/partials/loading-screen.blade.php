{{-- Loading Screen --}}
<div id="asyxLoadingScreen" style="position:fixed; inset:0; z-index:99999; background:#ffffff; display:flex; flex-direction:column; align-items:center; justify-content:center; transition: opacity 0.6s ease;">

  {{-- Logo --}}
  <div style="margin-bottom:32px; animation: ls-pulse 1.6s ease-in-out infinite;">
    <img src="{{ asset('asyxgrouplogo.png') }}" alt="ASYX Group" style="width:120px; height:auto; opacity:0.85;">
  </div>

  {{-- Loading bar --}}
  <div style="width:180px; height:3px; background:#f0f0f0; border-radius:3px; overflow:hidden;">
    <div style="height:100%; width:0%; background:linear-gradient(90deg, #5A0917, #F6891F, #5A0917); animation: ls-loadfill 1.6s cubic-bezier(.6,0,.2,1) forwards; border-radius:3px;"></div>
  </div>

</div>

<style>
@keyframes ls-loadfill {
  to { width: 100%; }
}
@keyframes ls-pulse {
  0%, 100% { opacity: 1; }
  50% { opacity: 0.5; }
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
    setTimeout(function() { if (screen.parentNode) screen.remove(); }, 700);
  }

  // If page already loaded, hide immediately
  if (document.readyState === 'complete') {
    setTimeout(hideScreen, 1200);
  } else {
    window.addEventListener('load', function() {
      setTimeout(hideScreen, 1200);
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
